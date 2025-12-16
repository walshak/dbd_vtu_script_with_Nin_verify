<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Airtime;

class AirtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airtimeData = [
            // MTN Airtime VTU
            ['nId' => 1, 'airtimeAmount' => 100, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
            ['nId' => 1, 'airtimeAmount' => 200, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
            ['nId' => 1, 'airtimeAmount' => 500, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
            ['nId' => 1, 'airtimeAmount' => 1000, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],

            // MTN Share and Sell
            ['nId' => 1, 'airtimeAmount' => 100, 'userDiscount' => 4.0, 'agentDiscount' => 5.0, 'apiDiscount' => 6.0, 'airtimeType' => 'Share and Sell'],
            ['nId' => 1, 'airtimeAmount' => 200, 'userDiscount' => 4.0, 'agentDiscount' => 5.0, 'apiDiscount' => 6.0, 'airtimeType' => 'Share and Sell'],
            ['nId' => 1, 'airtimeAmount' => 500, 'userDiscount' => 4.0, 'agentDiscount' => 5.0, 'apiDiscount' => 6.0, 'airtimeType' => 'Share and Sell'],

            // Airtel Airtime VTU
            ['nId' => 2, 'airtimeAmount' => 100, 'userDiscount' => 2.0, 'agentDiscount' => 2.5, 'apiDiscount' => 3.0, 'airtimeType' => 'VTU'],
            ['nId' => 2, 'airtimeAmount' => 200, 'userDiscount' => 2.0, 'agentDiscount' => 2.5, 'apiDiscount' => 3.0, 'airtimeType' => 'VTU'],
            ['nId' => 2, 'airtimeAmount' => 500, 'userDiscount' => 2.0, 'agentDiscount' => 2.5, 'apiDiscount' => 3.0, 'airtimeType' => 'VTU'],
            ['nId' => 2, 'airtimeAmount' => 1000, 'userDiscount' => 2.0, 'agentDiscount' => 2.5, 'apiDiscount' => 3.0, 'airtimeType' => 'VTU'],

            // Glo Airtime VTU
            ['nId' => 3, 'airtimeAmount' => 100, 'userDiscount' => 3.0, 'agentDiscount' => 3.5, 'apiDiscount' => 4.0, 'airtimeType' => 'VTU'],
            ['nId' => 3, 'airtimeAmount' => 200, 'userDiscount' => 3.0, 'agentDiscount' => 3.5, 'apiDiscount' => 4.0, 'airtimeType' => 'VTU'],
            ['nId' => 3, 'airtimeAmount' => 500, 'userDiscount' => 3.0, 'agentDiscount' => 3.5, 'apiDiscount' => 4.0, 'airtimeType' => 'VTU'],
            ['nId' => 3, 'airtimeAmount' => 1000, 'userDiscount' => 3.0, 'agentDiscount' => 3.5, 'apiDiscount' => 4.0, 'airtimeType' => 'VTU'],

            // 9mobile Airtime VTU
            ['nId' => 4, 'airtimeAmount' => 100, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
            ['nId' => 4, 'airtimeAmount' => 200, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
            ['nId' => 4, 'airtimeAmount' => 500, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
            ['nId' => 4, 'airtimeAmount' => 1000, 'userDiscount' => 2.5, 'agentDiscount' => 3.0, 'apiDiscount' => 3.5, 'airtimeType' => 'VTU'],
        ];

        foreach ($airtimeData as $airtime) {
            Airtime::create($airtime);
        }
    }
}
