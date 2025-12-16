<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CablePlan;

class CablePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: cableprovider maps to cable_ids.cId (1=DSTV, 2=GOTV, 3=Startimes)
        $cablePlans = [
            // DSTV Plans (cableprovider = 1)
            ['planid' => 'dstv-padi', 'name' => 'DStv Padi', 'price' => '2150', 'userprice' => '2150', 'agentprice' => '2100', 'vendorprice' => '2050', 'cableprovider' => 1, 'day' => '30', 'status' => 'active'],
            ['planid' => 'dstv-yanga', 'name' => 'DStv Yanga', 'price' => '2950', 'userprice' => '2950', 'agentprice' => '2900', 'vendorprice' => '2850', 'cableprovider' => 1, 'day' => '30', 'status' => 'active'],
            ['planid' => 'dstv-confam', 'name' => 'DStv Confam', 'price' => '5300', 'userprice' => '5300', 'agentprice' => '5200', 'vendorprice' => '5100', 'cableprovider' => 1, 'day' => '30', 'status' => 'active'],
            ['planid' => 'dstv-compact', 'name' => 'DStv Compact', 'price' => '9000', 'userprice' => '9000', 'agentprice' => '8900', 'vendorprice' => '8800', 'cableprovider' => 1, 'day' => '30', 'status' => 'active'],
            ['planid' => 'dstv-compact-plus', 'name' => 'DStv Compact Plus', 'price' => '14250', 'userprice' => '14250', 'agentprice' => '14100', 'vendorprice' => '14000', 'cableprovider' => 1, 'day' => '30', 'status' => 'active'],
            ['planid' => 'dstv-premium', 'name' => 'DStv Premium', 'price' => '21000', 'userprice' => '21000', 'agentprice' => '20800', 'vendorprice' => '20600', 'cableprovider' => 1, 'day' => '30', 'status' => 'active'],

            // GOtv Plans (cableprovider = 2)
            ['planid' => 'gotv-smallie', 'name' => 'GOtv Smallie', 'price' => '900', 'userprice' => '900', 'agentprice' => '880', 'vendorprice' => '860', 'cableprovider' => 2, 'day' => '30', 'status' => 'active'],
            ['planid' => 'gotv-jinja', 'name' => 'GOtv Jinja', 'price' => '1900', 'userprice' => '1900', 'agentprice' => '1850', 'vendorprice' => '1800', 'cableprovider' => 2, 'day' => '30', 'status' => 'active'],
            ['planid' => 'gotv-jolli', 'name' => 'GOtv Jolli', 'price' => '2800', 'userprice' => '2800', 'agentprice' => '2750', 'vendorprice' => '2700', 'cableprovider' => 2, 'day' => '30', 'status' => 'active'],
            ['planid' => 'gotv-max', 'name' => 'GOtv Max', 'price' => '4150', 'userprice' => '4150', 'agentprice' => '4100', 'vendorprice' => '4050', 'cableprovider' => 2, 'day' => '30', 'status' => 'active'],
            ['planid' => 'gotv-supa', 'name' => 'GOtv Supa', 'price' => '5500', 'userprice' => '5500', 'agentprice' => '5400', 'vendorprice' => '5300', 'cableprovider' => 2, 'day' => '30', 'status' => 'active'],

            // Startimes Plans (cableprovider = 3)
            ['planid' => 'startimes-nova', 'name' => 'Startimes Nova', 'price' => '900', 'userprice' => '900', 'agentprice' => '880', 'vendorprice' => '860', 'cableprovider' => 3, 'day' => '30', 'status' => 'active'],
            ['planid' => 'startimes-basic', 'name' => 'Startimes Basic', 'price' => '1850', 'userprice' => '1850', 'agentprice' => '1800', 'vendorprice' => '1750', 'cableprovider' => 3, 'day' => '30', 'status' => 'active'],
            ['planid' => 'startimes-smart', 'name' => 'Startimes Smart', 'price' => '2480', 'userprice' => '2480', 'agentprice' => '2400', 'vendorprice' => '2350', 'cableprovider' => 3, 'day' => '30', 'status' => 'active'],
            ['planid' => 'startimes-classic', 'name' => 'Startimes Classic', 'price' => '2750', 'userprice' => '2750', 'agentprice' => '2700', 'vendorprice' => '2650', 'cableprovider' => 3, 'day' => '30', 'status' => 'active'],
            ['planid' => 'startimes-super', 'name' => 'Startimes Super', 'price' => '4200', 'userprice' => '4200', 'agentprice' => '4100', 'vendorprice' => '4000', 'cableprovider' => 3, 'day' => '30', 'status' => 'active'],
        ];

        foreach ($cablePlans as $plan) {
            CablePlan::updateOrCreate(
                ['planid' => $plan['planid']],
                $plan
            );
        }
    }
}
