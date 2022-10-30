<?php

namespace gipfl\Protocol\Snmp\DataType;

use InvalidArgumentException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Primitive\NullType;
use Sop\ASN1\Type\Tagged\ImplicitlyTaggedType;
use Sop\ASN1\Type\UnspecifiedType;

class DataTypeContextSpecific extends DataType
{
    const NO_SUCH_OBJECT = 0;
    const NO_SUCH_INSTANCE = 1;
    const END_OF_MIB_VIEW = 2;

    protected static array $errorMessages = [
        self::NO_SUCH_OBJECT   => 'No such object',
        self::NO_SUCH_INSTANCE => 'No such instance',
        self::END_OF_MIB_VIEW  => 'End of MIB view',
    ];

    final protected function __construct($rawValue)
    {
        parent::__construct(null);
        $this->tag = $rawValue;
    }

    public function toArray(): array
    {
        return [
            'type'  => 'context_specific',
            'value' => $this->rawValue,
        ];
    }

    public static function noSuchObject(): static
    {
        return new static(self::NO_SUCH_OBJECT);
    }

    public static function noSuchInstance(): static
    {
        return new static(self::NO_SUCH_INSTANCE);
    }

    public static function endOfMibView(): static
    {
        return new static(self::END_OF_MIB_VIEW);
    }

    public static function fromASN1(UnspecifiedType $element): static
    {
        $tag = $element->tag();
        if (isset(static::$errorMessages[$tag])) {
            return new static($tag);
        } else {
            throw new InvalidArgumentException(
                "Unknown context specific data type, tag=$tag"
            );
        }
    }

    public function toASN1(): Element
    {
        return new ImplicitlyTaggedType($this->getTag(), new NullType());
    }
}
