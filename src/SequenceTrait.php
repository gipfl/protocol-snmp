<?php

namespace gipfl\Protocol\Snmp;

use Sop\ASN1\Type\UnspecifiedType;

trait SequenceTrait
{
    public static function fromBinary(string $binary): static
    {
        return static::fromASN1(UnspecifiedType::fromDER($binary)->asSequence());
    }

    public function toBinary(): string
    {
        return $this->toASN1()->toDER();
    }
}
