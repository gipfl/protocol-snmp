<?php

namespace gipfl\Protocol\Snmp;

class ErrorStatus
{
    const NO_ERROR = 0;
    const TOO_BIG = 1;
    // Hint: noSuchName, badValue and readOnly are here for proxy compatibility
    const NO_SUCH_NAME = 2;
    const BAD_VALUE = 3;
    const READ_ONLY = 4;
    const GEN_ERR = 5;
    const NO_ACCESS = 6;
    const WRONG_TYPE = 7;
    const WRONG_LENGTH = 8;
    const WRONG_ENCODING = 9;
    const WRONG_VALUE = 10;
    const NO_CREATION = 11;
    const INCONSISTENT_VALUE = 12;
    const RESOURCE_UNAVAILABLE = 13;
    const COMMIT_FAILED = 14;
    const UNDO_FAILED = 15;
    const AUTHORIZATION_ERROR = 16;
    const NOT_WRITABLE = 17;
    const INCONSISTENT_NAME = 18;

    const ERROR_TO_NAME_MAP = [
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

    protected $status;

    public function __construct($status)
    {
        if (\array_key_exists($status, self::ERROR_TO_NAME_MAP)) {
            $this->status = (int) $status;
        } else {
            throw new \RuntimeException("$status is not a valid ErrorStatus");
        }
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusName()
    {
        return self::ERROR_TO_NAME_MAP[$this->status];
    }
}
