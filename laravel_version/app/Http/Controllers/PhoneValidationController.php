<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class PhoneValidationController extends Controller
{
    /**
     * Validate phone number and return network information
     */
    public function validatePhone(Request $request)
    {
        // Normalize network to lowercase
        if ($request->has('network')) {
            $request->merge(['network' => strtolower($request->network)]);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'network' => 'sometimes|string|in:mtn,airtel,glo,9mobile'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid phone number format. Must be 11 digits.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $phone = $request->phone;
            $expectedNetwork = $request->network;

            // Check if phone starts with country code
            if (substr($phone, 0, 3) === '234') {
                $phone = '0' . substr($phone, 3);
            }

            if (strlen($phone) !== 11 || substr($phone, 0, 1) !== '0') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Nigerian phone number format'
                ], 400);
            }

            // Extract prefix to determine network
            $prefix = substr($phone, 0, 4);
            $networkInfo = $this->getNetworkFromPrefix($prefix);

            if (!$networkInfo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Could not determine network for this phone number'
                ], 400);
            }

            // Check if the detected network matches expected network (if provided)
            $networkMatch = true;
            if ($expectedNetwork && strtolower($networkInfo['code']) !== strtolower($expectedNetwork)) {
                $networkMatch = false;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'phone' => $phone,
                    'formatted_phone' => $this->formatPhoneNumber($phone),
                    'network' => $networkInfo['network'],
                    'network_code' => $networkInfo['code'],
                    'detected_network' => $networkInfo['network'],
                    'network_match' => $networkMatch,
                    'valid' => true,
                    'confidence' => $networkMatch ? 'high' : 'medium'
                ],
                'message' => $networkMatch ? 'Phone number is valid' : 'Phone number might not match selected network'
            ]);

        } catch (\Exception $e) {
            Log::error('Phone validation error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate phone number'
            ], 500);
        }
    }

    /**
     * Batch validate multiple phone numbers
     */
    public function validateBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phones' => 'required|array|min:1|max:20',
            'phones.*' => 'required|string|regex:/^[0-9]{11}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid phone numbers provided',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $results = [];

            foreach ($request->phones as $phone) {
                $validationResult = $this->validateSinglePhone($phone);
                $results[] = $validationResult;
            }

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Batch phone validation error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate phone numbers'
            ], 500);
        }
    }

    /**
     * Get network information from phone prefix
     */
    private function getNetworkFromPrefix($prefix)
    {
        // Nigerian network prefixes
        $networkPrefixes = [
            // MTN
            '0803' => ['network' => 'MTN', 'code' => 'mtn'],
            '0806' => ['network' => 'MTN', 'code' => 'mtn'],
            '0813' => ['network' => 'MTN', 'code' => 'mtn'],
            '0816' => ['network' => 'MTN', 'code' => 'mtn'],
            '0810' => ['network' => 'MTN', 'code' => 'mtn'],
            '0814' => ['network' => 'MTN', 'code' => 'mtn'],
            '0903' => ['network' => 'MTN', 'code' => 'mtn'],
            '0906' => ['network' => 'MTN', 'code' => 'mtn'],
            '0913' => ['network' => 'MTN', 'code' => 'mtn'],
            '0916' => ['network' => 'MTN', 'code' => 'mtn'],

            // Airtel
            '0802' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0808' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0812' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0701' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0708' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0902' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0907' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0901' => ['network' => 'Airtel', 'code' => 'airtel'],
            '0912' => ['network' => 'Airtel', 'code' => 'airtel'],

            // Glo
            '0805' => ['network' => 'Glo', 'code' => 'glo'],
            '0807' => ['network' => 'Glo', 'code' => 'glo'],
            '0815' => ['network' => 'Glo', 'code' => 'glo'],
            '0811' => ['network' => 'Glo', 'code' => 'glo'],
            '0905' => ['network' => 'Glo', 'code' => 'glo'],
            '0915' => ['network' => 'Glo', 'code' => 'glo'],

            // 9mobile (Etisalat)
            '0809' => ['network' => '9mobile', 'code' => '9mobile'],
            '0817' => ['network' => '9mobile', 'code' => '9mobile'],
            '0818' => ['network' => '9mobile', 'code' => '9mobile'],
            '0908' => ['network' => '9mobile', 'code' => '9mobile'],
            '0909' => ['network' => '9mobile', 'code' => '9mobile']
        ];

        return $networkPrefixes[$prefix] ?? null;
    }

    /**
     * Check if number is ported to different network
     */
    private function checkPortedNumber($phone)
    {
        // This would typically call an external porting API
        // For now, return basic info indicating no porting check
        return [
            'is_ported' => false,
            'current_network' => null,
            'porting_checked' => false,
            'message' => 'Porting check not available'
        ];
    }

    /**
     * Validate single phone number (internal helper)
     */
    private function validateSinglePhone($phone)
    {
        try {
            // Check if phone starts with country code
            if (substr($phone, 0, 3) === '234') {
                $phone = '0' . substr($phone, 3);
            }

            if (strlen($phone) !== 11 || substr($phone, 0, 1) !== '0') {
                return [
                    'phone' => $phone,
                    'valid' => false,
                    'message' => 'Invalid Nigerian phone number format'
                ];
            }

            $prefix = substr($phone, 0, 4);
            $networkInfo = $this->getNetworkFromPrefix($prefix);

            if (!$networkInfo) {
                return [
                    'phone' => $phone,
                    'valid' => false,
                    'message' => 'Could not determine network for this phone number'
                ];
            }

            return [
                'phone' => $phone,
                'formatted_phone' => $this->formatPhoneNumber($phone),
                'network' => $networkInfo['network'],
                'network_code' => $networkInfo['code'],
                'valid' => true
            ];

        } catch (\Exception $e) {
            return [
                'phone' => $phone,
                'valid' => false,
                'message' => 'Validation error occurred'
            ];
        }
    }

    /**
     * Format phone number for display
     */
    private function formatPhoneNumber($phone)
    {
        if (strlen($phone) === 11) {
            return '+234' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Get all supported network prefixes
     */
    public function getSupportedNetworks()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'networks' => [
                    ['name' => 'MTN', 'code' => 'mtn'],
                    ['name' => 'Airtel', 'code' => 'airtel'],
                    ['name' => 'Glo', 'code' => 'glo'],
                    ['name' => '9mobile', 'code' => '9mobile']
                ],
                'prefixes' => [
                    'mtn' => ['0803', '0806', '0813', '0816', '0810', '0814', '0903', '0906', '0913', '0916'],
                    'airtel' => ['0802', '0808', '0812', '0701', '0708', '0902', '0907', '0901', '0912'],
                    'glo' => ['0805', '0807', '0815', '0811', '0905', '0915'],
                    '9mobile' => ['0809', '0817', '0818', '0908', '0909']
                ]
            ]
        ]);
    }
}
