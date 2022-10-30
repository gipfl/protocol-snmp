<?php

namespace gipfl\Protocol\Snmp\DataType;

use Sop\ASN1\Element;
use Sop\ASN1\Type\UnspecifiedType;
use Sop\ASN1\Type\Primitive\NullType as AsnType;

class NullType extends DataType
{
    protected int $tag = Element::TYPE_NULL;

    final public function __construct()
    {
        parent::__construct(null);
    }

    public static function create(): static
    {
        return new static();
    }

    public function getReadableValue(): string
    {
        return '(null)';
    }

    public function toArray(): array
    {
        return [
            'type'  => 'null',
            'value' => null,
        ];
    }

    public static function fromASN1(UnspecifiedType $element): static
    {
        $element->asNull();

        return new static();
    }

    public function toASN1(): Element
    {
        return new AsnType();
    }
}
