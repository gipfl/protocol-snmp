<?php

namespace gipfl\Protocol\Snmp\DataType;

use Sop\ASN1\Element;
use Sop\ASN1\Type\UnspecifiedType;
use Sop\ASN1\Type\Primitive\OctetString as AsnType;
use function bin2hex;

class OctetString extends DataType
{
    protected int $tag = Element::TYPE_OCTET_STRING;

    public static function fromString($string): DataType|static
    {
        return new OctetString($string);
    }

    public static function fromASN1(UnspecifiedType $element): DataType|static
    {
        return new OctetString($element->asOctetString()->string());
    }

    public function toArray(): array
    {
        return [
            'type'  => 'octet_string',
            'value' => $this->isUtf8Safe() ? $this->rawValue : '0x' . bin2hex($this->rawValue),
        ];
    }

    protected function isUtf8Safe(): bool
    {
        // TODO: this is not correct, we would prepend 0x with 0x
        if (str_starts_with($this->rawValue, '0x')) {
            return false;
        }

        // TODO: check for special characters
        return false;
    }

    public function toASN1(): Element
    {
        return new AsnType($this->rawValue);
    }
}
