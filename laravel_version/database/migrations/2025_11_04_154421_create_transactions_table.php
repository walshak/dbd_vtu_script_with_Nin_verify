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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('tId');
            $table->unsignedBigInteger('user_id');
            $table->string('tType', 50);
            $table->enum('tStatus', ['pending', 'success', 'failed'])->default('pending');
            $table->decimal('tAmount', 10, 2);
            $table->string('tPhone', 20)->nullable();
            $table->string('tRef', 100)->unique();
            $table->decimal('tBalance', 10, 2)->default(0);
            $table->text('tRefer')->nullable();
            $table->string('tServer_ref', 100)->nullable();
            $table->text('tServer_response')->nullable();
            $table->timestamp('tDate')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
