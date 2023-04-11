<?php

namespace gipfl\Protocol\Snmp;

use gipfl\Protocol\Snmp\DataType\DataType;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\ExtendedPromiseInterface;

use function ltrim;
use function strlen;
use function substr;

class FetchTable
{
    protected Deferred $deferred;

    protected array $results;

    /** @var array<int|string, string> */
    protected array $pendingColumns;

    /** @var array<int|string, string> */
    protected array $columns;
    protected SocketAddress $target;
    protected string $community;
    protected string $baseOid;
    protected string $currentPrefix;
    protected string $currentColumn;

    public function __construct(
        protected Socket $socket,
        protected ?int $limit = null
    ) {
    }

    /**
     * @param array<int|string, string> $columns
     */
    public function fetchTable(
        string $oid,
        array $columns,
        SocketAddress $target,
        string $community
    ): ExtendedPromiseInterface {
        $this->results = [];
        $this->baseOid = $oid;
        $this->target = $target;
        $this->community = $community;
        $this->columns = $this->pendingColumns = $columns;

        $this->deferred = new Deferred();
        Loop::futureTick(function () {
            $this->next();
        });

        $promise = $this->deferred->promise();
        assert($promise instanceof ExtendedPromiseInterface);

        return $promise;
    }

    protected function next(): void
    {
        if (empty($this->pendingColumns)) {
            throw new \LogicException('Cannot call next() on empty pending columns');
        }
        $column = array_shift($this->pendingColumns);
        $this->currentColumn = $column;
        $this->currentPrefix = $this->baseOid . '.' . $column;
        $this->fetchColumn($column)
            ->then(function ($result) {
                $this->handleResult($result);
            }, function ($e = null) {
                var_dump($e);
                $this->resolve();
            });
    }

    protected function handleResult($result): void
    {
        /** @var DataType $value */
        foreach ($result as $oid => $value) {
            [$idx, $key] = $this->splitAtFirstDot($this->stripPrefix($oid));
            // Dropping 1.
            [$idx, $key] = $this->splitAtFirstDot($key);
            // Now idx is the column. We don't care, as we already have it in currentColummn
            $this->results[$key][$this->currentColumn] = $value->jsonSerialize();
        }

        if (empty($this->pendingColumns)) {
            $this->resolve();
        } else {
            Loop::futureTick(function () {
                $this->next();
            });
        }
    }

    /**
     * @param string $oid
     * @return array{0: string, 1: string}
     */
    protected function splitAtFirstDot(string $oid): array
    {
        $dot = strpos($oid, '.');
        if ($dot === false) {
            throw new \InvalidArgumentException("$oid has no dot");
        }

        return [
            substr($oid, 0, $dot),
            substr($oid, $dot + 1),
        ];
    }

    protected function hasPrefix(string $oid, string $prefix): bool
    {
        return str_starts_with($oid, $prefix);
    }

    protected function stripPrefix(string $oid, ?string $prefix = null): string
    {
        if ($prefix === null) {
            $prefix = $this->baseOid;
        }

        if (str_starts_with($oid, $prefix)) {
            $oid = substr($oid, strlen($prefix));
        }

        return ltrim($oid, '.');
    }

    protected function fetchIndex(): ExtendedPromiseInterface
    {
        return $this->fetchColumn('1.1');
    }

    protected function fetchColumn(string $column): ExtendedPromiseInterface
    {
        $walk = new Walk($this->socket);
        return $walk->walk($this->baseOid . '.' . $column, $this->target, $this->community);
    }

    protected function resolve(): void
    {
        $this->deferred->resolve($this->results);
    }
}
