<?php

use gipfl\Protocol\Snmp\DataType\Integer32;
use gipfl\Protocol\Snmp\DataType\ObjectIdentifier;
use gipfl\Protocol\Snmp\DataType\OctetString;
use gipfl\Protocol\Snmp\Socket;
use gipfl\Protocol\Snmp\SnmpV2Message;
use gipfl\Protocol\Snmp\TrapV2;
use gipfl\Protocol\Snmp\VarBind;
use gipfl\Protocol\Snmp\VarBinds;
use React\EventLoop\Loop;

const TRAP_OID = '1.3.6.1.6.3.1.1.4.1';
const SYS_UPTIME = '1.3.6.1.2.1.1.3';
const LINK_UP = '1.3.6.1.6.3.1.1.5.4';
const IF_DESCR = '1.3.6.1.2.1.2.2.1.2';
const IF_ADMIN_STATUS = '1.3.6.1.2.1.2.2.1.7';
const IF_OPER_STATUS  = '1.3.6.1.2.1.2.2.1.8';

require dirname(__DIR__) . '/vendor/autoload.php';

$startTime = time();
$socket = new Socket();
$community = 'public';

$newTrap = function ($id) use ($community, $startTime) {
    //$uptime = time() - $startTime;
    $varBinds = new VarBinds(
        // new VarBind(SYS_UPTIME, $uptime),
        new VarBind(TRAP_OID, ObjectIdentifier::fromString(LINK_UP)),
        new VarBind(IF_DESCR, OctetString::fromString('eth0')),
        new VarBind(IF_ADMIN_STATUS, Integer32::fromInteger(1)),
        new VarBind(IF_OPER_STATUS, Integer32::fromInteger(1))
    );

    return new SnmpV2Message($community, new TrapV2($varBinds, $id));
};

$i = 0;
$reported = 0;
$debug = false;
$target = '127.0.0.1';

$send = function () use ($socket, &$newTrap, &$i, &$send, $target) {
    for ($a = 0; $a < 100; $a ++) {
        $i++;
        $trap = $newTrap($i);
        Loop::futureTick(function () use ($socket, $trap, $target) {
            $socket->sendTrap($trap, $target);
        });
    }
};
$showReport = function () use (&$i, &$reported, $debug) {
    printf(
        "%s: sent %d Traps (total: %d)\n",
        date('H:i:s'),
        $i - $reported,
        $i
    );

    $reported = $i;
};
$term = function () use (&$showReport) {
    $showReport();
    Loop::stop();
};
Loop::addSignal(2, $term);
Loop::addPeriodicTimer(0.05, $send);
Loop::futureTick($send);
Loop::addPeriodicTimer(1, $showReport);
Loop::addTimer(15, function () use ($showReport) {
    $showReport();
    Loop::stop();
});
Loop::run();
