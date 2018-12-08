<?php

namespace gipfl\Protocol\Snmp;

class Counter32 extends Unsigned32
{
    protected $tag = DataTypeApplication::COUNTER_32;
}
