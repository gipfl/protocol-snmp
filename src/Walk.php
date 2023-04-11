<?php

namespace gipfl\Protocol\Snmp;

use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\DataType\DataTypeContextSpecific;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\ExtendedPromiseInterface;

class Walk
{
    protected Deferred $deferred;
    protected string $target;
    protected string $community;
    /** @var array<string, array{'type': string, 'value': mixed}> */
    protected array $results;
    protected string $baseOid;
    protected ?string $nextOid = null;
    protected bool $getBulk = true; // TODO: parameterize, false for v1

    public function __construct(
        protected readonly Socket $socket,
        protected ?int $limit = null,
    ) {
    }

    public function setNextOid(string $nextOid): void
    {
        $this->nextOid = $nextOid;
    }

    public function walk(
        string $oid,
        string $target,
        #[\SensitiveParameter] string $community
    ): ExtendedPromiseInterface {
        $this->results = [];
        $this->baseOid = $oid;
        if ($this->nextOid === null) {
            $this->nextOid = $oid;
        }
        $this->target = $target;
        $this->community = $community;
        // TODO: Multiple OIDs
        $this->deferred = new Deferred();
        Loop::futureTick(function () {
            $this->next();
        });

        // TODO: check whether cancel() really stops
        $promise = $this->deferred->promise();
        assert($promise instanceof ExtendedPromiseInterface);

        return $promise;
    }

    protected function next(): void
    {
        // TODO: Align max-repetitions with limit, try to not fetch more than required,
        //       and to avoid useless queries. Like: if we fetch 21 per default (for the
        //       "more" link), it would be a waste of roundtrips to ask for 20 twice
        if (!$this->nextOid) {
            throw new \LogicException('Running next() before $nextOid has been set');
        }

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
                [$this->nextOid],
                $this->target,
                $this->community
            );
        }
        // TODO: might be changed to:
        // $promise->then([$this, 'handleResult'], [$this, 'resolve']);
        $promise->then(function ($result) {
            $this->handleResult($result);
        }, function ($e = null) {
            var_dump($e);
            $this->resolve();
        });
    }

    protected function resolve(): void
    {
        $this->deferred->resolve($this->results);
    }

    protected function handleResult($result): void
    {
        $oid = $this->baseOid;
        /** @var DataType $value */
        foreach ($result as $newOid => $value) {
            if (
                ! str_starts_with($newOid, $this->baseOid) // Other prefix
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
            Loop::futureTick(function () {
                $this->next();
            });
        } else {
            $this->resolve();
        }
    }
}
