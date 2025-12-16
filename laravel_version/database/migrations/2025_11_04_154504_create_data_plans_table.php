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
        Schema::create('data_plans', function (Blueprint $table) {
            $table->id('dId');
            $table->string('dPlanId', 100)->unique();
            $table->integer('nId');
            $table->string('dPlan', 100);
            $table->string('dAmount', 50);
            $table->string('dValidity', 50);
            $table->integer('userPrice');
            $table->integer('agentPrice');
            $table->integer('apiPrice');
            $table->string('dGroup', 50)->default('SME');

            $table->foreign('nId')->references('nId')->on('network_ids')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_plans');
    }
};
