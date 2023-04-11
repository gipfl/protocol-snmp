<?php

namespace gipfl\Protocol\Snmp;

use InvalidArgumentException;

class ErrorStatus
{
    public const NO_ERROR = 0;
    public const TOO_BIG = 1;
    // Hint: noSuchName, badValue and readOnly are here for proxy compatibility
    public const NO_SUCH_NAME = 2;
    public const BAD_VALUE = 3;
    public const READ_ONLY = 4;
    public const GEN_ERR = 5;
    public const NO_ACCESS = 6;
    public const WRONG_TYPE = 7;
    public const WRONG_LENGTH = 8;
    public const WRONG_ENCODING = 9;
    public const WRONG_VALUE = 10;
    public const NO_CREATION = 11;
    public const INCONSISTENT_VALUE = 12;
    public const RESOURCE_UNAVAILABLE = 13;
    public const COMMIT_FAILED = 14;
    public const UNDO_FAILED = 15;
    public const AUTHORIZATION_ERROR = 16;
    public const NOT_WRITABLE = 17;
    public const INCONSISTENT_NAME = 18;

    protected const ERROR_TO_NAME_MAP = [
        self::NO_ERROR             => 'noError',
        self::TOO_BIG              => 'tooBig',
        self::NO_SUCH_NAME         => 'noSuchName',
        self::BAD_VALUE            => 'badValue',
        self::READ_ONLY            => 'readOnly',
        self::GEN_ERR              => 'genErr',
        self::NO_ACCESS            => 'noAccess',
        self::WRONG_TYPE           => 'wrongType',
        self::WRONG_LENGTH         => 'wrongLength',
        self::WRONG_ENCODING       => 'wrongEncoding',
        self::WRONG_VALUE          => 'wrongValue',
        self::NO_CREATION          => 'noCreation',
        self::INCONSISTENT_VALUE   => 'inconsistentValue',
        self::RESOURCE_UNAVAILABLE => 'resourceUnavailable',
        self::COMMIT_FAILED        => 'commitFailed',
        self::UNDO_FAILED          => 'undoFailed',
        self::AUTHORIZATION_ERROR  => 'authorizationError',
        self::NOT_WRITABLE         => 'notWritable',
        self::INCONSISTENT_NAME    => 'inconsistentName',
    ];

    public function __construct(
        protected int $status
    ) {
        if (\array_key_exists($status, self::ERROR_TO_NAME_MAP)) {
            $this->status = $status;
        } else {
            throw new InvalidArgumentException("$status is not a valid ErrorStatus");
        }
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getStatusName(): string
    {
        return self::ERROR_TO_NAME_MAP[$this->status];
    }
}
