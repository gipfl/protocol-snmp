<?php

namespace gipfl\Protocol\Snmp;

/**
 * InformRequest
 *
 * Has been added in SNMPv2
 */
class InformRequest extends Pdu
{
    protected bool $wantsResponse = true;

    public function getTag(): int
    {
        return Pdu::INFORM_REQUEST;
    }
}
