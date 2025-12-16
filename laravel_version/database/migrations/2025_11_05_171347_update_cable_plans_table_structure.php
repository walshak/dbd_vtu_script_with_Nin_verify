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
            // Drop existing columns
            // $table->dropColumn(['cPlanId', 'cDecoder', 'cPlan', 'userPrice', 'agentPrice', 'apiPrice', 'cDuration']);
        });

        Schema::table('cable_plans', function (Blueprint $table) {
            // Add new columns to match original schema
            $table->string('name', 255);
            $table->string('price', 255);
            // $table->string('userprice', 255);
            // $table->string('agentprice', 255);
            $table->string('vendorprice', 255);
            $table->string('planid', 255);
            $table->string('type', 255)->nullable();
            $table->tinyInteger('cableprovider');
            $table->string('day', 255);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::table('cable_plans', function (Blueprint $table) {
            // Rename primary key to match original
            $table->renameColumn('cId', 'cpId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cable_plans', function (Blueprint $table) {
            // Rename back
            $table->renameColumn('cpId', 'cId');

            // Drop new columns
            $table->dropColumn(['name', 'price', 'userprice', 'agentprice', 'vendorprice',
                               'planid', 'type', 'cableprovider', 'day', 'status']);
            $table->dropTimestamps();
        });

        Schema::table('cable_plans', function (Blueprint $table) {
            // Restore original columns
            // $table->string('cPlanId', 100)->unique();
            // $table->string('cDecoder', 50);
            // $table->string('cPlan', 100);
            // $table->integer('userPrice');
            // $table->integer('agentPrice');
            // $table->integer('apiPrice');
            // $table->string('cDuration', 50)->default('Monthly');
        });
    }
};
