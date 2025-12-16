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
        Schema::create('cable_ids', function (Blueprint $table) {
            $table->id('cId');
            $table->string('cableid', 10)->nullable();
            $table->string('provider', 10);
            $table->string('providerStatus', 10)->default('On');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_ids');
    }
};
