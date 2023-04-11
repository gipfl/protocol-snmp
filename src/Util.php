<?php

namespace gipfl\Protocol\Snmp;

class Util
{
    protected static int $hexDumpWidth = 16;

    /**
     * @param string $data
     * @param string $newline
     * @param int $width Number of Bytes per line
     * @param string $pad Padding for non-visible characters
     */
    public static function hexDump(string $data, string $newline = "\n", int $width = 16, string $pad = '.'): void
    {
        if ($width < 1) {
            throw new \RuntimeException('hexDump: $width needs to be greater than 1, got ' . $width);
        }
        $from = '';
        $to = '';

        if ($from === '') { // TODO: figure out, why this useless check is in place
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

    protected static function renderChunkedHex(string $line): string
    {
        $parts = array_chunk(str_split($line, 2), 4);

        $result = [];
        foreach ($parts as $part) {
            $result[] = implode(' ', $part);
        }

        return implode('  ', $result);
    }
}
