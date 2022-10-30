<?php

namespace gipfl\Protocol\Snmp\DataType;

class TimeTicks extends Unsigned32
{
    protected int $tag = self::TIME_TICKS;
}
