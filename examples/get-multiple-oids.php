<?php

use gipfl\Protocol\Snmp\Socket;
use React\EventLoop\Factory;

require dirname(__DIR__) . '/vendor/autoload.php';

$loop = Factory::create();
$socket = new Socket();
$socket->run($loop);

$community = 'public';
$ips = [
    '192.0.2.1',
    '192.0.2.2',
];
$oids = [
    '1.3.6.1.2.1.1.1.0' => 'sysDescr',
    '1.3.6.1.2.1.1.2.0' => 'sysObjectID',
    '1.3.6.1.2.1.1.3.0' => 'sysUpTime',
    '1.3.6.1.2.1.1.4.0' => 'sysContact',
    '1.3.6.1.2.1.1.5.0' => 'sysName',
    '1.3.6.1.2.1.1.6.0' => 'sysLocation',
    '1.3.6.1.2.1.1.7.0' => 'sysServices',
];

foreach ($ips as $ip) {
    $socket->get($oids, $ip, $community)->then(function ($result) {
        /** @var \gipfl\Protocol\Snmp\DataType $value */
        foreach ($result as $key => $value) {
            printf("%s: %s\n", $key, $value->getReadableValue());
        }
    })->otherwise(function ($reason) use ($ip) {
        echo "No Response from $ip: $reason\n";
    })->always(function () use ($socket, $ips, $loop) {
        if (! $socket->hasPendingRequests()) {
            printf("Done with %d IPs\n", count($ips));
            $loop->stop();
        }
    });
}

$loop->run();
