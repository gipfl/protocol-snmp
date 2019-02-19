<?php

namespace gipfl\Protocol\Snmp\DataType;

class Gauge32 extends Unsigned32
{
    protected $tag = DataTypeApplication::GAUGE_32;
}
