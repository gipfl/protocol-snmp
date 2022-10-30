<?php

namespace gipfl\Protocol\Snmp;

use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Primitive\OctetString;

class SnmpV3Message extends SnmpMessage
{
    const SECURITY_NO_AUTH = 0x00;
    const SECURITY_AUTH_NO_PRIV = 0x01;
    const SECURITY_AUTH_PRIV = 0x11;
    const REPORTABLE_FLAG = "\x04";

    protected int $version = self::SNMP_V3;

    // TODO: Should we really require a full header, or just some params?
    final public function __construct(
        public readonly Snmpv3Header $header,
        public readonly string $securityParameters, // defined by security model
        public readonly Snmpv3ScopedPduData $scopedPduData
    ) {
    }

    public function getPdu(): Pdu
    {
        // TODO: Implement getPdu() method. What we're missing
        /** @phpstan-ignore-next-line */
        return null;
    }

    public function toASN1(): Sequence
    {
        return new Sequence(
            new Integer($this->version),
            $this->header->toASN1(),
            new OctetString($this->securityParameters),
            new OctetString(''/* $this->scopedPduData*/)
        );
    }

    public static function fromASN1(Sequence $sequence): static
    {
        // RFC 3412, page 18
        // SNMPv3Message ::= SEQUENCE {
        // -- identify the layout of the SNMPv3Message
        // -- this element is in same position as in SNMPv1
        // -- and SNMPv2c, allowing recognition
        // -- the value 3 is used for snmpv3
        // msgVersion INTEGER ( 0 .. 2147483647 ),
        // HeaderData administrative parameters:
        // security model-specific parameters
        // format defined by Security Model:
        // ScopedPduData:
        return new static(
            Snmpv3Header::fromAsn1($sequence->at(1)->asSequence()),
            $sequence->at(2)->asOctetString()->string(),
            Snmpv3ScopedPduData::fromAsn1($sequence->at(3)->asSequence())
        );
    }
}
