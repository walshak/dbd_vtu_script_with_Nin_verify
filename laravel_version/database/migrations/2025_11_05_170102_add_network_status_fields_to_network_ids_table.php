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
        Schema::table('network_ids', function (Blueprint $table) {
            // Add status fields for network services
            if (!Schema::hasColumn('network_ids', 'networkStatus')) {
                $table->enum('networkStatus', ['On', 'Off'])->default('On')->after('corporateId');
            }
            if (!Schema::hasColumn('network_ids', 'vtuStatus')) {
                $table->enum('vtuStatus', ['On', 'Off'])->default('On')->after('networkStatus');
            }
            if (!Schema::hasColumn('network_ids', 'sharesellStatus')) {
                $table->enum('sharesellStatus', ['On', 'Off'])->default('On')->after('vtuStatus');
            }
            if (!Schema::hasColumn('network_ids', 'smeStatus')) {
                $table->enum('smeStatus', ['On', 'Off'])->default('On')->after('sharesellStatus');
            }
            if (!Schema::hasColumn('network_ids', 'giftingStatus')) {
                $table->enum('giftingStatus', ['On', 'Off'])->default('On')->after('smeStatus');
            }
            if (!Schema::hasColumn('network_ids', 'corporateStatus')) {
                $table->enum('corporateStatus', ['On', 'Off'])->default('On')->after('giftingStatus');
            }
            if (!Schema::hasColumn('network_ids', 'airtimepinStatus')) {
                $table->enum('airtimepinStatus', ['On', 'Off'])->default('On')->after('corporateStatus');
            }
            if (!Schema::hasColumn('network_ids', 'datapinStatus')) {
                $table->enum('datapinStatus', ['On', 'Off'])->default('On')->after('airtimepinStatus');
            }
            
            // Add network ID field if missing
            if (!Schema::hasColumn('network_ids', 'networkid')) {
                $table->integer('networkid')->default(1)->after('network');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_ids', function (Blueprint $table) {
            $table->dropColumn([
                'networkStatus', 'vtuStatus', 'sharesellStatus', 'smeStatus', 
                'giftingStatus', 'corporateStatus', 'airtimepinStatus', 'datapinStatus', 'networkid'
            ]);
        });
    }
};
