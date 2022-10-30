<?php

namespace gipfl\Protocol\Snmp;

use ArrayIterator;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\UnspecifiedType;

class VarBinds
{
    use SequenceTrait;

    /** @var VarBind[] */
    protected array $varBinds;

    final public function __construct(VarBind ...$varBinds)
    {
        $this->varBinds = $varBinds;
    }

    public static function fromASN1(Sequence $varBinds): static
    {
        $bindings = [];
        /** @var UnspecifiedType $varBind */
        foreach ($varBinds as $idx => $varBind) {
            try {
                $bindings[] = VarBind::fromASN1($varBind->asSequence());
            } catch (\UnexpectedValueException $e) {
                throw new \InvalidArgumentException(sprintf(
                    "Can't decode Variable Binding %d: %s",
                    $idx + 1,
                    $e->getMessage()
                ), 0, $e);
            }
        }

        return new static(...$bindings);
    }

    public function iterate(): ArrayIterator
    {
        return new ArrayIterator($this->varBinds);
    }

    public function toASN1(): Sequence
    {
        $result = [];
        foreach ($this->varBinds as $varBind) {
            $result[] = $varBind->toASN1();
        }

        return new Sequence(...$result);
    }
}
