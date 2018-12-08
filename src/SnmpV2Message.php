<?php

namespace gipfl\Protocol\Snmp;

class SnmpV2Message extends SnmpV1Message
{
    protected $version = self::SNMP_V2C;
}
