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
        Schema::table('users', function (Blueprint $table) {
            // Check if wallet_balance exists before using it as reference
            if (Schema::hasColumn('users', 'wallet_balance')) {
                $table->json('virtual_accounts')->nullable()->after('wallet_balance');
            } else {
                $table->json('virtual_accounts')->nullable();
            }
            $table->string('monnify_reference')->nullable()->after('virtual_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['virtual_accounts', 'monnify_reference']);
        });
    }
};
