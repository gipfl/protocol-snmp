<?php

namespace gipfl\Protocol\Snmp;

class SnmpMessageInspector
{
    public static function dump(SnmpMessage $message)
    {
        echo static::getDump($message);
    }

    public static function getDump(SnmpMessage $message)
    {
        $result = sprintf("Version: %s\n", $message->getVersion());
        if ($message instanceof SnmpV1Message) {
            $result .= sprintf("Community: %s\n", $message->getCommunity());
        }

        /** @var VarBind $varBind */
        foreach ($message->getPdu()->getVarBinds()->iterate() as $varBind) {
            $result .= sprintf(
                "%s: %s\n",
                $varBind->getOid(),
                $varBind->getValue()->getReadableValue()
            );
        }

        return $result;
    }
}
