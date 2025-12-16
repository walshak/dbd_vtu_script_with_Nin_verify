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
        Schema::create('airtimepinprice', function (Blueprint $table) {
            $table->id('aId');
            $table->string('aNetwork', 10);
            $table->decimal('aUserDiscount', 5, 2)->default(99.00);
            $table->decimal('aAgentDiscount', 5, 2)->default(98.00);
            $table->decimal('aVendorDiscount', 5, 2)->default(97.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airtimepinprice');
    }
};
