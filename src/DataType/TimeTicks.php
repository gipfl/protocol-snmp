<?php

namespace gipfl\Protocol\Snmp\DataType;

class TimeTicks extends Unsigned32
{
    protected $tag = DataTypeApplication::TIME_TICKS;
}
