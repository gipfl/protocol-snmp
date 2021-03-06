<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Element;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\UnspecifiedType;

class Integer32 extends DataType
{
    protected $tag = Element::TYPE_INTEGER;

    public static function fromInteger($int)
    {
        return new static((int) $int);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        return new static($element->asInteger()->intNumber());
    }

    public function toASN1()
    {
        return new Integer($this->rawValue);
    }

    public function toArray()
    {
        return [
            'type'  => self::TYPE_TO_NAME_MAP[$this->getTag()],
            'value' => $this->rawValue,
        ];
    }
}
