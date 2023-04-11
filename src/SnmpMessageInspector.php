<?php

namespace gipfl\Protocol\Snmp;

class SnmpMessageInspector
{
    public static function dump(SnmpMessage $message): void
    {
        echo static::getDump($message);
    }

    public static function getDump(SnmpMessage $message): string
    {
        $result = sprintf("Version: %s\n", $message->getVersion());
        if ($message instanceof SnmpV1Message) {
            $result .= sprintf("Community: %s\n", $message->community);
        }

        foreach ($message->getPdu()->varBinds as $varBind) {
            $result .= sprintf(
                "%s: %s\n",
                $varBind->oid,
                $varBind->value->getReadableValue()
            );
        }

        return $result;
    }
}
