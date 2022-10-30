<?php

namespace gipfl\Protocol\Snmp;

use Sop\ASN1\Type\Constructed\Sequence;

class Snmpv3ScopedPdu
{
    final public function __construct(
        public readonly Pdu $pdu,
        protected string $contextEngineId,
        protected string $contextName
    ) {
    }

    public static function fromAsn1(Sequence $sequence): static
    {
        // ScopedPDU ::= SEQUENCE {
        //   contextEngineID  OCTET STRING,
        //   contextName      OCTET STRING,
        //   data             ANY -- e.g., PDUs as defined in [RFC3416]
        // }
        return new static(
            Pdu::fromASN1($sequence->at(2)->asTagged()),
            $sequence->at(0)->asOctetString(),
            $sequence->at(0)->asOctetString()
        );
    }
}
