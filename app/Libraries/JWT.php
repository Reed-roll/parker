<?php
namespace App\Libraries;

class JWT
{
    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) $data .= str_repeat('=', 4 - $remainder);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function encode(array $payload, string $secret, int $expSeconds = 3600): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = array_merge($payload, ['iat' => time(), 'exp' => time() + $expSeconds]);

        $segments = [];
        $segments[] = self::base64UrlEncode(json_encode($header));
        $segments[] = self::base64UrlEncode(json_encode($payload));
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $secret, true);
        $segments[] = self::base64UrlEncode($signature);
        return implode('.', $segments);
    }

    public static function decode(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$headb64, $bodyb64, $sigb64] = $parts;
        $header = json_decode(self::base64UrlDecode($headb64), true);
        $payload = json_decode(self::base64UrlDecode($bodyb64), true);
        $sig = self::base64UrlDecode($sigb64);
        $verified = hash_equals(hash_hmac('sha256', $headb64.'.'.$bodyb64, $secret, true), $sig);
        if (!$verified) return null;
        if (isset($payload['exp']) && time() > (int)$payload['exp']) return null;
        return $payload;
    }
}
