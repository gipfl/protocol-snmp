<?php

namespace gipfl\Protocol\Snmp;

class GetRequest extends Pdu
{
    protected bool $wantsResponse = true;

    public function getTag(): int
    {
        return Pdu::GET_REQUEST;
    }
}
