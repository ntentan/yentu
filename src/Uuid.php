<?php

namespace yentu;

class Uuid
{
    /**
     * Generate an RFC 9562 UUIDv7.
     *
     * @param bool|null $asBinary When true, returns 16 raw bytes.
     *                            When false, returns 36-character hyphenated string.
     *                            When null, automatically formats for the active Yentu driver
     *                            (string for PostgreSQL, 16-byte binary for MySQL/SQLite).
     * @return string
     */
    public static function v7(?bool $asBinary = null): string
    {
        $msec = (int) (microtime(true) * 1000);
        $timeHex = str_pad(dechex($msec), 12, '0', STR_PAD_LEFT);

        $randomBytes = random_bytes(10);
        $randomBytes[0] = chr((ord($randomBytes[0]) & 0x0f) | 0x70); // version 7
        $randomBytes[2] = chr((ord($randomBytes[2]) & 0x3f) | 0x80); // variant RFC 9562

        $binary = hex2bin($timeHex) . $randomBytes;

        if ($asBinary === true) {
            return $binary;
        }

        $hex = bin2hex($binary);
        $uuidString = sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );

        if ($asBinary === false) {
            return $uuidString;
        }

        $driver = Yentu::getDriver();
        if ($driver !== null) {
            return $driver->formatUuid($uuidString);
        }

        return $uuidString;
    }

    /**
     * Convert a 36-character UUID string or 32-character hex into 16 raw bytes.
     */
    public static function toBinary(string $uuid): string
    {
        if (strlen($uuid) === 16) {
            return $uuid;
        }

        $cleaned = str_replace('-', '', $uuid);
        if (strlen($cleaned) === 32 && ctype_xdigit($cleaned)) {
            return hex2bin($cleaned);
        }

        throw new \InvalidArgumentException("Invalid UUID string: $uuid");
    }

    /**
     * Convert 16 raw bytes or 32-character hex into canonical 36-character hyphenated UUID string.
     */
    public static function toString(string $binaryOrHex): string
    {
        if (strlen($binaryOrHex) === 16) {
            $hex = bin2hex($binaryOrHex);
        } else {
            $hex = str_replace('-', '', $binaryOrHex);
        }

        if (strlen($hex) !== 32 || !ctype_xdigit($hex)) {
            throw new \InvalidArgumentException("Invalid binary or hex UUID");
        }

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    /**
     * Check if a string is a valid UUID format (36-char hyphenated or 32-char hex).
     */
    public static function isValid(string $uuid): bool
    {
        $cleaned = str_replace('-', '', $uuid);
        return strlen($cleaned) === 32 && ctype_xdigit($cleaned);
    }
}
