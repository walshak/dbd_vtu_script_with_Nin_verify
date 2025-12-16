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
        Schema::create('service_sync_status', function (Blueprint $table) {
            $table->id();
            $table->string('service_type', 50)->unique(); // 'data_plans', 'cable_plans', 'electricity', 'airtime'
            $table->timestamp('last_sync_at')->nullable();
            $table->enum('sync_status', ['never', 'success', 'partial', 'failed'])->default('never');
            $table->integer('total_synced')->default(0);
            $table->integer('total_created')->default(0);
            $table->integer('total_updated')->default(0);
            $table->integer('total_errors')->default(0);
            $table->text('last_error')->nullable();
            $table->string('api_source', 100)->default('uzobest');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_sync_status');
    }
};
