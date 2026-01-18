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
            // KYC Fields
            $table->string('nin', 11)->nullable()->after('phone');
            $table->string('bvn', 11)->nullable()->after('nin');
            $table->date('date_of_birth')->nullable()->after('bvn');

            // KYC Status
            $table->enum('kyc_status', ['pending', 'verified', 'rejected'])
                  ->default('pending')
                  ->after('date_of_birth');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
            $table->text('kyc_rejection_reason')->nullable()->after('kyc_verified_at');

            // Which method used
            $table->enum('kyc_method', ['nin', 'bvn', 'none'])->default('none')->after('kyc_rejection_reason');

            // Add indexes for quick lookup
            $table->index('nin');
            $table->index('bvn');
            $table->index('kyc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['nin']);
            $table->dropIndex(['bvn']);
            $table->dropIndex(['kyc_status']);

            $table->dropColumn([
                'nin',
                'bvn',
                'date_of_birth',
                'kyc_status',
                'kyc_verified_at',
                'kyc_rejection_reason',
                'kyc_method'
            ]);
        });
    }
};
