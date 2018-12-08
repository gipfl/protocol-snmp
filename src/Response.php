<?php

namespace gipfl\Protocol\Snmp;

class Response extends Pdu
{
    public function getTag()
    {
        return Pdu::RESPONSE;
    }
}
