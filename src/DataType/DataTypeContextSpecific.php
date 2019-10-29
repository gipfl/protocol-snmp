<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Type\Primitive\NullType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\UnspecifiedType;

class DataTypeContextSpecific extends DataType
{
    const NO_SUCH_OBJECT = 0;
    const NO_SUCH_INSTANCE = 1;
    const END_OF_MIB_VIEW = 2;

    protected static $errorMessages = [
        self::NO_SUCH_OBJECT   => 'No such object',
        self::NO_SUCH_INSTANCE => 'No such instance',
        self::END_OF_MIB_VIEW  => 'End of MIB view',
    ];

    protected function __construct($rawValue)
    {
        parent::__construct(null);
        $this->tag = $rawValue;
    }

    public function toArray()
    {
        return [
            'type'  => 'context_specific',
            'value' => $this->rawValue,
        ];
    }

    public static function noSuchObject()
    {
        return new static(self::NO_SUCH_OBJECT);
    }

    public static function noSuchInstance()
    {
        return new static(self::NO_SUCH_INSTANCE);
    }

    public static function endOfMibView()
    {
        return new static(self::END_OF_MIB_VIEW);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        $tag = $element->tag();
        if (isset(static::$errorMessages[$tag])) {
            return new static($tag);
        } else {
            throw new \InvalidArgumentException(
                "Unknown context specific data type, tag=$tag"
            );
        }
    }

    public function toASN1()
    {
        return new ImplicitlyTaggedType($this->getTag(), new NullType());
    }
}
