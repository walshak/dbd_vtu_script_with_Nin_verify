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
        // Add missing columns to users table to match PHP app functionality
        Schema::table('users', function (Blueprint $table) {
            // User identification and contact
            $table->string('phone', 20)->unique()->after('email');
            $table->string('state', 50)->nullable()->after('phone');
            
            // User type and status
            $table->tinyInteger('user_type')->default(1)->after('state'); // 1=User, 2=Agent, 3=Vendor
            $table->tinyInteger('reg_status')->default(3)->after('user_type'); // Registration status
            $table->smallInteger('ver_code')->default(0)->after('reg_status'); // Verification code
            
            // Security
            $table->integer('transaction_pin')->default(1234)->after('ver_code');
            $table->tinyInteger('pin_status')->default(0)->after('transaction_pin'); // PIN enabled/disabled
            
            // Financial
            $table->decimal('wallet_balance', 15, 2)->default(0)->after('pin_status');
            $table->decimal('referral_wallet', 15, 2)->default(0)->after('wallet_balance');
            
            // Banking details
            $table->string('bank_account', 20)->nullable()->after('referral_wallet');
            $table->string('rolex_bank', 20)->nullable()->after('bank_account');
            $table->string('sterling_bank', 20)->nullable()->after('rolex_bank');
            $table->string('fidelity_bank', 20)->nullable()->after('sterling_bank');
            $table->string('bank_name', 30)->nullable()->after('fidelity_bank');
            
            // API and referral
            $table->string('api_key', 200)->nullable()->after('bank_name');
            $table->string('referral_code', 15)->nullable()->after('api_key');
            $table->integer('referred_by')->nullable()->after('referral_code');
            
            // Activity tracking
            $table->timestamp('last_activity')->nullable()->after('referred_by');
        });

        // Update transactions table to match PHP app structure
        Schema::table('transactions', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('transactions', 'service_name')) {
                $table->string('service_name', 50)->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'service_description')) {
                $table->text('service_description')->after('service_name');
            }
            if (!Schema::hasColumn('transactions', 'old_balance')) {
                $table->decimal('old_balance', 15, 2)->after('service_description');
            }
            if (!Schema::hasColumn('transactions', 'new_balance')) {
                $table->decimal('new_balance', 15, 2)->after('old_balance');
            }
            if (!Schema::hasColumn('transactions', 'api_response')) {
                $table->text('api_response')->nullable()->after('new_balance');
            }
            if (!Schema::hasColumn('transactions', 'profit')) {
                $table->decimal('profit', 10, 2)->default(0)->after('api_response');
            }
        });

        // Add indexes for better performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone');
            $table->index('user_type');
            $table->index('referral_code');
            $table->index('api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['user_type']);
            $table->dropIndex(['referral_code']);
            $table->dropIndex(['api_key']);
            
            $table->dropColumn([
                'phone', 'state', 'user_type', 'reg_status', 'ver_code',
                'transaction_pin', 'pin_status', 'wallet_balance', 'referral_wallet',
                'bank_account', 'rolex_bank', 'sterling_bank', 'fidelity_bank', 'bank_name',
                'api_key', 'referral_code', 'referred_by', 'last_activity'
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'service_name', 'service_description', 'old_balance', 
                'new_balance', 'api_response', 'profit'
            ]);
        });
    }
};