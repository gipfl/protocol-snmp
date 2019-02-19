<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Element;
use ASN1\Type\UnspecifiedType;
use ASN1\Type\Primitive\ObjectIdentifier as AsnType;

class ObjectIdentifier extends DataType
{
    protected $tag = Element::TYPE_OBJECT_IDENTIFIER;

    public static function fromString($oid)
    {
        return new static($oid);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        return new static($element->asObjectIdentifier()->oid());
    }

    public function toASN1()
    {
        return new AsnType($this->rawValue);
    }
}
