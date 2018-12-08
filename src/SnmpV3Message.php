<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\Constructed\Sequence;

class SnmpV3Message extends SnmpMessage
{
    protected $version = self::SNMP_V3;

    public function toASN1()
    {
        // TODO: Implement toASN1() method.
    }

    public static function fromASN1(Sequence $sequence)
    {
        // RFC 3412, page 18
        $message = new static;
        $message->setGlobalData($sequence->at(1)->asSequence()); // HeaderData
        $message->setSecurityParameters($sequence->at(2)->asOctetString()->string());
        $message->setData($sequence->at(3)); // ScopedPduData
    }
}
