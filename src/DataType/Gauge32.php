<?php

namespace gipfl\Protocol\Snmp\DataType;

class Gauge32 extends Unsigned32
{
    protected int $tag = DataType::GAUGE_32;
}
