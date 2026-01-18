<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KycService
{
    private $monnifyService;

    public function __construct(MonnifyService $monnifyService)
    {
        $this->monnifyService = $monnifyService;
    }

    /**
     * Verify NIN via Monnify VAS API
     */
    public function verifyNin(string $nin): array
    {
        try {
            $accessToken = $this->monnifyService->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Authentication failed'];
            }

            // Always use production API
            $baseUrl = 'https://api.monnify.com';

            Log::info('Verifying NIN', ['nin' => substr($nin, 0, 3) . '********']);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/api/v1/vas/nin-details', [
                    'nin' => $nin
                ]);

            // Check for expired token and retry
            if ($response->status() === 401) {
                $responseBody = $response->body();
                if (strpos($responseBody, 'expired') !== false || strpos($responseBody, 'invalid_token') !== false) {
                    Log::warning('NIN verification token expired, retrying with fresh token...');

                    // Get fresh token and retry
                    $accessToken = $this->monnifyService->getAccessToken(true);
                    if (!$accessToken) {
                        return ['success' => false, 'message' => 'Failed to refresh authentication'];
                    }

                    $response = Http::timeout(30)
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ])
                        ->post($baseUrl . '/api/v1/vas/nin-details', [
                            'nin' => $nin
                        ]);
                }
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/api/v1/vas/nin-details', [
                    'nin' => $nin
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('NIN verification API response', [
                    'success' => $data['requestSuccessful'] ?? false,
                    'code' => $data['responseCode'] ?? 'N/A'
                ]);

                if (isset($data['requestSuccessful']) && $data['requestSuccessful']) {
                    return [
                        'success' => true,
                        'message' => 'NIN verified successfully',
                        'data' => $data['responseBody'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['responseMessage'] ?? 'NIN verification failed'
                ];
            }

            Log::error('NIN verification failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'NIN verification service unavailable. Please try again.'
            ];

        } catch (\Exception $e) {
            Log::error('NIN verification error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Verification service temporarily unavailable'
            ];
        }
    }

    /**
     * Verify BVN via Monnify VAS API
     */
    public function verifyBvn(string $bvn, string $name, string $dob, string $mobileNo): array
    {
        try {
            $accessToken = $this->monnifyService->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Authentication failed'];
            }

            // Always use production API
            $baseUrl = 'https://api.monnify.com';

            Log::info('Verifying BVN', [
                'bvn' => substr($bvn, 0, 3) . '********',
                'name' => $name
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/api/v1/vas/bvn-details-match', [
                    'bvn' => $bvn,
                    'name' => $name,
                    'dateOfBirth' => $dob,
                    'mobileNo' => $mobileNo
                ]);

            // Check for expired token and retry
            if ($response->status() === 401) {
                $responseBody = $response->body();
                if (strpos($responseBody, 'expired') !== false || strpos($responseBody, 'invalid_token') !== false) {
                    Log::warning('BVN verification token expired, retrying with fresh token...');

                    // Get fresh token and retry
                    $accessToken = $this->monnifyService->getAccessToken(true);
                    if (!$accessToken) {
                        return ['success' => false, 'message' => 'Failed to refresh authentication'];
                    }

                    $response = Http::timeout(30)
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ])
                        ->post($baseUrl . '/api/v1/vas/bvn-details-match', [
                            'bvn' => $bvn,
                            'name' => $name,
                            'dateOfBirth' => $dob,
                            'mobileNo' => $mobileNo
                        ]);
                }
            }

            if ($response->successful()) {
                $data = $response->json();

                Log::info('BVN verification API response', [
                    'success' => $data['requestSuccessful'] ?? false,
                    'code' => $data['responseCode'] ?? 'N/A'
                ]);

                if (isset($data['requestSuccessful']) && $data['requestSuccessful']) {
                    return [
                        'success' => true,
                        'message' => 'BVN verified successfully',
                        'data' => $data['responseBody'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['responseMessage'] ?? 'BVN verification failed'
                ];
            }

            Log::error('BVN verification failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'BVN verification service unavailable. Please try again.'
            ];

        } catch (\Exception $e) {
            Log::error('BVN verification error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Verification service temporarily unavailable'
            ];
        }
    }

    /**
     * Save KYC data to user profile
     */
    public function saveKycData(User $user, array $kycData): bool
    {
        try {
            $updateData = [
                'kyc_status' => 'verified',
                'kyc_verified_at' => Carbon::now(),
                'kyc_method' => $kycData['method'] ?? 'none'
            ];

            if (isset($kycData['nin'])) {
                $updateData['nin'] = $kycData['nin'];
            }

            if (isset($kycData['bvn'])) {
                $updateData['bvn'] = $kycData['bvn'];
            }

            if (isset($kycData['date_of_birth'])) {
                $updateData['date_of_birth'] = $kycData['date_of_birth'];
            }

            $user->update($updateData);

            Log::info('KYC data saved for user', [
                'user_id' => $user->id,
                'method' => $kycData['method'] ?? 'unknown'
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to save KYC data: ' . $e->getMessage(), [
                'user_id' => $user->id ?? 'unknown',
                'exception' => get_class($e)
            ]);

            return false;
        }
    }

    /**
     * Check if user has completed KYC
     */
    public function hasCompletedKyc(User $user): bool
    {
        return $user->kyc_status === 'verified' && (!empty($user->nin) || !empty($user->bvn));
    }

    /**
     * Check if user needs KYC
     */
    public function needsKyc(User $user): bool
    {
        return empty($user->nin) && empty($user->bvn);
    }
}
