<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove agent and vendor discount columns from pricing tables
     * as we're moving to a simplified user-only pricing model.
     */
    public function up(): void
    {
        // Airtime Pricing - Remove agent and vendor discounts
        if (Schema::hasTable('airtimepinprice')) {
            Schema::table('airtimepinprice', function (Blueprint $table) {
                if (Schema::hasColumn('airtimepinprice', 'aAgentDiscount')) {
                    $table->dropColumn('aAgentDiscount');
                }
                if (Schema::hasColumn('airtimepinprice', 'aVendorDiscount')) {
                    $table->dropColumn('aVendorDiscount');
                }
            });
        }

        // Recharge Pins - Remove agent and vendor columns if they exist
        if (Schema::hasTable('rechargepin')) {
            Schema::table('rechargepin', function (Blueprint $table) {
                $columns_to_drop = [];

                if (Schema::hasColumn('rechargepin', 'agent_discount')) {
                    $columns_to_drop[] = 'agent_discount';
                }
                if (Schema::hasColumn('rechargepin', 'vendor_discount')) {
                    $columns_to_drop[] = 'vendor_discount';
                }
                if (Schema::hasColumn('rechargepin', 'agent_price')) {
                    $columns_to_drop[] = 'agent_price';
                }
                if (Schema::hasColumn('rechargepin', 'vendor_price')) {
                    $columns_to_drop[] = 'vendor_price';
                }

                if (!empty($columns_to_drop)) {
                    $table->dropColumn($columns_to_drop);
                }
            });
        }

        // Data Plans - Remove agent/vendor pricing if exists
        if (Schema::hasTable('data_plans')) {
            Schema::table('data_plans', function (Blueprint $table) {
                $columns_to_drop = [];

                if (Schema::hasColumn('data_plans', 'agent_price')) {
                    $columns_to_drop[] = 'agent_price';
                }
                if (Schema::hasColumn('data_plans', 'vendor_price')) {
                    $columns_to_drop[] = 'vendor_price';
                }
                if (Schema::hasColumn('data_plans', 'agent_discount')) {
                    $columns_to_drop[] = 'agent_discount';
                }
                if (Schema::hasColumn('data_plans', 'vendor_discount')) {
                    $columns_to_drop[] = 'vendor_discount';
                }

                if (!empty($columns_to_drop)) {
                    $table->dropColumn($columns_to_drop);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore agent/vendor columns if needed
        if (Schema::hasTable('airtimepinprice')) {
            Schema::table('airtimepinprice', function (Blueprint $table) {
                $table->decimal('aAgentDiscount', 5, 2)->nullable();
                $table->decimal('aVendorDiscount', 5, 2)->nullable();
            });
        }

        if (Schema::hasTable('rechargepin')) {
            Schema::table('rechargepin', function (Blueprint $table) {
                $table->decimal('agent_discount', 5, 2)->nullable();
                $table->decimal('vendor_discount', 5, 2)->nullable();
            });
        }

        if (Schema::hasTable('data_plans')) {
            Schema::table('data_plans', function (Blueprint $table) {
                $table->decimal('agent_price', 10, 2)->nullable();
                $table->decimal('vendor_price', 10, 2)->nullable();
            });
        }
    }
};
