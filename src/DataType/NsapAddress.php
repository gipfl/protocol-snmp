<?php

namespace gipfl\Protocol\Snmp\DataType;

use InvalidArgumentException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Primitive\OctetString as AsnType;
use Sop\ASN1\Type\UnspecifiedType;

class NsapAddress extends DataType
{
    protected int $tag = self::NSAP_ADDRESS;

    final protected function __construct(string $string)
    {
        if (strlen($string) > 20) {
            throw new InvalidArgumentException(sprintf(
                '0x%s is not a valid NSAP Address',
                bin2hex($string)
            ));
        }

        parent::__construct($string);
    }

    public function jsonSerialize(): array
    {
        return [
            'type'  => self::TYPE_TO_NAME_MAP[$this->tag],
            'value' => $this->getReadableValue(),
        ];
    }

    public function getReadableValue(): string
    {
        return '0x' . bin2hex(AsnTypeHelper::wantString($this->rawValue));
    }

    public static function fromASN1(UnspecifiedType $element): static
    {
        return new static($element->asOctetString()->string());
    }

    public function toASN1(): Element
    {
        return new AsnType(AsnTypeHelper::wantString($this->rawValue));
    }
}
