<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Element;
use ASN1\Type\UnspecifiedType;

abstract class DataTypeUniversal extends DataType
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
        $tag = $element->tag();
        switch ($tag) {
            case Element::TYPE_INTEGER:
                return Integer32::fromASN1($element);
            case Element::TYPE_OCTET_STRING:
                return OctetString::fromASN1($element);
            case Element::TYPE_BIT_STRING:
                return BitString::fromASN1($element);
            case Element::TYPE_OBJECT_IDENTIFIER:
                return ObjectIdentifier::fromASN1($element);
            case Element::TYPE_NULL:
                return new NullType(null);
            default:
                $typeName = Element::tagToName($tag);
                throw new \InvalidArgumentException("SNMP does not support ASN1 Universal type '$typeName'");
        }
    }
}
