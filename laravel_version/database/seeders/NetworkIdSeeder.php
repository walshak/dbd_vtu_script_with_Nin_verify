<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NetworkId;

class NetworkIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $networks = [
            [
                'nId' => 1,
                'network' => 'MTN',
                'smeId' => '1',
                'giftingId' => '2',
                'corporateId' => '3',
                'airtimeId' => '1',
                'status' => 'On'
            ],
            [
                'nId' => 2,
                'network' => 'Airtel',
                'smeId' => '4',
                'giftingId' => '5',
                'corporateId' => '6',
                'airtimeId' => '2',
                'status' => 'On'
            ],
            [
                'nId' => 3,
                'network' => 'Glo',
                'smeId' => '7',
                'giftingId' => '8',
                'corporateId' => '9',
                'airtimeId' => '3',
                'status' => 'On'
            ],
            [
                'nId' => 4,
                'network' => '9mobile',
                'smeId' => '10',
                'giftingId' => '11',
                'corporateId' => '12',
                'airtimeId' => '4',
                'status' => 'On'
            ]
        ];

        foreach ($networks as $network) {
            NetworkId::updateOrCreate(
                ['nId' => $network['nId']],
                $network
            );
        }
    }
}
