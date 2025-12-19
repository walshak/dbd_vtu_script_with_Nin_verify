<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cable_plans', function (Blueprint $table) {
            // Unified pricing model columns
            $table->decimal('cost_price', 10, 2)->nullable()->after('vendorprice')->comment('Cost from Uzobest API');
            $table->decimal('selling_price', 10, 2)->nullable()->after('cost_price')->comment('Unified selling price for all users');
            $table->decimal('profit_margin', 10, 2)->nullable()->after('selling_price')->comment('Profit per transaction');

            // Uzobest API references
            $table->string('uzobest_cable_id', 50)->nullable()->after('profit_margin')->comment('Uzobest cable provider ID (1=DSTV, 2=GOTV, 3=STARTIMES)');
            $table->string('uzobest_plan_id', 100)->nullable()->after('uzobest_cable_id')->comment('Uzobest plan ID for API calls');
        });

        // Migrate existing data to new columns
        DB::statement('UPDATE cable_plans SET cost_price = CAST(price AS DECIMAL(10,2)) WHERE cost_price IS NULL');
        DB::statement('UPDATE cable_plans SET selling_price = CAST(userprice AS DECIMAL(10,2)) WHERE selling_price IS NULL');
        DB::statement('UPDATE cable_plans SET profit_margin = (CAST(userprice AS DECIMAL(10,2)) - CAST(price AS DECIMAL(10,2))) WHERE profit_margin IS NULL');
        DB::statement('UPDATE cable_plans SET uzobest_plan_id = planid WHERE uzobest_plan_id IS NULL');

        // Set uzobest_cable_id based on cableprovider (1=DSTV, 2=GOTV, 3=STARTIMES)
        DB::statement('UPDATE cable_plans SET uzobest_cable_id = cableprovider WHERE uzobest_cable_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cable_plans', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'selling_price', 'profit_margin', 'uzobest_cable_id', 'uzobest_plan_id']);
        });
    }
};
