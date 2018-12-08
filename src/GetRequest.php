<?php

namespace gipfl\Protocol\Snmp;

class GetRequest extends Pdu
{
    protected $wantsResponse = true;

    public function getTag()
    {
        return Pdu::GET_REQUEST;
    }
}
