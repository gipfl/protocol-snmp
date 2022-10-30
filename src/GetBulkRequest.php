<?php

namespace gipfl\Protocol\Snmp;

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

    protected int $nonRepeaters = 0;

    protected int $maxRepetitions;

    public function __construct(VarBinds $varBinds, ?int $requestId = null, int $maxRepetitions = 10, $nonRepeaters = 0)
    {
        parent::__construct($varBinds, $requestId);
        $this->maxRepetitions = $maxRepetitions;
        $this->nonRepeaters = $nonRepeaters;
    }

    public function getTag(): int
    {
        return Pdu::GET_BULK_REQUEST;
    }

    public function toASN1(): ImplicitlyTaggedType
    {
        return new ImplicitlyTaggedType($this->getTag(), new Sequence(
            new Integer($this->requestId),
            new Integer($this->nonRepeaters),
            new Integer($this->maxRepetitions),
            $this->varBinds->toASN1()
        ));
    }

    // TODO: fromASN1
}
