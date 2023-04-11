<?php

namespace gipfl\Protocol\Snmp;

use InvalidArgumentException;
use RuntimeException;
use Sop\ASN1\Element;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Tagged\ImplicitlyTaggedType;
use Sop\ASN1\Type\TaggedType;

abstract class Pdu
{
    public const GET_REQUEST = 0;
    public const GET_NEXT_REQUEST = 1;
    public const RESPONSE = 2;
    public const SET_REQUEST = 3;
    public const TRAP = 4; // Special, obsolete
    public const GET_BULK_REQUEST = 5; // Special
    public const INFORM_REQUEST = 6;
    public const TRAP_V2 = 7; // ?
    public const REPORT = 8;

    protected int $errorStatus = 0; // error-status: noError(0)
    protected int $errorIndex = 0;
    protected bool $wantsResponse = false;

    /**
     * @param VarBind[] $varBinds
     */
    public function __construct(
        public readonly array $varBinds,
        public ?int $requestId = null
    ) {
    }

    abstract public function getTag(): int;

    public function wantsResponse(): bool
    {
        return $this->wantsResponse;
    }

    public function isError(): bool
    {
        return $this->errorStatus !== 0;
    }

    public function toASN1(): ImplicitlyTaggedType
    {
        if ($this->requestId === null) {
            throw new RuntimeException('Cannot created ASN1 type w/o requiestId');
        }
        return new ImplicitlyTaggedType($this->getTag(), new Sequence(
            new Integer($this->requestId),
            new Integer($this->errorStatus),
            new Integer($this->errorIndex),
            VarBind::listToSequence($this->varBinds)
        ));
    }

    public static function fromASN1(TaggedType $tagged): Pdu
    {
        $sequence = $tagged->asImplicit(Element::TYPE_SEQUENCE)->asSequence();
        // $sequence->count() === 4;
        $varBinds = VarBind::listFromSequence($sequence->at(3)->asSequence());
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
