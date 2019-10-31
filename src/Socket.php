<?php

namespace gipfl\Protocol\Snmp;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\DataType\DataTypeContextSpecific;
use React\EventLoop\LoopInterface;
use React\Datagram\Factory as UdpFactory;
use React\Datagram\Socket as UdpSocket;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;

class Socket implements EventEmitterInterface
{
    use EventEmitterTrait;

    /** @var LoopInterface */
    protected $loop;

    protected $ip;

    protected $port;

    /** @var UdpFactory */
    protected $factory;

    /** @var UdpSocket */
    protected $socket;

    /** @var Deferred[] */
    protected $pendingRequests = [];

    /** @var array */
    protected $pendingRequestOids = [];

    protected $timers = [];

    public function __construct($ip = '0.0.0.0', $port = 1161)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    public function hasPendingRequests()
    {
        return ! empty($this->pendingRequests);
    }

    public function run(LoopInterface $loop)
    {
        $this->loop = $loop;
        // $resolver --> React\Dns\Resolver\Resolver
        $this->factory = new UdpFactory($this->loop/*, $resolver */);

        return $this->factory->createServer(sprintf('%s:%d', $this->ip, $this->port))
            ->then(function (UdpSocket $socket) {
                $this->socket = $socket;
                $socket->on('message', function ($data, $peer, UdpSocket $socket) {
                    $this->handleData($data, $peer, $socket);
                });
            });
    }

    public function get($oids, $target, $community)
    {
        $id = $this->nextRequestId();
        $varBinds = $this->prepareAndScheduleOids($id, $oids);
        $request = new SnmpV2Message($community, new GetRequest($varBinds, $id));

        return $this->send($request, $target);
    }

    public function getNext($oid, $ip, $community)
    {
        $id = $this->nextRequestId();
        $varBinds = $this->prepareAndScheduleOids($id, [$oid => null]);
        $request = new SnmpV2Message($community, new GetNextRequest($varBinds, $id));

        return $this->send($request, $ip);
    }

    public function getBulk($oid, $target, $community, $maxRepetitions = 10)
    {
        $id = $this->nextRequestId();
        $varBinds = $this->prepareAndScheduleOids($id, [$oid => null]);
        $request = new SnmpV2Message($community, new GetBulkRequest($varBinds, $id, $maxRepetitions));

        return $this->send($request, $target);
    }

    public function walk($oid, $target, $community, $limit = null, $nextOid = null)
    {
        $walk = new Walk($this, $this->loop, $limit);
        if ($nextOid !== null) {
            $walk->setNextOid($nextOid);
        }

        return $walk->walk($oid, $target, $community);
    }

    public function walkBulk($oid, $ip, $community, $maxRepetitions = 10)
    {
        // TODO: Multiple OIDs
        $results = [];
        $deferred = new Deferred();
        $error = function ($reason) use ($deferred) {
            $deferred->reject($reason);
        };
        $handle = function ($result) use (
            $oid,
            $ip,
            $community,
            $maxRepetitions,
            $error,
            &$results,
            $deferred,
            &$handle
        ) {
            if (empty($result)) {
                $deferred->resolve($results);

                return;
            }
            $newOid = null;

            /** @var DataType $value */
            foreach ($result as $newOid => $value) {
                if (substr($newOid, 0, strlen($oid)) === $oid) {
                    $results[$newOid] = $value;
                } else {
                    $deferred->resolve($results);

                    return;
                }
            }

            $this->getBulk($newOid, $ip, $community, $maxRepetitions)
                ->then($handle)
                ->otherwise($error);
        };
        $this->getBulk($oid, $ip, $community, $maxRepetitions)
            ->then($handle)
            ->otherwise($error);

        return $deferred->promise();
    }

    public function sendTrap(SnmpMessage $trap, $destination)
    {
        if (strpos($destination, ':') === false) {
            $destination .= ':162';
        }

        return $this->send($trap, $destination);
    }

    protected function send(SnmpMessage $message, $destination)
    {
        $pdu = $message->getPdu();
        $wantsResponse = $pdu->wantsResponse();
        if ($wantsResponse) {
            $deferred = new Deferred();
            $id = $pdu->getRequestId();
            $this->pendingRequests[$id] = $deferred;
            $this->scheduleTimeout($id);
            $result = $deferred->promise();
        } else {
            $result = new FulfilledPromise();
        }

        if (strpos($destination, ':') === false) {
            $destination .= ':161';
        }
        $this->socket->send($message->toBinary(), $destination);

        return $result;
    }

    protected function prepareAndScheduleOids($id, $oids)
    {
        $this->pendingRequestOids[$id] = $oids;
        $binds = [];
        foreach ($oids as $oid => $target) {
            $binds[] = new VarBind($oid);
        }
        $varBinds = new VarBinds(...$binds);

        return $varBinds;
    }

    protected function scheduleTimeout($id, $timeout = 5)
    {
        $this->timers[$id] = $this->loop->addTimer($timeout, function () use ($id) {
            if (isset($this->pendingRequests[$id])) {
                $deferred = $this->pendingRequests[$id];
                unset($this->pendingRequests[$id]);
                unset($this->pendingRequestOids[$id]);
                unset($this->timers[$id]);
                $deferred->reject('Timeout'); // TODO: ErrorStatus, Exception?
            }
        });
    }

    protected function clearPendingRequest($id)
    {
        unset($this->pendingRequests[$id]);
        unset($this->pendingRequestOids[$id]);
        $this->loop->cancelTimer($this->timers[$id]);
        unset($this->timers[$id]);
    }

    protected function handleData($data, $peer, UdpSocket $socket)
    {
        // TODO: Logger::debug("Got message from $peer");
        $message = SnmpMessage::fromBinary($data);
        $pdu = $message->getPdu();

        if ($pdu instanceof TrapV2) {
            $this->emit('trap', [$message, $peer]);
            return;
        }
        $requestId = $pdu->getRequestId();
        if (isset($this->pendingRequests[$requestId])) {
            $deferred = $this->pendingRequests[$requestId];
            $oids = $this->pendingRequestOids[$requestId];
            $this->clearPendingRequest($requestId);
            if ($pdu->isError()) {
                // TODO: get errorStatus/errorIndex
                $deferred->reject('ERROR (TODO: get errorStatus/errorIndex)');
            } else {
                $result = [];
                /** @var VarBind $varBind */
                foreach ($pdu->getVarBinds()->iterate() as $varBind) {
                    $oid = $varBind->getOid();
                    if (isset($oids[$oid])) {
                        $result[$oids[$oid]] = $varBind->getValue();
                        unset($oids[$oid]);
                    } else {
                        $result[$oid] = $varBind->getValue();
                    }
                }
                foreach ($oids as $missing) {
                    if ($missing !== null) {
                        $result[$missing] = DataTypeContextSpecific::noSuchObject();
                    }
                }
                $deferred->resolve($result);
            }
        } else {
            // TODO: Logger::debug("Ignoring response for unknown requestId=$requestId");
        }
    }

    protected function nextRequestId()
    {
        $id = null;

        while (true) {
            $id = rand(1, 1000000000);
            if (! isset($this->pendingRequests[$id])) {
                break;
            }
        }

        return $id;
    }
}
