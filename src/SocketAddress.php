<?php

namespace gipfl\Protocol\Snmp;

use Stringable;

class SocketAddress implements Stringable
{
    final public function __construct(
        public string $ip,
        public ?int $port = 0
    ) {
    }

    public static function parse(string $string, ?int $defaultPort = 0): static
    {
        if (str_contains($string, ':')) {
            return new static(...explode(':', $string));
        }

        return new static($string, $defaultPort);
    }

    public static function detect(SocketAddress|string $address, ?int $defaultPort = 0): static
    {
        if ($address instanceof static) {
            return $address;
        }

        return static::parse($address, $defaultPort);
    }

    public function toUdpUri(): string
    {
        return 'udp://' . $this->__toString();
    }

    public function __toString(): string
    {
        return $this->ip . ':' . $this->port;
    }
}
