<?php

namespace gipfl\Protocol\Snmp\DataType;

use InvalidArgumentException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Tagged\ApplicationType;
use Sop\ASN1\Type\UnspecifiedType;

class Unsigned32 extends DataType
{
    protected int $tag = self::UNSIGNED_32;

    final protected function __construct(ApplicationType $app)
    {
        $tag = $this->getTag();
        $value = $app->asImplicit(Element::TYPE_INTEGER, $tag)->asInteger()->intNumber();
        if ($value < 0 || $value > 4294967295) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid unsigned integer',
                $value
            ));
        }
        parent::__construct($value);
    }

    // TODO
    // public static function fromInteger(int $int): static
    // {
    //     new ApplicationType();
    //     return new static($int);
    // }

    public function toArray(): array
    {
        return [
            'type'  => self::TYPE_TO_NAME_MAP[$this->tag],
            'value' => $this->rawValue,
        ];
    }

    public static function fromASN1(UnspecifiedType $element): static
    {
        return new static($element->asApplication());
    }

    public function toASN1(): Element
    {
        return new Integer($this->rawValue);
    }
}
