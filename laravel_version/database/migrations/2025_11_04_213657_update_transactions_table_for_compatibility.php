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
        Schema::dropIfExists('transactions');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id('tId');
            $table->integer('sId');
            $table->string('transref');
            $table->string('servicename', 100);
            $table->string('servicedesc');
            $table->string('amount', 100);
            $table->tinyInteger('status')->default(0);
            $table->string('oldbal', 100);
            $table->string('newbal', 100);
            $table->float('profit')->default(0);
            $table->datetime('date')->default(now());

            $table->index(['sId']);
            $table->index(['transref']);
            $table->index(['status']);
            $table->index(['date']);
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
