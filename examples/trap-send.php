<?php

use gipfl\Protocol\Snmp\DataType\Integer32;
use gipfl\Protocol\Snmp\DataType\ObjectIdentifier;
use gipfl\Protocol\Snmp\DataType\OctetString;
use gipfl\Protocol\Snmp\Socket;
use gipfl\Protocol\Snmp\SnmpV2Message;
use gipfl\Protocol\Snmp\TrapV2;
use gipfl\Protocol\Snmp\VarBind;
use gipfl\Protocol\Snmp\VarBinds;
use React\EventLoop\Factory;

const TRAP_OID = '1.3.6.1.6.3.1.1.4.1';
const SYS_UPTIME = '1.3.6.1.2.1.1.3';
const LINK_UP = '1.3.6.1.6.3.1.1.5.4';
const IF_DESCR = '1.3.6.1.2.1.2.2.1.2';
const IF_ADMIN_STATUS = '1.3.6.1.2.1.2.2.1.7';
const IF_OPER_STATUS  = '1.3.6.1.2.1.2.2.1.8';

require dirname(__DIR__) . '/vendor/autoload.php';

$startTime = time();
$loop = Factory::create();
$socket = new Socket();
$socket->run($loop);
$community = 'public';

$newTrap = function ($id) use ($community, $startTime) {
    $uptime = time() - $startTime;
    $varbinds = new VarBinds(
        // new VarBind(SYS_UPTIME, $uptime),
        new VarBind(TRAP_OID, ObjectIdentifier::fromString(LINK_UP)),
        new VarBind(IF_DESCR, OctetString::fromString('eth0')),
        new VarBind(IF_ADMIN_STATUS, Integer32::fromInteger(1)),
        new VarBind(IF_OPER_STATUS, Integer32::fromInteger(1))
    );

    return new SnmpV2Message($community, new TrapV2($varbinds, $id));
};

$i = 0;
$reported = 0;
$debug = false;
$target = '127.0.0.1';

$send = function () use ($socket, & $newTrap, & $i, $loop, & $send, $target) {
    for ($a = 0; $a < 30; $a ++) {
        $i++;
        $trap = $newTrap($i);
        $socket->sendTrap($trap, $target);
    }
};
$showReport = function () use (& $i, & $reported, $debug) {
    printf(
        "%s: sent %d Traps (total: %d)\n",
        date('H:i:s'),
        $i - $reported,
        $i
    );

    $reported = $i;
};
$term = function () use ($loop, & $showReport) {
    $showReport();
    $loop->stop();
};
$loop->addSignal(2, $term);
$loop->addPeriodicTimer(0.5, $send);
$loop->futureTick($send);
$loop->addPeriodicTimer(1, $showReport);
$loop->addTimer(60, function () use ($loop) {
    $loop->stop();
});
$loop->run();
