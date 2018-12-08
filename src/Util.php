<?php

namespace gipfl\Protocol\Snmp;

class Util
{
    protected static $hexDumpWidth = 16;

    /**
     * @param $data
     * @param string $newline
     * @param int $width Number of Bytes per line
     * @param string $pad Padding for non-visible characters
     */
    public static function hexDump($data, $newline = "\n", $width = 16, $pad = '.')
    {
        $from = '';
        $to = '';

        if ($from === '') {
            for ($i = 0; $i <= 0xFF; $i++) {
                $from .= chr($i);
                $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
            }
        }

        $hex = str_split(bin2hex($data), $width * 2);
        $chars = str_split(strtr($data, $from, $to), $width);

        $offset = 0;
        foreach ($hex as $i => $line) {
            $line = str_pad(strtoupper($line), $width * 2, ' ');
            echo sprintf('%04s', $offset)
                . ': '
                . static::renderChunkedHex($line)
                . '    ' . $chars[$i]
                . $newline;
            $offset += $width;
        }
    }

    protected static function renderChunkedHex($line)
    {
        $parts = array_chunk(str_split($line, 2), 4);

        $result = [];
        foreach ($parts as $part) {
            $result[] = implode(' ', $part);
        }

        return implode('  ', $result);
    }
}
