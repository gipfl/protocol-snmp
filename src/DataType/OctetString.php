<?php

namespace gipfl\Protocol\Snmp\DataType;

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

    public function toArray()
    {
        return [
            'type'  => 'octet_string',
            'value' => $this->isUtf8Safe() ? $this->rawValue : '0x' . \bin2hex($this->rawValue),
        ];
    }

    protected function isUtf8Safe()
    {
        if (\substr($this->rawValue, 0, 2) === '0x') {
            return false;
        }

        // TODO: check for special characters
        return false;
    }

    public function toASN1()
    {
        return new AsnType($this->rawValue);
    }
}
