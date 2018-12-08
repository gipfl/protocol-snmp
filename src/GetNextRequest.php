<?php

namespace gipfl\Protocol\Snmp;

class GetNextRequest extends Pdu
{
    protected $wantsResponse = true;

    public function getTag()
    {
        return Pdu::GET_NEXT_REQUEST;
    }
}
