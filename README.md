gipfl\\Snmp - SNMP library
==========================

SNMP protocol implementation in raw PHP for async usage. API is still subject
to change, SNMPv3 is currently missing, but will be implemented.

Usage
-----

Please see the `examples` directory for some usage examples:

* `get_multiple_oids.php` fetches a list of OIDs in parallel from multiple ips
* `trap-recieve.php` is a simple Trap receiver
* `trap-send.php` sends 60 test-traps a second to test the receiver

Tuning
------

Network Stack buffer sizes might be increased for high-traffic setups:

```
$ sudo sysctl -w net.core.rmem_max=26214400
net.core.rmem_max = 26214400
$ sudo sysctl -w net.core.rmem_default=26214400
net.core.rmem_default = 26214400
```
