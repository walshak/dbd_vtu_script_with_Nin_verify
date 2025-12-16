<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiLinks = [
            // Topupmate Provider
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/user/', 'type' => 'Wallet', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/airtime/', 'type' => 'Airtime', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/data/', 'type' => 'Data', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/cabletv/verify/', 'type' => 'CableVer', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/cabletv/', 'type' => 'Cable', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/electricity/verify/', 'type' => 'ElectricityVer', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/electricity/', 'type' => 'Electricity', 'priority' => 1],
            ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/exam/', 'type' => 'Exam', 'priority' => 1],

            // N3T Data Provider
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/user/', 'type' => 'Wallet', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/topup/', 'type' => 'Airtime', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/data/', 'type' => 'Data', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/cable/cable-validation', 'type' => 'CableVer', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/cable/', 'type' => 'Cable', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/bill/bill-validation', 'type' => 'ElectricityVer', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/bill/', 'type' => 'Electricity', 'priority' => 2],
            ['name' => 'N3T Data', 'value' => 'https://n3tdata.com/api/exam/', 'type' => 'Exam', 'priority' => 2],

            // Bilalsadasub Provider
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/user/', 'type' => 'Wallet', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/topup/', 'type' => 'Airtime', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/data/', 'type' => 'Data', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/cable/cable-validation', 'type' => 'CableVer', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/cable/', 'type' => 'Cable', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/bill/bill-validation', 'type' => 'ElectricityVer', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/bill/', 'type' => 'Electricity', 'priority' => 3],
            ['name' => 'Bilalsadasub', 'value' => 'https://bilalsadasub.com/api/exam/', 'type' => 'Exam', 'priority' => 3],

            // Aabaxztech Provider
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/user/', 'type' => 'Wallet', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/topup/', 'type' => 'Airtime', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/data/', 'type' => 'Data', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/validateiuc', 'type' => 'CableVer', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/cablesub/', 'type' => 'Cable', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/validatemeter', 'type' => 'ElectricityVer', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/billpayment/', 'type' => 'Electricity', 'priority' => 4],
            ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/epin/', 'type' => 'Exam', 'priority' => 4],

            // Maskawasub Provider
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/user/', 'type' => 'Wallet', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/topup/', 'type' => 'Airtime', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/data/', 'type' => 'Data', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/validateiuc', 'type' => 'CableVer', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/cablesub/', 'type' => 'Cable', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/validatemeter', 'type' => 'ElectricityVer', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/billpayment/', 'type' => 'Electricity', 'priority' => 5],
            ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/epin/', 'type' => 'Exam', 'priority' => 5],

            // Husmodataapi Provider
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/user/', 'type' => 'Wallet', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/topup/', 'type' => 'Airtime', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/data/', 'type' => 'Data', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/validateiuc', 'type' => 'CableVer', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/cablesub/', 'type' => 'Cable', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/validatemeter', 'type' => 'ElectricityVer', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/billpayment/', 'type' => 'Electricity', 'priority' => 6],
            ['name' => 'Husmodataapi', 'value' => 'https://husmodataapi.com/api/epin/', 'type' => 'Exam', 'priority' => 6],

            // Gongozconcept Provider
            ['name' => 'Gongozconcept', 'value' => 'https://gongozconcept.com/api/user/', 'type' => 'Wallet', 'priority' => 7],
            ['name' => 'Gongozconcept', 'value' => 'https://gongozconcept.com/api/topup/', 'type' => 'Airtime', 'priority' => 7],
            ['name' => 'Gongozconcept', 'value' => 'https://gongozconcept.com/api/data/', 'type' => 'Data', 'priority' => 7],
            
            // Data Pin Provider
            ['name' => 'DataPin Provider', 'value' => 'https://datapinapi.com/api/', 'type' => 'Data Pin', 'priority' => 1],
        ];

        foreach ($apiLinks as $link) {
            DB::table('apilinks')->insert([
                'name' => $link['name'],
                'value' => $link['value'],
                'type' => $link['type'],
                'is_active' => true,
                'priority' => $link['priority'],
                'success_rate' => 100.00,
                'response_time' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
