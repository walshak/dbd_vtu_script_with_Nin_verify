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
        Schema::create('electricity', function (Blueprint $table) {
            $table->id('eId');
            $table->string('ePlan'); // Provider name like AEDC, EKEDC, etc.
            $table->string('eProviderId')->nullable(); // External provider ID
            $table->decimal('ePrice', 10, 2)->default(0); // Base price
            $table->decimal('eBuyingPrice', 10, 2)->default(0); // Buying price from provider
            $table->tinyInteger('eStatus')->default(1); // 1=active, 0=inactive
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity');
    }
};
