<?php

namespace App\Support;

class Totp
{
    public static function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($data) as $c) {
            $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            if (strlen($chunk) < 5) $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $out .= $alphabet[bindec($chunk)];
        }
        return $out;
    }

    public static function base32Decode(string $base32): string
    {
        $alphabet = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $bits = '';
        $base32 = strtoupper($base32);
        foreach (str_split($base32) as $c) {
            if (!isset($alphabet[$c])) continue;
            $bits .= str_pad(decbin($alphabet[$c]), 5, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) < 8) continue;
            $out .= chr(bindec($chunk));
        }
        return $out;
    }

    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function hotp(string $secretBase32, int $counter, int $digits = 6): string
    {
        $secret = self::base32Decode($secretBase32);
        $binCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binCounter, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncated = (ord($hash[$offset]) & 0x7F) << 24 |
            (ord($hash[$offset + 1]) & 0xFF) << 16 |
            (ord($hash[$offset + 2]) & 0xFF) << 8 |
            (ord($hash[$offset + 3]) & 0xFF);
        $code = $truncated % (10 ** $digits);
        return str_pad((string)$code, $digits, '0', STR_PAD_LEFT);
    }

    public static function totp(string $secretBase32, int $period = 30, int $digits = 6, ?int $time = null): string
    {
        $time = $time ?? time();
        $counter = intdiv($time, $period);
        return self::hotp($secretBase32, $counter, $digits);
    }

    public static function verify(string $secretBase32, string $code, int $window = 1, int $period = 30, int $digits = 6): bool
    {
        $time = time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::totp($secretBase32, $period, $digits, $time + ($i * $period)), $code)) {
                return true;
            }
        }
        return false;
    }
}

