<?php

namespace gipfl\Protocol\Snmp;

class ErrorStatus
{
    public static $messages = [
        0 => 'noError',
        1 => 'tooBig',
        2 => 'noSuchName', // for proxy compatibility
        3 => 'badValue',   // for proxy compatibility
        4 => 'readOnly',   // for proxy compatibility
        5 => 'genErr',
        6 => 'noAccess',
        7 => 'wrongType',
        8 => 'wrongLength',
        9 => 'wrongEncoding',
        10 => 'wrongValue',
        11 => 'noCreation',
        12 => 'inconsistentValue',
        13 => 'resourceUnavailable',
        14 => 'commitFailed',
        15 => 'undoFailed',
        16 => 'authorizationError',
        17 => 'notWritable',
        18 => 'inconsistentName',
    ];
}
