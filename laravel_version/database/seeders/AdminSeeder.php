<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update or create admin user with correct status
        DB::table('sysusers')->updateOrInsert(
            ['sysUsername' => 'walshak1999@gmail.com'],
            [
                'sysName' => 'Super Admin',
                'sysUsername' => 'walshak1999@gmail.com',
                'sysToken' => Hash::make('12345678'),
                'sysRole' => 1, // Super admin role (1 = SUPER_ADMIN)
                'sysStatus' => 0, // Active status (0 = ACTIVE, 1 = BLOCKED)
            ]
        );

        // Show current status
        $admin = DB::table('sysusers')->where('sysUsername', 'walshak1999@gmail.com')->first();

        $this->command->info('Admin user updated successfully!');
        $this->command->info('Email/Username: walshak1999@gmail.com');
        $this->command->info('Password: 12345678');
        $this->command->info('Role: ' . $admin->sysRole);
        $this->command->info('Status: ' . ($admin->sysStatus == 0 ? 'Active' : 'Blocked'));
        $this->command->info('ID: ' . $admin->sysId);
    }
}
