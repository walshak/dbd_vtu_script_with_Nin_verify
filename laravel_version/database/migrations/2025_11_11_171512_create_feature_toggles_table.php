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
        Schema::create('feature_toggles', function (Blueprint $table) {
            $table->id();
            $table->string('feature_name');
            $table->string('feature_key')->unique();
            $table->boolean('is_enabled')->default(false);
            $table->text('description')->nullable();
            $table->string('environment')->default('production');
            $table->integer('rollout_percentage')->default(0);
            $table->json('target_users')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('created_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['feature_key', 'is_enabled']);
            $table->index(['environment', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_toggles');
    }
};
