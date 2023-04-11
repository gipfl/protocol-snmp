<?php

namespace gipfl\Protocol\Snmp;

use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Primitive\OctetString;

class SnmpV1Message extends SnmpMessage
{
    protected int $version = self::SNMP_V1;

    final public function __construct(
        #[\SensitiveParameter]
        public readonly string $community,
        public Pdu $pdu
    ) {
    }

    public function toASN1(): Sequence
    {
        return new Sequence(
            new Integer($this->version),
            new OctetString($this->community),
            $this->pdu->toASN1()
        );
    }

    public function getPdu(): Pdu
    {
        return $this->pdu;
    }

    public static function fromASN1(Sequence $sequence): static
    {
        return new static(
            $sequence->at(1)->asOctetString()->string(),
            Pdu::fromASN1($sequence->at(2)->asTagged())
        );
    }
}
