<?php

namespace gipfl\Protocol\Snmp;

/**
 * InformRequest
 *
 * Has been added in SNMPv2
 */
class InformRequest extends Pdu
{
    protected $wantsResponse = true;

    public function getTag()
    {
        return Pdu::INFORM_REQUEST;
    }
}
