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
        Schema::create('sitesettings', function (Blueprint $table) {
            $table->id('sId');
            $table->string('sitename')->nullable();
            $table->string('siteurl')->nullable();
            $table->text('apidocumentation')->nullable();
            $table->decimal('referalupgradebonus', 10, 2)->default(0);
            $table->decimal('referalairtimebonus', 10, 2)->default(0);
            $table->decimal('referaldatabonus', 10, 2)->default(0);
            $table->decimal('referalwalletbonus', 10, 2)->default(0);
            $table->decimal('referalcablebonus', 10, 2)->default(0);
            $table->decimal('referalexambonus', 10, 2)->default(0);
            $table->decimal('referalmeterbonus', 10, 2)->default(0);
            $table->decimal('wallettowalletcharges', 10, 2)->default(0);
            $table->decimal('agentupgrade', 10, 2)->default(0);
            $table->decimal('vendorupgrade', 10, 2)->default(0);
            $table->string('accountname')->nullable();
            $table->string('accountno')->nullable();
            $table->string('bankname')->nullable();
            $table->decimal('electricitycharges', 10, 2)->default(0);
            $table->integer('airtimemin')->default(50);
            $table->integer('airtimemax')->default(10000);
            $table->string('sitecolor')->default('blue');
            $table->string('loginstyle')->default('default');
            $table->string('homestyle')->default('default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sitesettings');
    }
};
