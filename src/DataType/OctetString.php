<?php

namespace gipfl\Protocol\Snmp\DataType;

use Sop\ASN1\Element;
use Sop\ASN1\Type\UnspecifiedType;
use Sop\ASN1\Type\Primitive\OctetString as AsnType;

use function bin2hex;

class OctetString extends DataType
{
    protected int $tag = Element::TYPE_OCTET_STRING;

    public static function fromString(string $string): DataType|static
    {
        return new OctetString($string);
    }

    public static function fromASN1(UnspecifiedType $element): DataType|static
    {
        return new OctetString($element->asOctetString()->string());
    }

    public function jsonSerialize(): array
    {
        $value = AsnTypeHelper::wantString($this->rawValue);
        return [
            'type'  => 'octet_string',
            'value' => $this->isUtf8Safe() ? $value : '0x' . bin2hex($value),
        ];
    }

    protected function isUtf8Safe(): bool
    {
        // TODO: this is not correct, we would prepend 0x with 0x
        if (str_starts_with(AsnTypeHelper::wantString($this->rawValue), '0x')) {
            return false;
        }

        // TODO: check for special characters
        return false;
    }

    public function toASN1(): Element
    {
        return new AsnType(AsnTypeHelper::wantString($this->rawValue));
    }
}
