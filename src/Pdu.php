<?php

namespace gipfl\Protocol\Snmp;

use InvalidArgumentException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Tagged\ImplicitlyTaggedType;
use Sop\ASN1\Type\TaggedType;

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

    protected ?int $requestId;
    protected int $errorStatus = 0; // error-status: noError(0)
    protected int $errorIndex = 0;
    protected VarBinds $varBinds;
    protected bool $wantsResponse = false;

    public function __construct(VarBinds $varBinds, ?int $requestId = null)
    {
        $this->varBinds = $varBinds;
        $this->requestId = $requestId;
    }

    abstract public function getTag(): int;

    public function wantsResponse(): bool
    {
        return $this->wantsResponse;
    }

    public function getRequestId(): ?int
    {
        return $this->requestId;
    }

    public function setRequestId($id): static
    {
        $this->requestId = $id;

        return $this;
    }

    public function getVarBinds(): VarBinds
    {
        return $this->varBinds;
    }

    public function isError(): bool
    {
        return $this->errorStatus !== 0;
    }

    public function toASN1(): ImplicitlyTaggedType
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
        $sequence = $tagged->asImplicit(Element::TYPE_SEQUENCE)->asSequence();
        // $sequence->count() === 4;
        $varBinds = VarBinds::fromASN1($sequence->at(3)->asSequence());
        $pdu = match ($tagged->tag()) {
            self::GET_REQUEST      => new GetRequest($varBinds),
            self::GET_NEXT_REQUEST => new GetNextRequest($varBinds),
            self::RESPONSE         => new Response($varBinds),
            self::SET_REQUEST      => new SetRequest($varBinds),
            self::GET_BULK_REQUEST => new GetBulkRequest($varBinds),
            self::INFORM_REQUEST   => new InformRequest($varBinds),
            self::TRAP_V2          => new TrapV2($varBinds),
            default                 => throw new InvalidArgumentException(sprintf(
                'Invalid PDU tag %s',
                $tagged->tag()
            )),
        };

        $pdu->requestId = $sequence->at(0)->asInteger()->intNumber();
        $pdu->errorStatus = $sequence->at(1)->asInteger()->intNumber();
        $pdu->errorIndex = $sequence->at(2)->asInteger()->intNumber();

        return $pdu;
    }
}
