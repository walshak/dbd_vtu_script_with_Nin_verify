<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CableId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CableTVViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the cable TV view renders without Blade template errors.
     *
     * @return void
     */
    public function test_cable_tv_view_renders_without_errors()
    {
        // Create a test user
        $user = User::factory()->create([
            'wallet_balance' => 5000.00
        ]);

        // Create test cable provider data
        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv'
        ]);

        CableId::create([
            'network' => 'gotv',
            'name' => 'GOTV',
            'provider' => 'gotv'
        ]);

        // Act as the user and visit the cable TV page
        $response = $this->actingAs($user)->get('/cable-tv');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the view is correct
        $response->assertViewIs('cable-tv.index');

        // Assert the view has the required data
        $response->assertViewHas('providers');

        // Assert key elements are present in the rendered HTML
        $response->assertSee('Cable TV Subscription');
        $response->assertSee('Select Cable Provider');
        $response->assertSee('Enter IUC/Smart Card Number');
        $response->assertSee('Select Subscription Package');
    }

    /**
     * Test that all Blade sections are properly matched.
     *
     * @return void
     */
    public function test_blade_sections_are_properly_matched()
    {
        $user = User::factory()->create();

        // Create at least one provider
        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv'
        ]);

        // This should not throw InvalidArgumentException
        $response = $this->actingAs($user)->get('/cable-tv');

        $response->assertStatus(200);
        $response->assertDontSee('InvalidArgumentException');
        $response->assertDontSee('Cannot end a section without first starting one');
    }

    /**
     * Test that the view uses the correct layout.
     *
     * @return void
     */
    public function test_cable_tv_view_extends_correct_layout()
    {
        $user = User::factory()->create();

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv'
        ]);

        $response = $this->actingAs($user)->get('/cable-tv');

        $response->assertStatus(200);

        // Check for layout elements
        $response->assertSee('Wallet Balance');
    }
}
