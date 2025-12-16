<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NetworkId;
use App\Models\CableId;
use App\Models\ElectricityProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all service views receive expected data from controllers.
     * Validates Requirement 3.5: All service pages display UI elements correctly
     *
     * @return void
     */
    public function test_all_views_receive_expected_data()
    {
        // Create a test user
        $user = User::factory()->create([
            'wallet_balance' => 5000.00
        ]);

        // Create test data for airtime/data views
        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png',
            'brand_color' => '#FFCC00'
        ]);

        // Create test data for cable TV view
        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv'
        ]);

        // Create test data for electricity view
        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50
        ]);

        // Test airtime view data
        $airtimeResponse = $this->actingAs($user)->get('/buy-airtime');
        $airtimeResponse->assertStatus(200);
        $airtimeResponse->assertViewHas('networks');
        $this->assertNotEmpty($airtimeResponse->viewData('networks'));

        // Test cable TV view data
        $cableResponse = $this->actingAs($user)->get('/cable-tv');
        $cableResponse->assertStatus(200);
        $cableResponse->assertViewHas('providers');
        $this->assertNotEmpty($cableResponse->viewData('providers'));

        // Test electricity view data
        $electricityResponse = $this->actingAs($user)->get('/electricity');
        $electricityResponse->assertStatus(200);
        $electricityResponse->assertViewHas('providers');
        $this->assertNotEmpty($electricityResponse->viewData('providers'));
    }

    /**
     * Test that airtime view receives correct network data structure.
     *
     * @return void
     */
    public function test_airtime_view_receives_correct_data_structure()
    {
        $user = User::factory()->create();

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png',
            'brand_color' => '#FFCC00'
        ]);

        $response = $this->actingAs($user)->get('/buy-airtime');

        $response->assertStatus(200);
        $response->assertViewHas('networks');

        $networks = $response->viewData('networks');
        $this->assertIsIterable($networks);
        $this->assertGreaterThan(0, count($networks));

        // Verify first network has expected properties
        $firstNetwork = $networks->first();
        $this->assertNotNull($firstNetwork->network);
        $this->assertNotNull($firstNetwork->logoPath);
    }

    /**
     * Test that cable TV view receives correct provider data structure.
     *
     * @return void
     */
    public function test_cable_tv_view_receives_correct_data_structure()
    {
        $user = User::factory()->create();

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv'
        ]);

        $response = $this->actingAs($user)->get('/cable-tv');

        $response->assertStatus(200);
        $response->assertViewHas('providers');

        $providers = $response->viewData('providers');
        $this->assertIsIterable($providers);
        $this->assertGreaterThan(0, count($providers));

        // Verify first provider has expected properties
        $firstProvider = $providers->first();
        $this->assertObjectHasProperty('network', $firstProvider);
        $this->assertObjectHasProperty('name', $firstProvider);
    }

    /**
     * Test that electricity view receives correct provider data structure.
     *
     * @return void
     */
    public function test_electricity_view_receives_correct_data_structure()
    {
        $user = User::factory()->create();

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50
        ]);

        $response = $this->actingAs($user)->get('/electricity');

        $response->assertStatus(200);
        $response->assertViewHas('providers');

        $providers = $response->viewData('providers');
        $this->assertIsIterable($providers);
        $this->assertGreaterThan(0, count($providers));

        // Verify first provider has expected properties
        $firstProvider = $providers->first();
        $this->assertObjectHasProperty('ePlan', $firstProvider);
        $this->assertObjectHasProperty('ePrice', $firstProvider);
    }
}
