gipfl\\Snmp - SNMP library
==========================

SNMP protocol implementation in raw PHP for async usage. API is still subject
to change, SNMPv3 is currently missing, but will be implemented.


[![Coding Standards](https://github.com/gipfl/protocol-snmp/actions/workflows/CodingStandards.yml/badge.svg)](https://github.com/gipfl/protocol-snmp/actions/workflows/CodingStandards.yml)
[![Unit Tests](https://github.com/gipfl/protocol-snmp/actions/workflows/UnitTests.yml/badge.svg)](https://github.com/gipfl/protocol-snmp/actions/workflows/UnitTests.yml)
[![Static Analysis](https://github.com/gipfl/protocol-snmp/actions/workflows/StaticAnalysis.yml/badge.svg)](https://github.com/gipfl/protocol-snmp/actions/workflows/StaticAnalysis.yml)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://phpstan.org/)
[![Minimum PHP Version: 8.1](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)
[![License: MIT](https://poser.pugx.org/gipfl/protocol-snmp/license)](https://choosealicense.com/licenses/mit/)
[![Version](https://poser.pugx.org/gipfl/protocol-snmp/version)](https://packagist.org/packages/gipfl/protocol-snmp)


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
