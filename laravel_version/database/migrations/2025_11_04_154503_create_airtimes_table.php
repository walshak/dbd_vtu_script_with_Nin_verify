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
        Schema::create('airtimes', function (Blueprint $table) {
            $table->id('aId');
            $table->integer('nId');
            $table->integer('airtimeAmount');
            $table->decimal('userDiscount', 5, 2)->default(0);
            $table->decimal('agentDiscount', 5, 2)->default(0);
            $table->decimal('apiDiscount', 5, 2)->default(0);
            $table->string('airtimeType', 50)->default('VTU');

            $table->foreign('nId')->references('nId')->on('network_ids')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airtimes');
    }
};
