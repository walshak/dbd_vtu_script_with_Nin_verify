<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NetworkId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AirtimeViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the airtime view renders without Blade template errors.
     *
     * @return void
     */
    public function test_airtime_view_renders_without_errors()
    {
        // Create a test user
        $user = User::factory()->create([
            'wallet_balance' => 1000.00
        ]);

        // Create test network data
        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png',
            'brand_color' => '#FFCC00'
        ]);

        NetworkId::create([
            'network' => 'glo',
            'logoPath' => '/assets/images/glo.png',
            'brand_color' => '#00A859'
        ]);

        // Act as the user and visit the airtime page
        $response = $this->actingAs($user)->get('/buy-airtime');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the view is correct
        $response->assertViewIs('airtime.index');

        // Assert the view has the required data
        $response->assertViewHas('networks');

        // Assert key elements are present in the rendered HTML
        $response->assertSee('Buy Airtime');
        $response->assertSee('Select Network Provider');
        $response->assertSee('Enter Phone Number');
        $response->assertSee('Select Amount');
        $response->assertSee('Complete Purchase');
    }

    /**
     * Test that all Blade sections are properly matched.
     *
     * @return void
     */
    public function test_blade_sections_are_properly_matched()
    {
        $user = User::factory()->create();

        // Create at least one network
        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        // This should not throw InvalidArgumentException
        $response = $this->actingAs($user)->get('/buy-airtime');

        $response->assertStatus(200);
        $response->assertDontSee('InvalidArgumentException');
        $response->assertDontSee('Cannot end a section without first starting one');
    }
}
