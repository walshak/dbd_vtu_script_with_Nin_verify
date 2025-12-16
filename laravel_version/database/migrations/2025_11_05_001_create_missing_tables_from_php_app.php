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
        // API Links table - stores multiple API provider configurations
        Schema::create('apilinks', function (Blueprint $table) {
            $table->id('aId');
            $table->string('name', 50);
            $table->text('value');
            $table->string('type', 20);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);
            $table->string('auth_type', 20)->default('token');
            $table->json('auth_params')->nullable();
            $table->decimal('success_rate', 5, 2)->default(100.00);
            $table->integer('response_time')->default(0);
            $table->datetime('last_checked')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['priority', 'success_rate']);
        });

        // Cable ID table - manages cable provider configurations
        Schema::create('cableid', function (Blueprint $table) {
            $table->id('cId');
            $table->string('cableid', 10)->nullable();
            $table->string('provider', 10);
            $table->string('providerStatus', 10)->default('On');
            $table->timestamps();
        });

        // Data Pins table - manages data pin generation and tracking
        Schema::create('datapins', function (Blueprint $table) {
            $table->id('dId');
            $table->integer('sId');
            $table->string('network', 10);
            $table->text('dataPlan');
            $table->text('dataPin');
            $table->text('serialNumber');
            $table->integer('quantity');
            $table->string('transref', 50);
            $table->integer('amount');
            $table->integer('buyprice');
            $table->string('api', 50);
            $table->string('status', 10);
            $table->datetime('date');
            $table->timestamps();
        });

        // Data Tokens table - manages data token generation
        Schema::create('datatokens', function (Blueprint $table) {
            $table->id('dId');
            $table->integer('sId');
            $table->string('network', 10);
            $table->text('planName');
            $table->text('serialNumber');
            $table->text('pin');
            $table->datetime('date');
            $table->timestamps();
        });

        // Electricity ID table - manages electricity provider configurations
        Schema::create('electricityid', function (Blueprint $table) {
            $table->id('eId');
            $table->string('electricityid', 10);
            $table->string('provider', 50);
            $table->string('providerStatus', 10)->default('On');
            $table->timestamps();
        });

        // Exam ID table - manages exam provider configurations
        Schema::create('examid', function (Blueprint $table) {
            $table->id('eId');
            $table->string('examid', 10);
            $table->string('provider', 50);
            $table->integer('price');
            $table->string('providerStatus', 10)->default('On');
            $table->timestamps();
        });

        // Recharge Pins table - manages recharge pin generation and tracking
        Schema::create('rechargepins', function (Blueprint $table) {
            $table->id('rId');
            $table->integer('sId');
            $table->string('network', 10);
            $table->integer('amount');
            $table->text('pins');
            $table->text('serialNumbers');
            $table->integer('quantity');
            $table->string('transref', 50);
            $table->integer('totalPrice');
            $table->integer('buyprice');
            $table->string('api', 50);
            $table->string('status', 10);
            $table->datetime('date');
            $table->timestamps();
        });

        // Recharge Tokens table - manages recharge token generation
        Schema::create('rechargetokens', function (Blueprint $table) {
            $table->id('rId');
            $table->integer('sId');
            $table->string('network', 10);
            $table->integer('amount');
            $table->text('pin');
            $table->text('serialNumber');
            $table->datetime('date');
            $table->timestamps();
        });

        // User Visits table - tracks user visits and analytics
        Schema::create('uservisits', function (Blueprint $table) {
            $table->id('vId');
            $table->string('ipAddress', 50);
            $table->string('userAgent', 255);
            $table->date('visitDate');
            $table->timestamps();
        });

        // Wallet Providers table - manages multiple wallet provider balances
        Schema::create('wallet_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name', 50);
            $table->string('api_key', 255);
            $table->string('api_url', 255);
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);
            $table->timestamps();
        });

        // Referral Bonuses table - tracks referral bonuses and earnings
        Schema::create('referral_bonuses', function (Blueprint $table) {
            $table->id();
            $table->integer('referrer_id');
            $table->integer('referred_id');
            $table->string('service_type', 20);
            $table->decimal('bonus_amount', 10, 2);
            $table->string('transaction_ref', 50);
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });

        // Profit Tracking table - tracks profit per transaction
        Schema::create('profit_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_ref', 50)->unique();
            $table->decimal('revenue', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->string('service_type', 20);
            $table->timestamps();
        });

        // Message System table - internal messaging system
        Schema::create('message_system', function (Blueprint $table) {
            $table->id();
            $table->integer('sender_id');
            $table->integer('receiver_id');
            $table->string('subject', 255);
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });

        // Webhook Logs table - logs webhook responses
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('event_type', 50);
            $table->text('payload');
            $table->text('response')->nullable();
            $table->string('status', 20);
            $table->timestamps();
        });

        // KYC Verification table - manages user verification
        Schema::create('kyc_verification', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('nin', 11)->nullable();
            $table->string('bvn', 11)->nullable();
            $table->string('document_type', 20)->nullable();
            $table->string('document_path', 255)->nullable();
            $table->string('verification_status', 20)->default('pending');
            $table->text('verification_response')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // Bulk SMS table - manages bulk SMS campaigns
        Schema::create('bulk_sms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('message');
            $table->text('recipients'); // JSON array of phone numbers
            $table->integer('total_recipients');
            $table->integer('sent_count')->default(0);
            $table->decimal('cost_per_sms', 5, 2);
            $table->decimal('total_cost', 10, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_sms');
        Schema::dropIfExists('kyc_verification');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('message_system');
        Schema::dropIfExists('profit_tracking');
        Schema::dropIfExists('referral_bonuses');
        Schema::dropIfExists('wallet_providers');
        Schema::dropIfExists('uservisits');
        Schema::dropIfExists('rechargetokens');
        Schema::dropIfExists('rechargepins');
        Schema::dropIfExists('examid');
        Schema::dropIfExists('electricityid');
        Schema::dropIfExists('datatokens');
        Schema::dropIfExists('datapins');
        Schema::dropIfExists('cableid');
        Schema::dropIfExists('apilinks');
    }
};
