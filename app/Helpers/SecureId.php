<?php

namespace App\Helpers;

class SecureId
{
    private static string $salt = 'ts-secret-salt-2026';

    public static function encode(int|string $id, string $context = 'general'): string
    {
        if (!$id) return '';
        $data = $context . ':' . $id;
        $hash = hash_hmac('sha256', $data, config('app.key') ?: self::$salt);
        $shortHash = substr($hash, 0, 8);
        $payload = base64_encode($id . '.' . $shortHash . '.' . $context);
        return rtrim(strtr($payload, '+/', '-_'), '=');
    }

    public static function decode(string $token, string $context = 'general'): ?int
    {
        if (empty($token)) return null;
        
        // Fallback jika masih ada request dengan integer mentah
        if (is_numeric($token)) {
            return (int) $token;
        }

        $base64 = strtr($token, '-_', '+/');
        $decoded = base64_decode($base64, true);
        if (!$decoded) return null;

        $parts = explode('.', $decoded);
        if (count($parts) !== 3) return null;

        [$id, $shortHash, $ctx] = $parts;
        if ($ctx !== $context) return null;

        $expectedHash = substr(hash_hmac('sha256', $ctx . ':' . $id, config('app.key') ?: self::$salt), 0, 8);
        if (!hash_equals($expectedHash, $shortHash)) {
            return null;
        }

        return (int) $id;
    }
}