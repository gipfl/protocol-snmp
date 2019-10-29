<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Element;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

class Counter64 extends DataType
{
    protected $tag = self::COUNTER_64;

    protected function __construct(ApplicationType $app)
    {
        $tag = $this->getTag();
        $value = $app->asImplicit(Element::TYPE_INTEGER, $tag)->asInteger()->intNumber();
        if ($value < 0 || $value > 18446744073709551615) {
            throw new InvalidArgumentException(
                '%s is not a valid Counter64'
            );
        }
        parent::__construct($value);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        return new static($element->asApplication());
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
