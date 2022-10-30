<?php

namespace gipfl\Protocol\Snmp;

interface RequestIdConsumer
{
    public function hasId(int $id): bool;
}
