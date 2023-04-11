<?php

namespace gipfl\Protocol\Snmp;

use RuntimeException;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Tagged\ImplicitlyTaggedType;

/**
 * GetBulkRequest
 *
 * Has been added in SNMPv2
 */
class GetBulkRequest extends Pdu
{
    protected bool $wantsResponse = true;

    public function __construct(
        array $varBinds,
        ?int $requestId = null,
        protected int $maxRepetitions = 10,
        protected int $nonRepeaters = 0
    ) {
        parent::__construct($varBinds, $requestId);
    }

    public function getTag(): int
    {
        return Pdu::GET_BULK_REQUEST;
    }

    public function toASN1(): ImplicitlyTaggedType
    {
        if ($this->requestId === null) {
            throw new RuntimeException('Cannot created ASN1 type w/o requiestId');
        }
        return new ImplicitlyTaggedType($this->getTag(), new Sequence(
            new Integer($this->requestId),
            new Integer($this->nonRepeaters),
            new Integer($this->maxRepetitions),
            VarBind::listToSequence($this->varBinds)
        ));
    }

    // TODO: fromASN1
}
