<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackTransferService
{
    private string $baseUrl = 'https://api.paystack.co';
    private ?string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey);
    }

    public function createRecipient(string $accountName, string $accountNumber, string $bankCode): array
    {
        $response = Http::withToken($this->secretKey)
            ->post($this->baseUrl . '/transferrecipient', [
                'type' => 'nuban',
                'name' => $accountName,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'currency' => 'NGN',
            ]);

        $json = $response->json();

        if (!$response->successful() || !($json['status'] ?? false)) {
            return [
                'success' => false,
                'message' => $json['message'] ?? 'Unable to create transfer recipient.',
                'data' => $json['data'] ?? null,
            ];
        }

        return [
            'success' => true,
            'message' => $json['message'] ?? 'Recipient created successfully.',
            'data' => $json['data'] ?? [],
        ];
    }

    public function initiateTransfer(float $amount, string $recipientCode, string $reference, ?string $reason = null): array
    {
        $response = Http::withToken($this->secretKey)
            ->post($this->baseUrl . '/transfer', [
                'source' => 'balance',
                'amount' => (int) round($amount * 100), // Kobo
                'recipient' => $recipientCode,
                'reference' => $reference,
                'reason' => $reason ?? 'Vendor payout transfer',
                'currency' => 'NGN',
            ]);

        $json = $response->json();

        if (!$response->successful() || !($json['status'] ?? false)) {
            return [
                'success' => false,
                'message' => $json['message'] ?? 'Transfer initiation failed.',
                'data' => $json['data'] ?? null,
            ];
        }

        return [
            'success' => true,
            'message' => $json['message'] ?? 'Transfer initiated.',
            'data' => $json['data'] ?? [],
        ];
    }

    public function resolveBankCodeByName(string $bankName): ?string
    {
        $response = Http::withToken($this->secretKey)
            ->get($this->baseUrl . '/bank', [
                'country' => 'nigeria',
                'currency' => 'NGN',
            ]);

        $json = $response->json();
        if (!$response->successful() || !($json['status'] ?? false)) {
            return null;
        }

        $normalizedNeedle = mb_strtolower(trim($bankName));
        foreach (($json['data'] ?? []) as $bank) {
            $name = mb_strtolower(trim((string) ($bank['name'] ?? '')));
            if ($name === $normalizedNeedle) {
                return (string) ($bank['code'] ?? '');
            }
        }

        foreach (($json['data'] ?? []) as $bank) {
            $name = mb_strtolower(trim((string) ($bank['name'] ?? '')));
            if (str_contains($name, $normalizedNeedle) || str_contains($normalizedNeedle, $name)) {
                return (string) ($bank['code'] ?? '');
            }
        }

        return null;
    }
}
