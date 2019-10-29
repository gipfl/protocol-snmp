<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

abstract class DataType
{
    const IP_ADDRESS = 0;
    const COUNTER_32 = 1;
    const GAUGE_32 = 2;
    const TIME_TICKS = 3;
    const OPAQUE = 4;
    const NSAP_ADDRESS = 5;
    const COUNTER_64 = 6;
    const UNSIGNED_32 = 7;

    const TYPE_TO_NAME_MAP = [
        self::IP_ADDRESS   => 'ip_address',
        self::COUNTER_32   => 'counter32',
        self::GAUGE_32     => 'gauge32',
        self::TIME_TICKS   => 'time_ticks',
        self::OPAQUE       => 'opaque',
        self::NSAP_ADDRESS => 'nsap_address',
        self::COUNTER_64   => 'counter64',
        self::UNSIGNED_32  => 'unsigned32',
    ];

    // From SNMPv2-SMI:
    // ipAddress-value => IpAddress
    // counter-value => Counter32
    // timeticks-value => TimeTicks
    // arbitrary-value => Opaque
    // -> nsap?
    // big-counter-value => Counter64
    // unsigned-integer-value => Unsigned32

    const NAME_TO_TYPE_MAP = [
        'ip_address'   => self::IP_ADDRESS,
        'counter32'    => self::COUNTER_32,
        'gauge32'      => self::GAUGE_32,
        'time_ticks'   => self::TIME_TICKS,
        'opaque'       => self::OPAQUE,
        'nsap_address' => self::NSAP_ADDRESS,
        'counter64'    => self::COUNTER_64,
        'unsigned32'   => self::UNSIGNED_32,
    ];

    protected $rawValue;

    protected $tag;

    protected function __construct($rawValue)
    {
        $this->rawValue = $rawValue;
    }

    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return Element
     */
    abstract public function toASN1();

    abstract public function toArray();

    public function getReadableValue()
    {
        return $this->rawValue;
    }

    public static function fromBinary($binary)
    {
        return self::fromASN1(UnspecifiedType::fromDER($binary));
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        $class = $element->typeClass();

        switch ($element->typeClass()) {
            case Identifier::CLASS_UNIVERSAL:
                return DataTypeUniversal::fromASN1($element);
            case Identifier::CLASS_APPLICATION:
                return DataTypeApplication::fromASN1($element);
            case Identifier::CLASS_CONTEXT_SPECIFIC:
                return DataTypeContextSpecific::fromASN1($element);
            default:
                $className = Identifier::classToName($class);
                throw new InvalidArgumentException(
                    "Unsupported ASN1 class=$className"
                );
        }
    }
}
