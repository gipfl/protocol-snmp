<?php

namespace gipfl\Protocol\Snmp\DataType;

use ASN1\Element;
use ASN1\Type\Primitive\OctetString as AsnType;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\UnspecifiedType;
use InvalidArgumentException;

class IpAddress extends DataType
{
    protected $tag = self::IP_ADDRESS;

    protected function __construct(ApplicationType $app)
    {
        $tag = $this->getTag();
        $binaryIp = $app->asImplicit(Element::TYPE_OCTET_STRING, $tag)->asOctetString()->string();

        if (strlen($binaryIp) !== 4) {
            throw new InvalidArgumentException(sprintf(
                '0x%s is not a valid IpAddress',
                bin2hex($binaryIp)
            ));
        }
        parent::__construct($binaryIp);
    }

    public function toArray()
    {
        return [
            'type'  => self::TYPE_TO_NAME_MAP[$this->tag],
            'value' => $this->getReadableValue(),
        ];
    }

    public function getReadableValue()
    {
        return inet_ntop($this->rawValue);
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
