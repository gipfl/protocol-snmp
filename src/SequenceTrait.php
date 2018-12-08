<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\UnspecifiedType;

trait SequenceTrait
{
    public static function fromBinary($binary)
    {
        return static::fromASN1(UnspecifiedType::fromDER($binary)->asSequence());
    }

    public function toBinary()
    {
        return $this->toASN1()->toDER();
    }
}
