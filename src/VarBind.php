<?php

namespace gipfl\Protocol\Snmp;

use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\DataType\NullType;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\ObjectIdentifier;

class VarBind
{
    use SequenceTrait;

    final public function __construct(
        public readonly string $oid,
        public readonly DataType $value = new NullType()
    ) {
    }

    public function toASN1(): Sequence
    {
        return new Sequence(new ObjectIdentifier($this->oid), $this->value->toASN1());
    }

    public static function fromASN1(Sequence $varBind): static
    {
        // $varBind->count() === 2

        $oid = $varBind->at(0)->asObjectIdentifier()->oid();
        $value = DataType::fromASN1($varBind->at(1));

        return new static($oid, $value);
    }
}
