<?php

namespace gipfl\Protocol\Snmp\DataType;

use InvalidArgumentException;
use Sop\ASN1\Component\Identifier;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Tagged\ImplicitlyTaggedType;
use Sop\ASN1\Type\UnspecifiedType;

abstract class DataTypeApplication extends DataType
{
    public static function fromASN1(UnspecifiedType $element): DataType|static
    {
        return match ($element->tag()) {
            self::IP_ADDRESS   => IpAddress::fromASN1($element),
            self::COUNTER_32   => Counter32::fromASN1($element),
            self::GAUGE_32     => Gauge32::fromASN1($element),
            self::TIME_TICKS   => TimeTicks::fromASN1($element),
            self::OPAQUE       => Opaque::fromASN1($element),
            self::NSAP_ADDRESS => NsapAddress::fromASN1($element),
            self::COUNTER_64   => Counter64::fromASN1($element),
            self::UNSIGNED_32  => Unsigned32::fromASN1($element),
            default => throw new InvalidArgumentException(
                'Unknown application data type, tag=' . $element->tag()
            ),
        };
    }

    public function toASN1(): Element
    {
        $int = new Integer(AsnTypeHelper::wantGmpIntString($this->rawValue));
        return new ImplicitlyTaggedType(
            self::getTag(),
            $int,
            Identifier::CLASS_APPLICATION
        );
    }
}
