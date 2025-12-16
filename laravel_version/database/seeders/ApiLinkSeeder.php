<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApiLink;

class ApiLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete existing Uzobest configurations if any
        ApiLink::where('name', 'LIKE', '%uzobest%')->delete();
        ApiLink::where('name', 'LIKE', '%Uzobest%')->delete();

        // Create primary Uzobest API configuration
        ApiLink::create([
            'name' => 'Uzobest Primary API',
            'type' => 'primary',
            'value' => 'https://uzobestgsm.com/api',
            'is_active' => true,
            'priority' => 1,
            'auth_type' => 'token',
            'auth_params' => [
                'token' => env('UZOBEST_API_TOKEN', '66f2e5c39ac8640f13cd888f161385b12f7e5e92'),
                'header_name' => 'Authorization',
                'header_format' => 'Token {token}'
            ],
            'success_rate' => 95.0,
            'response_time' => 1200, // milliseconds
        ]);

        // Create backup Uzobest configuration (if needed)
        ApiLink::create([
            'name' => 'Uzobest Backup API',
            'type' => 'backup',
            'value' => 'https://uzobestgsm.com/api',
            'is_active' => false, // Disabled by default
            'priority' => 2,
            'auth_type' => 'token',
            'auth_params' => [
                'token' => env('UZOBEST_API_TOKEN_BACKUP', '66f2e5c39ac8640f13cd888f161385b12f7e5e92'),
                'header_name' => 'Authorization',
                'header_format' => 'Token {token}'
            ],
            'success_rate' => 0.0,
            'response_time' => 0,
        ]);

        $this->command->info('✅ Uzobest API configurations seeded successfully');
    }
}
