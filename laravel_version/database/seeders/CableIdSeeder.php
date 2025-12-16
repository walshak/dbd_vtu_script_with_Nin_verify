<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CableIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cableProviders = [
            ['cId' => 1, 'cableid' => '1', 'provider' => 'dstv', 'providerStatus' => 'On'],
            ['cId' => 2, 'cableid' => '2', 'provider' => 'gotv', 'providerStatus' => 'On'],
            ['cId' => 3, 'cableid' => '3', 'provider' => 'startimes', 'providerStatus' => 'On'],
        ];

        foreach ($cableProviders as $provider) {
            DB::table('cable_ids')->updateOrInsert(
                ['cId' => $provider['cId']],
                array_merge($provider, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
