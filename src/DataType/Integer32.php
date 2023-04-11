<?php

namespace gipfl\Protocol\Snmp\DataType;

use Sop\ASN1\Element;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\UnspecifiedType;

class Integer32 extends DataType
{
    protected int $tag = Element::TYPE_INTEGER;

    public static function fromInteger(int $int): Integer32
    {
        return new Integer32($int);
    }

    public static function fromASN1(UnspecifiedType $element): Integer32
    {
        return new Integer32($element->asInteger()->intNumber());
    }

    public function toASN1(): Element
    {
        return new Integer(AsnTypeHelper::wantGmpIntString($this->rawValue));
    }

    public function jsonSerialize(): array
    {
        return [
            'type'  => self::TYPE_TO_NAME_MAP[$this->getTag()],
            'value' => $this->rawValue,
        ];
    }
}
