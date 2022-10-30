<?php

namespace gipfl\Protocol\Snmp;

use React\EventLoop\Loop;
use React\Datagram\Socket as DatagramSocket;
use RuntimeException;
use function stream_socket_server;
use const STREAM_SERVER_BIND;

class UdpSocketFactory
{
    private const SOCKET_CONTEXT = 'socket';
    private const SOCKET_BACKLOG = 'backlog';
    private const SOCKET_BROADCAST = 'so_broadcast';
    private const SOCKET_REUSE_PORT = 'so_reuseport';

    public static function prepareUdpSocket(SocketAddress $socketAddress): DatagramSocket
    {
        return static::prepareUdpSocketWithContext($socketAddress, self::prepareStreamContext($socketAddress));
    }

    public static function prepareBroadcastingUdpSocket(SocketAddress $socketAddress): DatagramSocket
    {
        return static::prepareUdpSocketWithContext($socketAddress, self::prepareBroadcastStreamContext($socketAddress));
    }

    protected static function prepareUdpSocketWithContext(SocketAddress $socketAddress, $context): DatagramSocket
    {
        $socket = @stream_socket_server(
            $socketAddress->toUdpUri(),
            $errNo,
            $errStr,
            STREAM_SERVER_BIND,
            $context
        );

        if (!$socket) {
            throw new RuntimeException('Unable to create server socket: ' . $errStr, $errNo);
        }

        $s = new DatagramSocket(Loop::get(), $socket);
        $s->bufferSize = 4096 * 4096;
        return $s;
    }

    /**
     * @param SocketAddress $socketAddress
     * @return resource
     */
    protected static function prepareStreamContext(SocketAddress $socketAddress)
    {
        return stream_context_create([
            self::SOCKET_CONTEXT => [
                self::SOCKET_REUSE_PORT => 1,
                self::SOCKET_BACKLOG    => 4096, // less than /proc/sys/net/core/somaxconn
            ]
        ]);
    }

    /**
     * @param SocketAddress $socketAddress
     * @return resource
     */
    protected static function prepareBroadcastStreamContext(SocketAddress $socketAddress)
    {
        $options = [
            self::SOCKET_CONTEXT => [
                self::SOCKET_BROADCAST => 1,
                self::SOCKET_REUSE_PORT => 1,
                self::SOCKET_BACKLOG    => 4096, // less than /proc/sys/net/core/somaxconn
            ]
        ];

        return stream_context_create($options);
    }
}
