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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_plans');
    }
};
