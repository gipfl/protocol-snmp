<?php

use gipfl\Protocol\Snmp\Socket;
use gipfl\Protocol\Snmp\SnmpMessage;
use gipfl\Protocol\Snmp\SnmpMessageInspector;
use gipfl\Protocol\Snmp\SocketAddress;
use React\EventLoop\Loop;

require dirname(__DIR__) . '/vendor/autoload.php';

$socket = new Socket(new SocketAddress('0.0.0.0', 162));
$cnt = 0;
$reported = 0;
$showReport = function () use (&$cnt, &$reported) {
    printf(
        "%s: got %d traps (total: %s)\n",
        date('Y-m-d H:i:s'),
        $cnt - $reported,
        $cnt
    );
    $reported = $cnt;
};
$term = function () use (&$showReport) {
    $showReport();
    Loop::stop();
};
Loop::addSignal(2, $term);
Loop::addPeriodicTimer(1, $showReport);
$socket->on(Socket::ON_TRAP, function (SnmpMessage $trap, $peer) use (&$cnt) {
    $cnt++;
    /*
    printf(
        "Got trap from %s:\n%s\n",
        $peer,
        SnmpMessageInspector::getDump($trap)
    );
    */
});
$socket->listen();
Loop::run();
