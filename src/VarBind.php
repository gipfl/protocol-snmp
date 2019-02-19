<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\ObjectIdentifier;
use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\DataType\NullType;

class VarBind
{
    use SequenceTrait;

    protected $oid;

    /** @var DataType */
    protected $value;

    public function __construct($oid, DataType $value = null)
    {
        $this->oid = $oid;
        if ($value === null) {
            $this->value = NullType::create();
        } else {
            $this->value = $value;
        }
    }

    public function getOid()
    {
        return $this->oid;
    }

    /**
     * @return DataType
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Sequence
     */
    public function toASN1()
    {
        return new Sequence(new ObjectIdentifier($this->oid), $this->value->toASN1());
    }

    public static function fromASN1(Sequence $varBind)
    {
        // $varBind->count() === 2

        $oid = $varBind->at(0)->asObjectIdentifier()->oid();
        $value = DataType::fromASN1($varBind->at(1));

        return new self($oid, $value);
    }
}
