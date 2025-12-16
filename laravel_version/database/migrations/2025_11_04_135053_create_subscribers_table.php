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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id('sId');
            $table->string('sApiKey', 200);
            $table->string('sFname', 50);
            $table->string('sLname', 50);
            $table->string('sEmail', 50)->nullable();
            $table->string('sPhone', 20)->unique();
            $table->string('sPass', 150);
            $table->string('sState', 50);
            $table->integer('sPin')->default(1234);
            $table->tinyInteger('sPinStatus')->default(0);
            $table->tinyInteger('sType')->default(1);
            $table->float('sWallet')->default(0);
            $table->float('sRefWallet')->default(0);
            $table->string('sBankNo', 20)->nullable();
            $table->string('sRolexBank', 20)->nullable();
            $table->string('sSterlingBank', 20)->nullable();
            $table->string('sFidelityBank', 20)->nullable();
            $table->string('sBankName', 30)->nullable();
            $table->tinyInteger('sRegStatus')->default(3);
            $table->smallInteger('sVerCode')->default(0);
            $table->timestamp('sRegDate')->useCurrent();
            $table->timestamp('sLastActivity')->nullable();
            $table->string('sReferal', 15)->nullable();

            // Laravel timestamps
            $table->timestamps();

            $table->index('sPhone');
            $table->index('sEmail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
