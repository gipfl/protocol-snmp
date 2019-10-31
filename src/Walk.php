<?php

namespace gipfl\Protocol\Snmp;

use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\DataType\DataTypeContextSpecific;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

class Walk
{
    protected $socket;

    protected $target;

    protected $community;

    protected $results = [];

    /** @var Deferred */
    protected $deferred;

    /** @var LoopInterface */
    protected $loop;

    protected $baseOid;

    protected $nextOid;

    protected $limit;

    /** @var bool TODO: parameterize, false for v1 */
    protected $getBulk = true;

    public function __construct(Socket $socket, LoopInterface $loop, $limit = null)
    {
        $this->socket = $socket;
        $this->loop = $loop;
        $this->limit = $limit;
    }

    public function setNextOid($nextOid)
    {
        $this->nextOid = $nextOid;

        return $this;
    }

    public function walk($oid, $target, $community)
    {
        $this->baseOid = $oid;
        if ($this->nextOid === null) {
            $this->nextOid = $oid;
        }
        $this->target = $target;
        $this->community = $community;
        // TODO: Multiple OIDs
        $this->deferred = new Deferred();
        $this->loop->futureTick(function () {
            $this->next();
        });

        // TODO: check whether cancel() really stops
        return $this->deferred->promise();
    }

    protected function next()
    {
        // TODO: Align max-repetitions with limit, try to not fetch more than required,
        //       and to avoid useless queries. Like: if we fetch 21 per default (for the
        //       "more" link), it would be a waste of roundtrips to ask for 20 twice

        if ($this->getBulk) {
            $maxLimit = 16;
            if ($this->limit === null) {
                $maxRepetitions = $maxLimit;
            } else {
                $maxRepetitions = min($this->limit, $maxLimit);
            }
            $promise = $this->socket->getBulk(
                $this->nextOid,
                $this->target,
                $this->community,
                $maxRepetitions
            );
        } else {
            $promise = $this->socket->getNext(
                $this->nextOid,
                $this->target,
                $this->community
            );
        }
        // TODO: evtl change to:
        // $promise->then([$this, 'handleResult'], [$this, 'resolve']);
        $promise->then(function ($result) {
            $this->handleResult($result);
        })->otherwise(function ($e = null) {
            var_dump($e);
            $this->resolve();
        });
    }

    protected function resolve()
    {
        $this->deferred->resolve($this->results);
    }

    protected function handleResult($result)
    {
        /** @var DataType $value */
        $oid = $this->baseOid;
        foreach ($result as $newOid => $value) {
            if (! $this->hasPrefix($newOid, $this->baseOid) // Other prefix
                || ($value instanceof DataTypeContextSpecific
                && $value->getTag() === DataTypeContextSpecific::END_OF_MIB_VIEW) // End Of MIB
            ) {
                $this->resolve();
                return;
            }

            if ($newOid === $oid) {
                if (! isset($this->results[$newOid])) {
                    // Keep the value in case we started here
                    $this->results[$newOid] = $value;
                }
                $this->resolve();
                return;
            }

            $this->nextOid = $newOid;
            $this->results[$newOid] = $value;
        }

        if ($this->limit === null || count($this->results) < $this->limit) {
            $this->loop->futureTick(function () {
                $this->next();
            });
        } else {
            $this->resolve();
            return;
        }
    }

    protected function hasPrefix($oid, $prefix)
    {
        return \substr($oid, 0, \strlen($prefix)) === $prefix;
    }

    public function __destruct()
    {
        $this->socket = null;
    }
}
