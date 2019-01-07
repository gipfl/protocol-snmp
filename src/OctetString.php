<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Element;
use ASN1\Type\UnspecifiedType;
use ASN1\Type\Primitive\OctetString as AsnType;

class OctetString extends DataType
{
    protected $tag = Element::TYPE_OCTET_STRING;

    public static function fromString($string)
    {
        return new static($string);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        return new static($element->asOctetString()->string());
    }

    public function toASN1()
    {
        return new AsnType($this->rawValue);
    }
}
