<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NetworkId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the data view renders without Blade template errors.
     *
     * @return void
     */
    public function test_data_view_renders_without_errors()
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

        // Act as the user and visit the data page
        $response = $this->actingAs($user)->get('/buy-data');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the view is correct
        $response->assertViewIs('data.index');

        // Assert the view has the required data
        $response->assertViewHas('networks');

        // Assert key elements are present in the rendered HTML
        $response->assertSee('Buy Data Bundle');
        $response->assertSee('Select Network Provider');
        $response->assertSee('Choose Data Type');
        $response->assertSee('Enter Phone Number');
        $response->assertSee('Complete Data Purchase');
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
        $response = $this->actingAs($user)->get('/buy-data');

        $response->assertStatus(200);
        $response->assertDontSee('InvalidArgumentException');
        $response->assertDontSee('Cannot end a section without first starting one');
    }

    /**
     * Test that the view uses proper layout extension.
     *
     * @return void
     */
    public function test_view_extends_correct_layout()
    {
        $user = User::factory()->create();

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        $response = $this->actingAs($user)->get('/buy-data');

        // Verify the page has the user layout structure
        $response->assertStatus(200);
        $response->assertSee('Data Bundle Purchase');
    }
}
