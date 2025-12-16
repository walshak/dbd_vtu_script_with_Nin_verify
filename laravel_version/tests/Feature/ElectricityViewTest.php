<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ElectricityProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElectricityViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the electricity view renders without Blade template errors.
     *
     * @return void
     */
    public function test_electricity_view_renders_without_errors()
    {
        // Create a test user
        $user = User::factory()->create([
            'sWallet' => 5000.00
        ]);

        // Create test electricity provider data
        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50
        ]);

        ElectricityProvider::create([
            'ePlan' => 'IKEDC',
            'ePrice' => 46.00
        ]);

        // Act as the user and visit the electricity page
        $response = $this->actingAs($user)->get('/electricity');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the view is correct
        $response->assertViewIs('electricity.index');

        // Assert the view has the required data
        $response->assertViewHas('providers');

        // Assert key elements are present in the rendered HTML
        $response->assertSee('Electricity Bill Payment');
        $response->assertSee('Select Electricity Distribution Company');
        $response->assertSee('Select Meter Type');
        $response->assertSee('Enter Meter Number');
    }

    /**
     * Test that all Blade sections are properly matched.
     *
     * @return void
     */
    public function test_blade_sections_are_properly_matched()
    {
        $user = User::factory()->create([
            'sWallet' => 5000.00
        ]);

        // Create at least one provider
        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50
        ]);

        // This should not throw InvalidArgumentException
        $response = $this->actingAs($user)->get('/electricity');

        $response->assertStatus(200);
        $response->assertDontSee('InvalidArgumentException');
        $response->assertDontSee('Cannot end a section without first starting one');
    }

    /**
     * Test that the view uses the correct layout.
     *
     * @return void
     */
    public function test_electricity_view_extends_correct_layout()
    {
        $user = User::factory()->create([
            'sWallet' => 5000.00
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50
        ]);

        $response = $this->actingAs($user)->get('/electricity');

        $response->assertStatus(200);

        // Check for layout elements
        $response->assertSee('Wallet Balance');
    }

    /**
     * Test that duplicate form fields have been removed.
     *
     * @return void
     */
    public function test_no_duplicate_form_fields()
    {
        $user = User::factory()->create([
            'sWallet' => 5000.00
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50
        ]);

        $response = $this->actingAs($user)->get('/electricity');

        $response->assertStatus(200);

        // Get the response content
        $content = $response->getContent();

        // Count occurrences of meter_number input field
        $meterNumberCount = substr_count($content, 'name="meter_number"');

        // Should only have one meter_number input
        $this->assertEquals(1, $meterNumberCount, 'There should be exactly one meter_number input field');
    }
}
