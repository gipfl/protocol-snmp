<?php

namespace gipfl\Protocol\Snmp;

class SnmpV2Message extends SnmpV1Message
{
    protected int $version = self::SNMP_V2C;
}
