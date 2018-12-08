<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Element;
use ASN1\Type\Primitive\OctetString as AsnType;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

class NsapAddress extends DataType
{
    protected $tag = self::NSAP_ADDRESS;

    protected function __construct(ApplicationType $app)
    {
        $tag = $this->getTag();
        $binary = $app->asImplicit(Element::TYPE_OCTET_STRING, $tag)->asOctetString()->string();

        if (strlen($binary) > 20) {
            throw new InvalidArgumentException(sprintf(
                '0x%s is not a valid NSAP Address',
                bin2hex($binary)
            ));
        }

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
