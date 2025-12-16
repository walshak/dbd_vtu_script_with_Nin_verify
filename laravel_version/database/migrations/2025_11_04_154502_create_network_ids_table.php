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
        Schema::create('network_ids', function (Blueprint $table) {
            $table->integer('nId')->primary();
            $table->string('network', 50);
            $table->string('smeId', 10)->nullable();
            $table->string('giftingId', 10)->nullable();
            $table->string('corporateId', 10)->nullable();
            $table->string('airtimeId', 10)->nullable();
            $table->enum('status', ['On', 'Off'])->default('On');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_ids');
    }
};
