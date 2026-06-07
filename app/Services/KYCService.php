<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KYCService
{
    /**
     * Perform automated BVN and NIN verification.
     * Supports a free Sandbox/Mock mode for development and testing,
     * and integrates with Paystack's official Identity Verification API for production.
     *
     * @return array ['success' => bool, 'message' => string, 'auto_verified' => bool]
     */
    public static function autoVerify(?string $nin, ?string $bvn, string $fullName): array
    {
        $nin = trim($nin ?? '');
        $bvn = trim($bvn ?? '');

        if (empty($nin) && empty($bvn)) {
            return [
                'success' => false,
                'message' => 'NIN or BVN must be provided for automated verification.',
                'auto_verified' => false,
            ];
        }

        // 1. Sandbox / Development / Testing Mode (100% Free)
        if (config('app.env') !== 'production') {
            Log::info("KYC Service: Sandbox Auto-Verification triggered for '{$fullName}'");

            // Mock validation: NIN/BVN must be 11 digits
            if (! empty($nin) && strlen($nin) !== 11) {
                return [
                    'success' => false,
                    'message' => 'Sandbox Fail: NIN must be exactly 11 digits.',
                    'auto_verified' => false,
                ];
            }
            if (! empty($bvn) && strlen($bvn) !== 11) {
                return [
                    'success' => false,
                    'message' => 'Sandbox Fail: BVN must be exactly 11 digits.',
                    'auto_verified' => false,
                ];
            }

            return [
                'success' => true,
                'message' => 'Auto-verified successfully via BuyNiger Sandbox Engine.',
                'auto_verified' => true,
            ];
        }

        // 2. Production Mode: Paystack Identity Verification API Integration
        // Since NIMC and NIBSS charge lookup fees, big companies use Paystack/Monnify verification APIs.
        $paystackSecret = config('services.paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY');
        if (empty($paystackSecret)) {
            Log::warning('KYC Service: Paystack secret key not configured in production. Falling back to manual admin review.');

            return [
                'success' => false,
                'message' => 'Production verification API is not configured. Falling back to manual review.',
                'auto_verified' => false,
            ];
        }

        try {
            // Split user name to match against Paystack response details
            $nameParts = explode(' ', strtolower(trim($fullName)));

            // We verify whichever document number is supplied (preferring BVN)
            $type = ! empty($bvn) ? 'bvn' : 'nin';
            $value = ! empty($bvn) ? $bvn : $nin;

            // Paystack Identity Verification Endpoint
            $response = Http::withToken($paystackSecret)
                ->timeout(15)
                ->post('https://api.paystack.co/verification/identity', [
                    'type' => $type,
                    'value' => $value,
                ]);

            if ($response->failed()) {
                Log::error('KYC Service: Paystack verification failed: '.$response->body());

                return [
                    'success' => false,
                    'message' => 'Verification server returned an error. Falling back to manual review.',
                    'auto_verified' => false,
                ];
            }

            $result = $response->json();

            if (! data_get($result, 'status')) {
                return [
                    'success' => false,
                    'message' => data_get($result, 'message', 'Verification failed.'),
                    'auto_verified' => false,
                ];
            }

            // Paystack returns verified identity details: first_name, last_name, etc.
            $verifiedFirstName = strtolower(data_get($result, 'data.first_name', ''));
            $verifiedLastName = strtolower(data_get($result, 'data.last_name', ''));

            // Check if name details match our vendor full name to prevent fraud (e.g. using someone else's BVN/NIN)
            $firstNameMatched = false;
            $lastNameMatched = false;

            foreach ($nameParts as $part) {
                if ($part === $verifiedFirstName) {
                    $firstNameMatched = true;
                }
                if ($part === $verifiedLastName) {
                    $lastNameMatched = true;
                }
            }

            if ($firstNameMatched && $lastNameMatched) {
                return [
                    'success' => true,
                    'message' => 'Auto-verified successfully via Paystack API.',
                    'auto_verified' => true,
                ];
            }

            Log::warning("KYC Service: Name mismatch in auto-verification. Submitted: '{$fullName}', Returned: '{$verifiedFirstName} {$verifiedLastName}'");

            return [
                'success' => false,
                'message' => 'Identity name mismatch. Please ensure details match your official registration documents.',
                'auto_verified' => false,
            ];

        } catch (\Exception $e) {
            Log::error('KYC Service: Verification Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'An exception occurred during verification. Falling back to manual review.',
                'auto_verified' => false,
            ];
        }
    }
}
