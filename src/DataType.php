<?php

namespace gipfl\Protocol\Snmp;

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
