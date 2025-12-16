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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('nId');
            $table->string('nSubject');
            $table->enum('nMessageFor', ['all', 'users', 'agents', 'vendors'])->default('all');
            $table->text('nMessage');
            $table->tinyInteger('nStatus')->default(1);
            $table->timestamp('dPosted')->useCurrent();
            $table->timestamp('dUpdated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
