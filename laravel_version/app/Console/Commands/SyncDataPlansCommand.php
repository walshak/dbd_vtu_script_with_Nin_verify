<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalApiService;
use App\Models\DataPlan;
use App\Models\NetworkId;
use Illuminate\Support\Facades\Log;

class SyncDataPlansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:sync-plans {--force : Force sync even if last sync was recent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data plans from Uzobest API and update cost prices';

    protected ExternalApiService $externalApiService;

    public function __construct(ExternalApiService $externalApiService)
    {
        parent::__construct();
        $this->externalApiService = $externalApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data plans sync from Uzobest API...');

        try {
            // Fetch all networks and their data plans from Uzobest
            $response = $this->externalApiService->getDataPlans();

            if (!$response['success']) {
                $this->error('Failed to fetch data plans from Uzobest: ' . ($response['message'] ?? 'Unknown error'));
                return 1;
            }

            $networkPlans = $response['plans'] ?? [];
            $totalPlans = 0;
            $updatedPlans = 0;
            $newPlans = 0;

            // Map the API response format to what we expect
            $networks = [];
            if (isset($networkPlans['MTN_PLAN'])) {
                $networks[] = ['network' => 'MTN', 'plans' => $networkPlans['MTN_PLAN']];
            }
            if (isset($networkPlans['GLO_PLAN'])) {
                $networks[] = ['network' => 'GLO', 'plans' => $networkPlans['GLO_PLAN']];
            }
            if (isset($networkPlans['AIRTEL_PLAN'])) {
                $networks[] = ['network' => 'AIRTEL', 'plans' => $networkPlans['AIRTEL_PLAN']];
            }
            if (isset($networkPlans['9MOBILE_PLAN'])) {
                $networks[] = ['network' => '9MOBILE', 'plans' => $networkPlans['9MOBILE_PLAN']];
            }

            $this->info('Processing ' . count($networks) . ' networks...');

            foreach ($networks as $networkData) {
                $networkName = $networkData['network'] ?? null;
                $plans = $networkData['plans'] ?? [];

                if (!$networkName || empty($plans)) {
                    continue;
                }

                // Get local network ID
                $network = NetworkId::getByName(strtoupper($networkName));
                if (!$network) {
                    $this->warn("Unknown network: {$networkName}");
                    continue;
                }

                $this->line("Processing {$networkName} with " . count($plans) . " plans...");

                foreach ($plans as $plan) {
                    $totalPlans++;

                    $uzobestPlanId = $plan['id'] ?? $plan['dataplan_id'] ?? null;
                    $planName = $plan['plan'] ?? null;
                    $planSize = $plan['plan'] ?? null; // Same as plan name in this API
                    $costPrice = floatval($plan['plan_amount'] ?? 0);

                    if (!$uzobestPlanId || !$planName || $costPrice <= 0) {
                        $this->warn("Skipping invalid plan: " . json_encode($plan));
                        continue;
                    }

                    // Check if plan already exists
                    $existingPlan = DataPlan::where('uzobest_plan_id', $uzobestPlanId)->first();

                    if ($existingPlan) {
                        // Update cost price and recalculate profit margin
                        $oldCostPrice = $existingPlan->cost_price;
                        $sellingPrice = $existingPlan->selling_price;

                        $existingPlan->update([
                            'cost_price' => $costPrice,
                            'profit_margin' => $this->calculateProfitMargin($sellingPrice, $costPrice),
                        ]);

                        if ($oldCostPrice != $costPrice) {
                            $updatedPlans++;
                            $this->line("  Updated: {$planName} - Cost: ₦{$oldCostPrice} → ₦{$costPrice}");
                        }
                    } else {
                        // Create new plan with 5% markup as default
                        $sellingPrice = $costPrice * 1.05;

                        DataPlan::create([
                            'nId' => $network->nId,
                            'dPlan' => $planName,
                            'dAmount' => $planSize,
                            'dValidity' => $plan['month_validate'] ?? '30 Days',
                            'dGroup' => $plan['plan_type'] ?? 'SME',
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                            'profit_margin' => 5.0,
                            'uzobest_plan_id' => $uzobestPlanId,
                            // Set legacy fields to maintain compatibility
                            'userPrice' => $sellingPrice,
                            'agentPrice' => $sellingPrice,
                            'apiPrice' => $sellingPrice,
                            'dPlanId' => $uzobestPlanId,
                        ]);

                        $newPlans++;
                        $this->line("  Created: {$planName} - Cost: ₦{$costPrice}, Selling: ₦{$sellingPrice}");
                    }
                }
            }

            // Log sync completion
            Log::info('Data plans sync completed', [
                'total_plans_processed' => $totalPlans,
                'updated_plans' => $updatedPlans,
                'new_plans' => $newPlans,
            ]);

            $this->info("\nSync completed successfully!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total plans processed', $totalPlans],
                    ['Plans updated', $updatedPlans],
                    ['New plans created', $newPlans],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('Data plans sync failed: ' . $e->getMessage());
            Log::error('Data plans sync error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }

    /**
     * Calculate profit margin percentage
     */
    private function calculateProfitMargin(float $sellingPrice, float $costPrice): float
    {
        if ($costPrice <= 0) {
            return 0;
        }

        return round((($sellingPrice - $costPrice) / $costPrice) * 100, 2);
    }
}
