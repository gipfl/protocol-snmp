<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Element;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

class Unsigned32 extends DataType
{
    protected $tag = self::UNSIGNED_32;

    protected function __construct(ApplicationType $app)
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

    public static function fromInteger($int)
    {
        new ApplicationType();
        return new static((int) $int);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        return new static($element->asApplication());
    }

    public function toASN1()
    {
        return new Integer($this->rawValue);
    }
}
