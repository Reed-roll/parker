<?php

namespace App\Libraries;

class PaymentClient
{
    protected $baseUrl;
    protected $key;

    public function __construct()
    {
        $this->baseUrl = env('PAYMENT_SERVICE_URL') ?? null;
        $this->key = env('PAYMENT_SERVICE_KEY') ?? null;
    }

    /**
     * Charge the payment microservice. Returns array with at least 'status' and 'transaction_id'.
     * If PAYMENT_SERVICE_URL isn't configured, simulate a successful charge.
     */
    public function charge(array $payload): array
    {
        if (empty($this->baseUrl)) {
            // Simulate
            return [
                'status' => 'completed',
                'transaction_id' => 'sim_' . uniqid(),
                'raw' => $payload,
            ];
        }

        $url = rtrim($this->baseUrl, '/') . '/charge';

        $ch = curl_init($url);
        $body = json_encode($payload);
        $headers = ['Content-Type: application/json'];
        if ($this->key) {
            $headers[] = 'Authorization: Bearer ' . $this->key;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false || $httpCode >= 500) {
            return ['status' => 'failed', 'transaction_id' => null, 'error' => $err ?: $resp];
        }

        $data = json_decode($resp, true);
        if (!is_array($data)) {
            return ['status' => 'failed', 'transaction_id' => null, 'error' => 'invalid_response'];
        }

        return $data + ['raw_response' => $resp];
    }
}
