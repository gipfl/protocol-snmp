<?php

namespace gipfl\Protocol\Snmp;

class SimpleRequestIdGenerator
{
    /** @var RequestIdConsumer[] */
    protected array $consumers = [];

    public function registerConsumer(RequestIdConsumer $consumer): void
    {
        $this->consumers[spl_object_id($consumer)] = $consumer;
    }

    public function getNextId(): int
    {
        while (true) {
            $id = rand(1, 1000000000);
            foreach ($this->consumers as $consumer) {
                if ($consumer->hasId($id)) {
                    continue 2;
                }
            }

            break;
        }

        return $id;
    }
}
