<?php

namespace gipfl\Protocol\Snmp;

class SnmpMessageInspector
{
    public static function dump(SnmpMessage $message)
    {
        printf("Version: %s\n", $message->getVersion());
        if ($message instanceof SnmpV1Message) {
            printf("Community: %s\n", $message->getCommunity());
        }

        /** @var VarBind $varBind */
        foreach ($message->getPdu()->getVarBinds() as $varBind) {
            printf(
                "%s: :%s\n",
                $varBind->getOid(),
                $varBind->getValue()->getReadableValue()
            );
        }
    }
}
