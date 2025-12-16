<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            // Admin and basic setup
            AdminSeeder::class,

            // Configuration and features
            ConfigurationSeeder::class,
            FeatureToggleSeeder::class,

            // API setup
            ApiLinksSeeder::class,
            ApiConfigurationSeeder::class,
            DefaultApiProviderSeeder::class,

            // Network and service data
            NetworkIdSeeder::class,
            DataPlanSeeder::class,
            AirtimeSeeder::class,
            CableIdSeeder::class,
            CablePlanSeeder::class,
        ]);
    }
}
