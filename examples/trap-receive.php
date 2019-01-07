<?php

use gipfl\Protocol\Snmp\Socket;
use gipfl\Protocol\Snmp\SnmpMessage;
use gipfl\Protocol\Snmp\SnmpMessageInspector;
use React\EventLoop\Factory;

require dirname(__DIR__) . '/vendor/autoload.php';

$loop = Factory::create();
$socket = new Socket('0.0.0.0', 162);
$socket->run($loop)->otherwise(function (Exception $e) use ($loop) {
    $loop->stop();
    echo $e->getMessage() . "\n";
    exit(1);
});

$cnt = 0;
$lastTime = 0;
$reported = 0;
$showReport = function () use (& $cnt, & $reported) {
    printf(
        "%s: got %d traps (total: %s)\n",
        date('Y-m-d H:i:s'),
        $cnt - $reported,
        $cnt
    );
    $reported = $cnt;
};
$term = function () use ($loop, & $showReport) {
    $showReport();
    $loop->stop();
};
$loop->addSignal(2, $term);
$loop->addPeriodicTimer(1, $showReport);

$socket->on('trap', function (SnmpMessage $trap, $peer) use (& $cnt, & $lastTime) {
    $cnt++;
    printf(
        "Got trap from %s:\n%s\n",
        $peer,
        SnmpMessageInspector::getDump($trap)
    );
});
$loop->run();
