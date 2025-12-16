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
        Schema::create('recharge_pin_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('reference');
            $table->string('network');
            $table->decimal('denomination', 10, 2);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('business_name')->nullable();
            $table->json('pins_data');
            $table->string('status')->default('successful');
            $table->timestamps();

            $table->foreign('user_id')->references('sId')->on('subscribers')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharge_pin_history');
    }
};
