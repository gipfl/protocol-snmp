<?php

namespace gipfl\Protocol\Snmp;

/**
 * Report
 *
 * Has been added in SNMPv3
 */
class Report extends Pdu
{
    protected $wantsResponse = true;

    public function getTag()
    {
        return Pdu::REPORT;
    }
}
