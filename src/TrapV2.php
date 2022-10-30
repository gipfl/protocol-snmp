<?php

namespace gipfl\Protocol\Snmp;

class TrapV2 extends Pdu
{
    public function getTag(): int
    {
        return Pdu::TRAP_V2;
    }
}
