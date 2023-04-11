<?php

namespace gipfl\Protocol\Snmp;

use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\DataType\NullType;
use InvalidArgumentException;
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
        if ($varBind->count() !== 2) {
            throw new InvalidArgumentException(sprintf(
                'Cannot construct a VarBind from a sequence with %d instead of 2 elements',
                $varBind->count()
            ));
        }

        return new static(
            $varBind->at(0)->asObjectIdentifier()->oid(),
            DataType::fromASN1($varBind->at(1))
        );

    }
}
