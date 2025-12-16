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
            // Add indexed column for faster Monnify account lookup
            $table->string('monnify_account_number')->nullable()->after('monnify_reference')->index();
        });
        
        // Populate existing data
        DB::statement("
            UPDATE users 
            SET monnify_account_number = JSON_EXTRACT(virtual_accounts, '$[0].account_number')
            WHERE virtual_accounts IS NOT NULL 
            AND JSON_VALID(virtual_accounts)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['monnify_account_number']);
            $table->dropColumn('monnify_account_number');
        });
    }
};
