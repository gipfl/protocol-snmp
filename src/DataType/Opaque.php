<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Element;
use ASN1\Type\Primitive\OctetString as AsnType;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\UnspecifiedType;

class Opaque extends DataType
{
    protected $tag = self::OPAQUE;

    protected function __construct(ApplicationType $app)
    {
        $tag = $this->getTag();
        $binary = $app->asImplicit(Element::TYPE_OCTET_STRING, $tag)->asOctetString()->string();

        parent::__construct($binary);
    }

    public function getReadableValue()
    {
        return '0x' . bin2hex($this->rawValue);
    }

    public static function fromASN1(UnspecifiedType $element)
    {
        return new static($element->asApplication());
    }

    public function toASN1()
    {
        return new AsnType($this->rawValue);
    }
}
