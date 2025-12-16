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
        Schema::create('sysusers', function (Blueprint $table) {
            $table->id('sysId');
            $table->string('sysName', 100);
            $table->tinyInteger('sysRole');
            $table->string('sysUsername', 100)->unique();
            $table->string('sysToken', 255);
            $table->tinyInteger('sysStatus')->default(0);
            $table->timestamps();

            $table->index('sysUsername');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysusers');
    }
};
