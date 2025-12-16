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
        Schema::create('contact', function (Blueprint $table) {
            $table->id('cId');
            $table->string('cName');
            $table->string('cEmail');
            $table->string('cSubject');
            $table->text('cMessage');
            $table->string('cPhone')->nullable();
            $table->tinyInteger('cStatus')->default(0); // 0=unread, 1=read, 2=replied
            $table->timestamp('dPosted')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact');
    }
};
