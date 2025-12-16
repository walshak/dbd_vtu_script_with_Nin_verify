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
        // Drop existing table
        Schema::dropIfExists('cable_plans');

        // Recreate table with proper structure
        Schema::create('cable_plans', function (Blueprint $table) {
            $table->id('cpId');
            $table->string('name', 255);
            $table->string('price', 255);
            $table->string('userprice', 255);
            $table->string('agentprice', 255);
            $table->string('vendorprice', 255);
            $table->string('planid', 255);
            $table->string('type', 255)->nullable();
            $table->tinyInteger('cableprovider');
            $table->string('day', 255);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_plans');
        
        // Recreate with original structure
        Schema::create('cable_plans', function (Blueprint $table) {
            $table->id('cId');
            $table->string('cPlanId', 100)->unique();
            $table->string('cDecoder', 50);
            $table->string('cPlan', 100);
            $table->integer('userPrice');
            $table->integer('agentPrice');
            $table->integer('apiPrice');
            $table->string('cDuration', 50)->default('Monthly');
        });
    }
};
