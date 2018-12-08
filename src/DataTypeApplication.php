<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Component\Identifier;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

abstract class DataTypeApplication extends DataType
{
    const IP_ADDRESS = 0;
    const COUNTER_32 = 1;
    const GAUGE_32 = 2;
    const TIME_TICKS = 3;
    const OPAQUE = 4;
    const NSAP_ADDRESS = 5;
    const COUNTER_64 = 6;
    const UNSIGNED_32 = 7;

    public static function fromASN1(UnspecifiedType $element)
    {
        switch ($element->tag()) {
            case self::IP_ADDRESS:
                return IpAddress::fromASN1($element);
            case self::COUNTER_32:
                return Counter32::fromASN1($element);
            case self::GAUGE_32:
                return Gauge32::fromASN1($element);
            case self::TIME_TICKS:
                return TimeTicks::fromASN1($element);
            case self::OPAQUE:
                return Opaque::fromASN1($element);
            case self::NSAP_ADDRESS:
                return NsapAddress::fromASN1($element);
            case self::COUNTER_64:
                return Counter64::fromASN1($element);
            case self::UNSIGNED_32:
                return Unsigned32::fromASN1($element);
            default:
                throw new InvalidArgumentException(
                    'Unknown application data type, tag=' . $element->tag()
                );
        }
    }

    public function toASN1()
    {
        $int = new Integer($this->rawValue);
        return new ImplicitlyTaggedType(
            self::getTag(),
            $int,
            Identifier::CLASS_APPLICATION
        );
    }
}
