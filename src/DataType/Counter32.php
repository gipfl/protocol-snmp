<?php

namespace gipfl\Protocol\Snmp\DataType;

class Counter32 extends Unsigned32
{
    protected int $tag = DataType::COUNTER_32;
}
