<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add cost_price (from Uzobest), selling_price (admin sets), and uzobest_id columns
     * to all service tables for unified pricing management.
     */
    public function up(): void
    {
        // Data Plans
        if (Schema::hasTable('data_plans')) {
            Schema::table('data_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('data_plans', 'cost_price')) {
                    $table->decimal('cost_price', 10, 2)->nullable()->after('userPrice')->comment('Cost from Uzobest API');
                }
                if (!Schema::hasColumn('data_plans', 'selling_price')) {
                    $table->decimal('selling_price', 10, 2)->nullable()->after('cost_price')->comment('Admin-configured selling price');
                }
                if (!Schema::hasColumn('data_plans', 'uzobest_plan_id')) {
                    $table->integer('uzobest_plan_id')->nullable()->after('nId')->comment('Uzobest API plan ID');
                }
                if (!Schema::hasColumn('data_plans', 'profit_margin')) {
                    $table->decimal('profit_margin', 10, 2)->nullable()->comment('Calculated: selling_price - cost_price');
                }
            });
        }

        // Cable TV (cable_plans table)
        if (Schema::hasTable('cable_plans')) {
            Schema::table('cable_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('cable_plans', 'cost_price')) {
                    $table->decimal('cost_price', 10, 2)->nullable()->after('userprice')->comment('Cost from Uzobest API');
                }
                if (!Schema::hasColumn('cable_plans', 'selling_price')) {
                    $table->decimal('selling_price', 10, 2)->nullable()->after('cost_price')->comment('Admin-configured selling price');
                }
                if (!Schema::hasColumn('cable_plans', 'uzobest_cable_id')) {
                    $table->integer('uzobest_cable_id')->nullable()->after('cableprovider')->comment('Uzobest API cable provider ID');
                }
                if (!Schema::hasColumn('cable_plans', 'uzobest_plan_id')) {
                    $table->integer('uzobest_plan_id')->nullable()->comment('Uzobest API plan ID');
                }
                if (!Schema::hasColumn('cable_plans', 'profit_margin')) {
                    $table->decimal('profit_margin', 10, 2)->nullable()->comment('Calculated: selling_price - cost_price');
                }
            });
        }

        // Electricity
        if (Schema::hasTable('electricity')) {
            Schema::table('electricity', function (Blueprint $table) {
                if (!Schema::hasColumn('electricity', 'cost_price')) {
                    $table->decimal('cost_price', 10, 2)->nullable()->after('eBuyingPrice')->comment('Cost from Uzobest API');
                }
                if (!Schema::hasColumn('electricity', 'selling_price')) {
                    $table->decimal('selling_price', 10, 2)->nullable()->after('cost_price')->comment('Admin-configured selling price');
                }
                if (!Schema::hasColumn('electricity', 'uzobest_disco_id')) {
                    $table->integer('uzobest_disco_id')->nullable()->after('ePlan')->comment('Uzobest API disco ID');
                }
                if (!Schema::hasColumn('electricity', 'profit_margin')) {
                    $table->decimal('profit_margin', 10, 2)->nullable()->comment('Calculated: selling_price - cost_price');
                }
            });
        }

        // Airtime Pricing
        if (Schema::hasTable('airtimepinprice')) {
            Schema::table('airtimepinprice', function (Blueprint $table) {
                if (!Schema::hasColumn('airtimepinprice', 'cost_percentage')) {
                    $table->decimal('cost_percentage', 5, 2)->nullable()->after('aNetwork')->comment('Cost % from Uzobest (e.g., 98.5)');
                }
                if (!Schema::hasColumn('airtimepinprice', 'selling_percentage')) {
                    $table->decimal('selling_percentage', 5, 2)->nullable()->after('cost_percentage')->comment('Selling % admin sets (e.g., 99.0)');
                }
                if (!Schema::hasColumn('airtimepinprice', 'profit_margin')) {
                    $table->decimal('profit_margin', 5, 2)->nullable()->comment('Calculated: selling_percentage - cost_percentage');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_plans')) {
            Schema::table('data_plans', function (Blueprint $table) {
                $table->dropColumn(['cost_price', 'selling_price', 'uzobest_plan_id', 'profit_margin']);
            });
        }

        if (Schema::hasTable('cable_plans')) {
            Schema::table('cable_plans', function (Blueprint $table) {
                $table->dropColumn(['cost_price', 'selling_price', 'uzobest_cable_id', 'uzobest_plan_id', 'profit_margin']);
            });
        }

        if (Schema::hasTable('electricity')) {
            Schema::table('electricity', function (Blueprint $table) {
                $table->dropColumn(['cost_price', 'selling_price', 'uzobest_disco_id', 'profit_margin']);
            });
        }

        if (Schema::hasTable('airtimepinprice')) {
            Schema::table('airtimepinprice', function (Blueprint $table) {
                $table->dropColumn(['cost_percentage', 'selling_percentage', 'profit_margin']);
            });
        }
    }
};
