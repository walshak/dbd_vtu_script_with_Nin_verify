<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CablePlan;
use App\Services\UzobestSyncService;

class UpdateCablePlanIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cable:update-plan-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cable plans with proper Uzobest numeric plan IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating cable plan Uzobest IDs...');

        $syncService = app(UzobestSyncService::class);
        $planMapping = $syncService->getStandardCablePlanMapping();

        $updated = 0;
        $notFound = 0;

        $plans = CablePlan::all();

        foreach ($plans as $plan) {
            $planKey = strtolower($plan->planid);

            if (isset($planMapping[$planKey])) {
                $uzobestPlanId = $planMapping[$planKey];

                $plan->update([
                    'uzobest_plan_id' => $uzobestPlanId
                ]);

                $this->line("✓ Updated {$plan->name}: uzobest_plan_id = {$uzobestPlanId}");
                $updated++;
            } else {
                $this->warn("✗ No mapping found for: {$plan->name} (planid: {$plan->planid})");
                $notFound++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("Updated: {$updated} plans");

        if ($notFound > 0) {
            $this->warn("Not found: {$notFound} plans (these need manual verification with Uzobest)");
        }

        $this->newLine();
        $this->info('Cable plan IDs updated successfully!');
        $this->comment('Note: Please verify these IDs with Uzobest API documentation for your account.');

        return 0;
    }
}
