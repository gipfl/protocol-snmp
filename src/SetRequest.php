<?php

namespace gipfl\Protocol\Snmp;

class SetRequest extends Pdu
{
    protected $wantsResponse = true;

    public function getTag()
    {
        return Pdu::SET_REQUEST;
    }
}
