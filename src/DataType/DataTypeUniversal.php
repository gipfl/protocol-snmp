<?php

namespace gipfl\Protocol\Snmp\DataType;

use InvalidArgumentException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\UnspecifiedType;

abstract class DataTypeUniversal extends DataType
{
    public static function fromASN1(UnspecifiedType $element): DataType
    {
        $tag = $element->tag();
        switch ($tag) {
            case Element::TYPE_INTEGER:
                return Integer32::fromASN1($element);
            case Element::TYPE_OCTET_STRING:
                return OctetString::fromASN1($element);
            // case Element::TYPE_BIT_STRING:
            //     return BitString::fromASN1($element);
            case Element::TYPE_OBJECT_IDENTIFIER:
                return ObjectIdentifier::fromASN1($element);
            case Element::TYPE_NULL:
                return new NullType();
            default:
                $typeName = Element::tagToName($tag);
                throw new InvalidArgumentException("SNMP does not support ASN1 Universal type '$typeName'");
        }
    }
}
