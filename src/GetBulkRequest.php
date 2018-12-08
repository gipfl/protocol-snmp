<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ImplicitlyTaggedType;

/**
 * GetBulkRequest
 *
 * Has been added in SNMPv2
 */
class GetBulkRequest extends Pdu
{
    protected $wantsResponse = true;

    protected $nonRepeaters = 0;

    protected $maxRepetitions;

    public function __construct(VarBinds $varBinds, $requestId = null, $maxRepetitions = 10, $nonRepeaters = 0)
    {
        parent::__construct($varBinds, $requestId);
        $this->maxRepetitions = $maxRepetitions;
        $this->nonRepeaters = $nonRepeaters;
    }

    public function getTag()
    {
        return Pdu::GET_BULK_REQUEST;
    }

    public function toASN1()
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
