<?php

use gipfl\Protocol\Snmp\DataType\DataType;
use gipfl\Protocol\Snmp\Socket;
use React\EventLoop\Loop;

require dirname(__DIR__) . '/vendor/autoload.php';

$socket = new Socket();
$community = 'public';
$ips = [
    '192.0.2.1',
    '192.0.2.2',
];
$oidList = [
    '1.3.6.1.2.1.1.1.0' => 'sysDescr',
    '1.3.6.1.2.1.1.2.0' => 'sysObjectID',
    '1.3.6.1.2.1.1.3.0' => 'sysUpTime',
    '1.3.6.1.2.1.1.4.0' => 'sysContact',
    '1.3.6.1.2.1.1.5.0' => 'sysName',
    '1.3.6.1.2.1.1.6.0' => 'sysLocation',
    '1.3.6.1.2.1.1.7.0' => 'sysServices',
];

foreach ($ips as $ip) {
    $socket->get($oidList, $ip, $community)->then(function ($result) {
        /** @var DataType $value */
        foreach ($result as $key => $value) {
            printf("%s: %s\n", $key, $value->getReadableValue());
        }
    })->otherwise(function ($reason) use ($ip) {
        echo "No Response from $ip: $reason\n";
    })->always(function () use ($socket, $ips) {
        if (! $socket->hasPendingRequests()) {
            printf("Done with %d IPs\n", count($ips));
            Loop::stop();
        }
    });
}

Loop::run();
