<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;

class SnmpV1Message extends SnmpMessage
{
    protected $version = self::SNMP_V1;

    protected $community;

    // unused
    protected $rawPdu;

    /** @var Pdu */
    protected $pdu;

    public function __construct($community, Pdu $pdu)
    {
        $this->setCommunity($community);
        $this->pdu = $pdu;
    }

    public function getCommunity()
    {
        return $this->community;
    }

    public function setCommunity($community)
    {
        $this->community = $community;

        return $this;
    }

    /**
     * @return Sequence
     */
    public function toASN1()
    {
        return new Sequence(
            new Integer($this->version),
            new OctetString($this->getCommunity()),
            $this->getPdu()->toASN1()
        );
    }

    /**
     * @return Pdu
     */
    public function getPdu()
    {
        return $this->pdu;
    }

    public static function fromASN1(Sequence $sequence)
    {
        return new static(
            $sequence->at(1)->asOctetString()->string(),
            Pdu::fromASN1($sequence->at(2)->asTagged())
        );
    }
}
