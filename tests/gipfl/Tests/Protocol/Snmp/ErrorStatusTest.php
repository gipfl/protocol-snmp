<?php

namespace gipfl\Tests\Protocol\Snmp;

use gipfl\Protocol\Snmp\ErrorStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ErrorStatusTest extends TestCase
{
    public function testNoErrorIsAnError()
    {
        $error = new ErrorStatus(0);
        $this->assertInstanceOf(ErrorStatus::class, $error);
    }

    public function testInvalidErrorNumberIsNotAccepted()
    {
        $this->expectException(InvalidArgumentException::class);
        $error = new ErrorStatus(42);
    }
}
