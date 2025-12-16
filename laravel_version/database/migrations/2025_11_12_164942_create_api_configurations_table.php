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
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->unique();
            $table->text('config_value')->nullable();
            $table->string('service_type')->nullable(); // airtime, data, cable, electricity, etc.
            $table->string('network')->nullable(); // MTN, AIRTEL, GLO, 9MOBILE
            $table->string('provider_type')->nullable(); // VTU, ShareSell, SME, Corporate, Gifting
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_type', 'network', 'provider_type']);
            $table->index('config_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_configurations');
    }
};
