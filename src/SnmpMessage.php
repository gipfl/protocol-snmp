<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

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

    protected $version;

    /**
     * @param Sequence $sequence
     * @return SnmpMessage
     */
    public static function fromASN1(Sequence $sequence)
    {
        $version = $sequence->at(0)->asInteger()->intNumber();

        switch ($version) {
            case self::SNMP_V1:
                return SnmpV1Message::fromASN1($sequence);
            case self::SNMP_V2C:
                return SnmpV2Message::fromASN1($sequence);
            case SnmpMessage::SNMP_V3:
                return SnmpV3Message::fromASN1($sequence);
            default:
                throw new InvalidArgumentException('Unsupported message version: ' . $version);
        }
    }

    /**
     * @return Sequence
     */
    abstract public function toASN1();

    /**
     * @return Pdu
     */
    abstract public function getPdu();

    /**
     * @param $binary
     * @return SnmpMessage
     */
    public static function fromBinary($binary)
    {
        return static::fromASN1(UnspecifiedType::fromDER($binary)->asSequence());
    }

    public function getVersion()
    {
        return static::$versionNames[$this->version];
    }
}
