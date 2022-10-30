<?php

namespace gipfl\Protocol\Snmp\DataType;

use InvalidArgumentException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Tagged\ApplicationType;
use Sop\ASN1\Type\UnspecifiedType;

class Counter64 extends DataType
{
    protected int $tag = self::COUNTER_64;

    final protected function __construct(ApplicationType $app)
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

    public static function fromASN1(UnspecifiedType $element): DataType|static
    {
        return new static($element->asApplication());
    }

    public function toASN1(): Element
    {
        return new Integer($this->rawValue);
    }

    public function toArray(): array
    {
        return [
            'type'  => self::TYPE_TO_NAME_MAP[$this->getTag()],
            'value' => $this->rawValue,
        ];
    }
}
