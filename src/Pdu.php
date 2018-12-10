<?php

namespace gipfl\Protocol\Snmp;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\TaggedType;

abstract class Pdu
{
    const GET_REQUEST = 0;
    const GET_NEXT_REQUEST = 1;
    const RESPONSE = 2;
    const SET_REQUEST = 3;
    const TRAP = 4; // Special, obsolete
    const GET_BULK_REQUEST = 5; // Special
    const INFORM_REQUEST = 6;
    const TRAP_V2 = 7; // ?
    const REPORT = 8;

    /** @var int */
    protected $requestId;

    /** @var int error-status: noError(0) */
    protected $errorStatus = 0;

    /** @var int */
    protected $errorIndex = 0;

    /** @var VarBinds */
    protected $varBinds;

    protected $wantsResponse = false;

    public function __construct(VarBinds $varBinds, $requestId = null)
    {
        $this->varBinds = $varBinds;
        $this->requestId = $requestId;
    }

    /**
     * @return int
     */
    abstract public function getTag();

    public function wantsResponse()
    {
        return $this->wantsResponse;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getVarBinds()
    {
        return $this->varBinds;
    }

    public function isError()
    {
        return $this->errorStatus !== 0;
    }

    public function toASN1()
    {
        return new ImplicitlyTaggedType($this->getTag(), new Sequence(
            new Integer($this->requestId),
            new Integer($this->errorStatus),
            new Integer($this->errorIndex),
            $this->varBinds->toASN1()
        ));
    }

    public static function fromASN1(TaggedType $tagged)
    {
        /** @var \ASN1\Type\Constructed\Sequence $sequence */
        $sequence = $tagged->asImplicit(\ASN1\Element::TYPE_SEQUENCE);
        // $sequence->count() === 4;
        $varBinds = VarBinds::fromASN1($sequence->at(3)->asSequence());
        switch ($tagged->tag()) {
            case self::GET_REQUEST:
                $pdu = new GetRequest($varBinds);
                break;
            case self::GET_NEXT_REQUEST:
                $pdu = new GetNextRequest($varBinds);
                break;
            case self::RESPONSE:
                $pdu = new Response($varBinds);
                break;
            case self::SET_REQUEST:
                $pdu = new SetRequest($varBinds);
                break;
            case self::GET_BULK_REQUEST:
                $pdu = new GetBulkRequest($varBinds); // TODO: max-rep, no error
                break;
            case self::INFORM_REQUEST:
                $pdu = new InformRequest($varBinds);
                break;
            case self::TRAP_V2:
                $pdu = new TrapV2($varBinds);
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Invalid PDU tag %s',
                    $tagged->tag()
                ));
        }

        $pdu->requestId = $sequence->at(0)->asInteger()->intNumber();
        $pdu->errorStatus = $sequence->at(1)->asInteger()->intNumber();
        $pdu->errorIndex = $sequence->at(2)->asInteger()->intNumber();

        return $pdu;
    }
}
