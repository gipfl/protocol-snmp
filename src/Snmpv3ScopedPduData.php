<?php

namespace gipfl\Protocol\Snmp;

use Sop\ASN1\Type\Constructed\Sequence;

class Snmpv3ScopedPduData
{
    // Hint: Sequence might not be correct. Or is it a CHOICE?
    public static function fromAsn1(Sequence $sequence): static
    {
        // ScopedPduData ::= CHOICE {
        //   plaintext    ScopedPDU,
        //   encryptedPDU OCTET STRING  -- encrypted scopedPDU value
        // }

        /** @phpstan-ignore-next-line */
        return null;
    }
}
