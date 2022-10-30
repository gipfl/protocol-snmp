<?php

namespace gipfl\Protocol\Snmp;

use InvalidArgumentException;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\UnspecifiedType;

abstract class SnmpMessage
{
    use SequenceTrait;

    const SNMP_V1  = 0;
    const SNMP_V2C = 1;
    const SNMP_V3  = 3;

    public static $versionNames = [
        self::SNMP_V1  => 'v1',
        self::SNMP_V2C => 'v2c',
        self::SNMP_V3  => 'v3',
    ];

    protected int $version;

    /**
     * @param Sequence $sequence
     * @return SnmpMessage
     */
    public static function fromASN1(Sequence $sequence): SnmpMessage
    {
        $version = $sequence->at(0)->asInteger()->intNumber();

        return match ($version) {
            self::SNMP_V1        => SnmpV1Message::fromASN1($sequence),
            self::SNMP_V2C       => SnmpV2Message::fromASN1($sequence),
            SnmpMessage::SNMP_V3 => SnmpV3Message::fromASN1($sequence),
            default => throw new InvalidArgumentException("Unsupported message version: $version"),
        };
    }

    abstract public function toASN1(): Sequence;

    abstract public function getPdu(): Pdu;

    public static function fromBinary(string $binary): SnmpMessage
    {
        return static::fromASN1(UnspecifiedType::fromDER($binary)->asSequence());
    }

    public function getVersion(): string
    {
        return static::$versionNames[$this->version];
    }
}
