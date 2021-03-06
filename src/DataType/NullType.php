<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Element;
use ASN1\Type\UnspecifiedType;
use ASN1\Type\Primitive\NullType as AsnType;

class NullType extends DataType
{
    protected $tag = Element::TYPE_NULL;

    public static function create()
    {
        return new static(null);
    }

    public function getReadableValue()
    {
        return '(null)';
    }

    public function toArray()
    {
        return [
            'type'  => 'null',
            'value' => null,
        ];
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        $element->asNull();

        return new static(null);
    }

    public function toASN1()
    {
        return new AsnType();
    }
}
