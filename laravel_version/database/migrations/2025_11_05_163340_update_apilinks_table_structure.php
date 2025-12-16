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
        Schema::table('apilinks', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('apilinks', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('type');
            }
            if (!Schema::hasColumn('apilinks', 'priority')) {
                $table->integer('priority')->default(1)->after('is_active');
            }
            if (!Schema::hasColumn('apilinks', 'auth_type')) {
                $table->string('auth_type', 20)->default('token')->after('priority');
            }
            if (!Schema::hasColumn('apilinks', 'auth_params')) {
                $table->json('auth_params')->nullable()->after('auth_type');
            }
            if (!Schema::hasColumn('apilinks', 'success_rate')) {
                $table->decimal('success_rate', 5, 2)->default(100.00)->after('auth_params');
            }
            if (!Schema::hasColumn('apilinks', 'response_time')) {
                $table->integer('response_time')->default(0)->after('success_rate');
            }
            if (!Schema::hasColumn('apilinks', 'last_checked')) {
                $table->datetime('last_checked')->nullable()->after('response_time');
            }
            if (!Schema::hasColumn('apilinks', 'created_at')) {
                $table->timestamps();
            }
        });

        // Try to add indexes (will be skipped if they already exist in base migration)
        try {
            Schema::table('apilinks', function (Blueprint $table) {
                $table->index(['type', 'is_active']);
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }

        try {
            Schema::table('apilinks', function (Blueprint $table) {
                $table->index(['priority', 'success_rate']);
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apilinks', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_active']);
            $table->dropIndex(['priority', 'success_rate']);
        });

        Schema::table('apilinks', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'priority', 'auth_type', 'auth_params', 'success_rate', 'response_time', 'last_checked']);
        });
    }
};
