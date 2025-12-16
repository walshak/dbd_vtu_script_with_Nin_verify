<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\DataPlan;
use App\Models\CableTVPlan;
use App\Models\ExamType;
use App\Services\ExternalApiService;

class ServiceParameterValidationController extends Controller
{
    private ExternalApiService $externalApiService;

    public function __construct(ExternalApiService $externalApiService)
    {
        $this->externalApiService = $externalApiService;
    }

    /**
     * Validate electricity meter number
     */
    public function validateMeterNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_number' => 'required|string|min:10|max:15',
            'disco' => 'required|string|in:eko_electric,ikeja_electric,abuja_electric,kaduna_electric,kano_electric,port_harcourt_electric,jos_electric,benin_electric,yola_electric,enugu_electric,ibadan_electric',
            'meter_type' => 'sometimes|string|in:prepaid,postpaid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid meter validation parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $meterNumber = $request->meter_number;
            $disco = $request->disco;
            $meterType = $request->meter_type ?? 'prepaid';

            // Validate meter number format
            if (!$this->isValidMeterNumberFormat($meterNumber)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid meter number format'
                ], 400);
            }

            // Check with external provider
            $validationResult = $this->validateMeterWithProvider($meterNumber, $disco, $meterType);

            if ($validationResult['valid']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Meter number validated successfully',
                    'data' => [
                        'meter_number' => $meterNumber,
                        'disco' => $disco,
                        'customer_name' => $validationResult['customer_name'],
                        'customer_address' => $validationResult['customer_address'],
                        'meter_type' => $validationResult['meter_type'],
                        'outstanding_balance' => $validationResult['outstanding_balance'],
                        'minimum_amount' => $validationResult['minimum_amount'],
                        'maximum_amount' => $validationResult['maximum_amount'],
                        'validated_at' => now()->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Meter number validation failed',
                    'data' => [
                        'error_code' => $validationResult['error_code'],
                        'error_message' => $validationResult['error_message']
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error validating meter number: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate meter number'
            ], 500);
        }
    }

    /**
     * Validate cable TV IUC/smartcard number
     */
    public function validateCableTVNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'iuc_number' => 'required|string|min:10|max:15',
            'provider' => 'required|string|in:dstv,gotv,startimes,showmax'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid cable TV validation parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $iucNumber = $request->iuc_number;
            $provider = $request->provider;

            // Validate IUC number format
            if (!$this->isValidIUCNumberFormat($iucNumber, $provider)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid IUC/smartcard number format for ' . strtoupper($provider)
                ], 400);
            }

            // Check with external provider
            $validationResult = $this->validateIUCWithProvider($iucNumber, $provider);

            if ($validationResult['valid']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'IUC number validated successfully',
                    'data' => [
                        'iuc_number' => $iucNumber,
                        'provider' => strtoupper($provider),
                        'customer_name' => $validationResult['customer_name'],
                        'customer_number' => $validationResult['customer_number'],
                        'current_package' => $validationResult['current_package'],
                        'due_date' => $validationResult['due_date'],
                        'status' => $validationResult['status'],
                        'outstanding_amount' => $validationResult['outstanding_amount'],
                        'validated_at' => now()->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'IUC number validation failed',
                    'data' => [
                        'error_code' => $validationResult['error_code'],
                        'error_message' => $validationResult['error_message']
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error validating IUC number: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate IUC number'
            ], 500);
        }
    }

    /**
     * Validate and get available data plans for phone number
     */
    public function validateDataPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'plan_id' => 'sometimes|integer|exists:data_plans,id',
            'network' => 'sometimes|string|in:mtn,airtel,glo,9mobile'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data plan validation parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $phone = $request->phone;

            // Validate phone and detect network
            $phoneValidation = $this->validatePhoneAndGetNetwork($phone);
            if (!$phoneValidation['valid']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid phone number or network detection failed'
                ], 400);
            }

            $detectedNetwork = $phoneValidation['network'];
            $requestedNetwork = $request->network;

            // Check if requested network matches detected network
            if ($requestedNetwork && $requestedNetwork !== $detectedNetwork) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Network mismatch detected',
                    'data' => [
                        'detected_network' => $detectedNetwork,
                        'requested_network' => $requestedNetwork,
                        'recommendation' => "Phone number belongs to {$detectedNetwork} network"
                    ]
                ]);
            }

            $network = $requestedNetwork ?: $detectedNetwork;

            // Get available plans for the network
            $availablePlans = $this->getAvailableDataPlans($network);

            if ($request->has('plan_id')) {
                // Validate specific plan
                $plan = DataPlan::find($request->plan_id);
                if (!$plan || $plan->network->network !== ucfirst($network)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Plan not available for this network'
                    ], 400);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data plan validated successfully',
                    'data' => [
                        'phone' => $phone,
                        'network' => $network,
                        'plan' => [
                            'id' => $plan->id,
                            'name' => $plan->plan_name,
                            'data_size' => $plan->data_size,
                            'validity' => $plan->validity,
                            'price' => number_format($plan->selling_price, 2)
                        ],
                        'validated_at' => now()->toISOString()
                    ]
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Available data plans retrieved',
                'data' => [
                    'phone' => $phone,
                    'network' => $network,
                    'available_plans' => $availablePlans,
                    'total_plans' => count($availablePlans)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating data plan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate data plan'
            ], 500);
        }
    }

    /**
     * Validate exam type and get pricing
     */
    public function validateExamType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_type' => 'required|string',
            'quantity' => 'sometimes|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid exam validation parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $examType = strtoupper($request->exam_type);
            $quantity = $request->quantity ?? 1;

            // Find exam type
            $exam = ExamType::where('exam', 'LIKE', "%{$examType}%")
                ->where('status', 'active')
                ->first();

            if (!$exam) {
                // Get available exam types
                $availableExams = ExamType::where('status', 'active')
                    ->select('id', 'exam', 'amount')
                    ->get()
                    ->map(function ($exam) {
                        return [
                            'id' => $exam->id,
                            'name' => $exam->exam,
                            'price' => number_format($exam->amount, 2),
                            'code' => strtolower(str_replace([' ', '/'], ['_', '_'], $exam->exam))
                        ];
                    });

                return response()->json([
                    'status' => 'error',
                    'message' => 'Exam type not found',
                    'data' => [
                        'available_exam_types' => $availableExams
                    ]
                ], 404);
            }

            // Check availability for exam type
            $availabilityCheck = $this->checkExamAvailability($exam->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam type validated successfully',
                'data' => [
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->exam,
                    'exam_code' => strtolower(str_replace([' ', '/'], ['_', '_'], $exam->exam)),
                    'unit_price' => number_format($exam->amount, 2),
                    'quantity' => $quantity,
                    'total_amount' => number_format($exam->amount * $quantity, 2),
                    'availability' => [
                        'available' => $availabilityCheck['available'],
                        'stock_level' => $availabilityCheck['stock_level'],
                        'estimated_delivery' => $availabilityCheck['estimated_delivery']
                    ],
                    'validated_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating exam type: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate exam type'
            ], 500);
        }
    }

    /**
     * Get available cable TV plans for provider
     */
    public function getCableTVPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:dstv,gotv,startimes,showmax'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid provider parameter',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $provider = $request->provider;

            $plans = CableTVPlan::where('provider', strtoupper($provider))
                ->where('status', 'active')
                ->orderBy('amount')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->plan_name,
                        'code' => $plan->plan_code,
                        'amount' => number_format($plan->amount, 2),
                        'validity' => $plan->validity ?? 'Monthly',
                        'description' => $plan->description ?? ''
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'provider' => strtoupper($provider),
                    'plans' => $plans,
                    'total_plans' => $plans->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cable TV plans: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get cable TV plans'
            ], 500);
        }
    }

    /**
     * Validate service transaction amount
     */
    public function validateTransactionAmount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable_tv,electricity,exam_pin,recharge_pin',
            'amount' => 'required|numeric|min:0.01',
            'provider' => 'sometimes|string',
            'plan_id' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid amount validation parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $serviceType = $request->service_type;
            $amount = $request->amount;

            $limits = $this->getServiceAmountLimits($serviceType);
            $isValid = $amount >= $limits['min'] && $amount <= $limits['max'];

            if (!$isValid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Amount is outside allowed limits',
                    'data' => [
                        'requested_amount' => number_format($amount, 2),
                        'minimum_amount' => number_format($limits['min'], 2),
                        'maximum_amount' => number_format($limits['max'], 2),
                        'service_type' => $serviceType
                    ]
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction amount validated successfully',
                'data' => [
                    'service_type' => $serviceType,
                    'amount' => number_format($amount, 2),
                    'within_limits' => true,
                    'limits' => [
                        'minimum' => number_format($limits['min'], 2),
                        'maximum' => number_format($limits['max'], 2)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating transaction amount: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate transaction amount'
            ], 500);
        }
    }

    /**
     * Batch validate multiple service parameters
     */
    public function batchValidateParameters(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'validations' => 'required|array|min:1|max:10',
            'validations.*.type' => 'required|string|in:meter,iuc,phone,data_plan,exam,amount',
            'validations.*.parameters' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid batch validation parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $results = [];

            foreach ($request->validations as $index => $validation) {
                $type = $validation['type'];
                $parameters = $validation['parameters'];

                try {
                    switch ($type) {
                        case 'meter':
                            $result = $this->validateSingleMeter($parameters);
                            break;
                        case 'iuc':
                            $result = $this->validateSingleIUC($parameters);
                            break;
                        case 'phone':
                            $result = $this->validateSinglePhone($parameters);
                            break;
                        case 'data_plan':
                            $result = $this->validateSingleDataPlan($parameters);
                            break;
                        case 'exam':
                            $result = $this->validateSingleExam($parameters);
                            break;
                        case 'amount':
                            $result = $this->validateSingleAmount($parameters);
                            break;
                        default:
                            $result = ['valid' => false, 'message' => 'Invalid validation type'];
                    }
                } catch (\Exception $e) {
                    $result = ['valid' => false, 'message' => 'Validation error: ' . $e->getMessage()];
                }

                $results[] = [
                    'index' => $index,
                    'type' => $type,
                    'result' => $result
                ];
            }

            $successCount = collect($results)->where('result.valid', true)->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_validations' => count($results),
                    'successful_validations' => $successCount,
                    'failed_validations' => count($results) - $successCount,
                    'results' => $results,
                    'processed_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in batch parameter validation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to perform batch validation'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */

    private function isValidMeterNumberFormat($meterNumber)
    {
        // Basic meter number format validation
        return preg_match('/^[0-9]{10,15}$/', $meterNumber);
    }

    private function validateMeterWithProvider($meterNumber, $disco, $meterType)
    {
        try {
            $result = $this->externalApiService->verifyMeter($meterNumber, $meterType, $disco);

            if (!$result['success']) {
                return [
                    'valid' => false,
                    'customer_name' => null,
                    'customer_address' => null,
                    'meter_type' => $meterType,
                    'outstanding_balance' => '0.00',
                    'minimum_amount' => 1000,
                    'maximum_amount' => 50000,
                    'error_code' => $result['error_code'] ?? 'VERIFICATION_FAILED',
                    'error_message' => $result['message'] ?? 'Meter verification failed'
                ];
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];

            return [
                'valid' => true,
                'customer_name' => $responseData['customer_name'] ?? $responseData['name'] ?? 'Unknown Customer',
                'customer_address' => $responseData['customer_address'] ?? $responseData['address'] ?? 'Not Available',
                'meter_type' => $responseData['meter_type'] ?? $meterType,
                'outstanding_balance' => $responseData['outstanding_balance'] ?? $responseData['balance'] ?? '0.00',
                'minimum_amount' => $responseData['minimum_amount'] ?? 1000,
                'maximum_amount' => $responseData['maximum_amount'] ?? 50000,
                'error_code' => null,
                'error_message' => null
            ];

        } catch (\Exception $e) {
            Log::error('Meter validation error', [
                'meter_number' => $meterNumber,
                'disco' => $disco,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'customer_name' => null,
                'customer_address' => null,
                'meter_type' => $meterType,
                'outstanding_balance' => '0.00',
                'minimum_amount' => 1000,
                'maximum_amount' => 50000,
                'error_code' => 'SYSTEM_ERROR',
                'error_message' => 'System error occurred during meter validation'
            ];
        }
    }

    private function isValidIUCNumberFormat($iucNumber, $provider)
    {
        $patterns = [
            'dstv' => '/^[0-9]{10,11}$/',
            'gotv' => '/^[0-9]{10,11}$/',
            'startimes' => '/^[0-9]{10,13}$/',
            'showmax' => '/^[0-9]{10,15}$/'
        ];

        return isset($patterns[$provider]) && preg_match($patterns[$provider], $iucNumber);
    }

    private function validateIUCWithProvider($iucNumber, $provider)
    {
        try {
            $result = $this->externalApiService->verifyCableIUC($iucNumber, $provider);

            if (!$result['success']) {
                return [
                    'valid' => false,
                    'customer_name' => null,
                    'customer_number' => null,
                    'current_package' => null,
                    'due_date' => null,
                    'status' => 'Inactive',
                    'outstanding_amount' => '0.00',
                    'error_code' => $result['error_code'] ?? 'VERIFICATION_FAILED',
                    'error_message' => $result['message'] ?? 'IUC verification failed'
                ];
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];

            return [
                'valid' => true,
                'customer_name' => $responseData['customer_name'] ?? $responseData['name'] ?? 'Unknown Customer',
                'customer_number' => $responseData['customer_number'] ?? $responseData['phone'] ?? null,
                'current_package' => $responseData['current_package'] ?? $responseData['package'] ?? 'Unknown Package',
                'due_date' => $responseData['due_date'] ?? $responseData['expiry_date'] ?? null,
                'status' => $responseData['status'] ?? 'Active',
                'outstanding_amount' => $responseData['outstanding_amount'] ?? $responseData['balance'] ?? '0.00',
                'error_code' => null,
                'error_message' => null
            ];

        } catch (\Exception $e) {
            Log::error('Cable IUC validation error', [
                'iuc_number' => $iucNumber,
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'customer_name' => null,
                'customer_number' => null,
                'current_package' => null,
                'due_date' => null,
                'status' => 'Inactive',
                'outstanding_amount' => '0.00',
                'error_code' => 'SYSTEM_ERROR',
                'error_message' => 'System error occurred during IUC validation'
            ];
        }
    }

    private function validatePhoneAndGetNetwork($phone)
    {
        try {
            $result = $this->externalApiService->validatePhone($phone);

            if (!$result['success']) {
                return [
                    'valid' => false,
                    'network' => null,
                    'error_message' => $result['message'] ?? 'Phone validation failed'
                ];
            }

            $data = $result['data'] ?? [];

            return [
                'valid' => true,
                'network' => strtolower($data['network'] ?? 'unknown'),
                'formatted_phone' => $data['formatted_number'] ?? $phone
            ];

        } catch (\Exception $e) {
            Log::error('Phone validation error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'network' => null,
                'error_message' => 'System error occurred during phone validation'
            ];
        }
    }

    private function getAvailableDataPlans($network)
    {
        return Cache::remember("data_plans_{$network}", 600, function () use ($network) {
            return DataPlan::whereHas('network', function ($query) use ($network) {
                $query->where('network', ucfirst($network));
            })
                ->where('status', 'active')
                ->orderBy('data_size')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->plan_name,
                        'data_size' => $plan->data_size,
                        'validity' => $plan->validity,
                        'price' => number_format($plan->selling_price, 2)
                    ];
                });
        });
    }

    private function checkExamAvailability($examId)
    {
        // Mock availability check
        return [
            'available' => rand(0, 10) > 1, // 90% availability
            'stock_level' => rand(50, 1000),
            'estimated_delivery' => 'Instant'
        ];
    }

    private function getServiceAmountLimits($serviceType)
    {
        $limits = [
            'airtime' => ['min' => 50, 'max' => 50000],
            'data' => ['min' => 100, 'max' => 20000],
            'cable_tv' => ['min' => 800, 'max' => 25000],
            'electricity' => ['min' => 1000, 'max' => 50000],
            'exam_pin' => ['min' => 500, 'max' => 15000],
            'recharge_pin' => ['min' => 100, 'max' => 10000]
        ];

        return $limits[$serviceType] ?? ['min' => 50, 'max' => 50000];
    }

    // Single validation methods for batch processing
    private function validateSingleMeter($parameters)
    {
        // Simplified single meter validation
        $result = $this->validateMeterWithProvider(
            $parameters['meter_number'] ?? '',
            $parameters['disco'] ?? 'eko_electric',
            $parameters['meter_type'] ?? 'prepaid'
        );
        return ['valid' => $result['valid'], 'data' => $result];
    }

    private function validateSingleIUC($parameters)
    {
        $result = $this->validateIUCWithProvider(
            $parameters['iuc_number'] ?? '',
            $parameters['provider'] ?? 'dstv'
        );
        return ['valid' => $result['valid'], 'data' => $result];
    }

    private function validateSinglePhone($parameters)
    {
        $result = $this->validatePhoneAndGetNetwork($parameters['phone'] ?? '');
        return ['valid' => $result['valid'], 'data' => $result];
    }

    private function validateSingleDataPlan($parameters)
    {
        $phone = $parameters['phone'] ?? '';
        $phoneValidation = $this->validatePhoneAndGetNetwork($phone);
        return ['valid' => $phoneValidation['valid'], 'data' => $phoneValidation];
    }

    private function validateSingleExam($parameters)
    {
        $examType = $parameters['exam_type'] ?? '';
        $exam = ExamType::where('exam', 'LIKE', "%{$examType}%")->where('status', 'active')->first();
        return ['valid' => $exam !== null, 'data' => $exam ? ['exam_id' => $exam->id, 'exam_name' => $exam->exam] : null];
    }

    private function validateSingleAmount($parameters)
    {
        $serviceType = $parameters['service_type'] ?? 'airtime';
        $amount = $parameters['amount'] ?? 0;
        $limits = $this->getServiceAmountLimits($serviceType);
        $valid = $amount >= $limits['min'] && $amount <= $limits['max'];
        return ['valid' => $valid, 'data' => ['amount' => $amount, 'limits' => $limits]];
    }
}
