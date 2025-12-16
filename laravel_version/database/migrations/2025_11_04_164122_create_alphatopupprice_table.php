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
        Schema::create('alphatopupprice', function (Blueprint $table) {
            $table->id('alphaId');
            $table->decimal('buyingPrice', 10, 2);
            $table->decimal('sellingPrice', 10, 2);
            $table->decimal('agent', 10, 2);
            $table->decimal('vendor', 10, 2);
            $table->timestamp('dPosted')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alphatopupprice');
    }
};
