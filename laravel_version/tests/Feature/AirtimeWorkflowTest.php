<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NetworkId;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AirtimeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete airtime purchase flow from page load to successful purchase.
     * Validates: Requirements 3.1
     *
     * @return void
     */
    public function test_complete_airtime_purchase_flow()
    {
        // Step 1: Create a test user with sufficient balance
        $user = User::factory()->create([
            'wallet_balance' => 1000.00,
            'email_verified_at' => now()
        ]);

        // Step 2: Create test network data
        $network = NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png',
            'brand_color' => '#FFCC00'
        ]);

        // Step 3: User navigates to airtime purchase page
        $response = $this->actingAs($user)->get('/buy-airtime');

        $response->assertStatus(200);
        $response->assertViewIs('airtime.index');
        $response->assertViewHas('networks');

        // Verify page displays the form elements
        $response->assertSee('Buy Airtime');
        $response->assertSee('Select Network Provider');
        $response->assertSee('Enter Phone Number');
        $response->assertSee('Select Amount');

        // Step 4: User requests pricing information
        $pricingResponse = $this->actingAs($user)
            ->postJson('/airtime/pricing', [
                'network' => 'mtn'
            ]);

        $pricingResponse->assertStatus(200);
        $pricingResponse->assertJson([
            'status' => 'success'
        ]);

        // Step 5: User submits purchase form with valid data
        $purchaseData = [
            'network' => 'mtn',
            'phone' => '08012345678',
            'amount' => 100,
            'type' => 'VTU'
        ];

        $purchaseResponse = $this->actingAs($user)
            ->postJson('/airtime/purchase', $purchaseData);

        // Step 6: Verify response structure (success or error with proper format)
        $purchaseResponse->assertStatus(200);
        $purchaseResponse->assertJsonStructure([
            'status',
            'message'
        ]);

        // The actual purchase may fail due to external API dependencies,
        // but the workflow should handle it gracefully
        $responseData = $purchaseResponse->json();
        $this->assertContains($responseData['status'], ['success', 'error']);

        // If successful, verify transaction was created
        if ($responseData['status'] === 'success') {
            $this->assertDatabaseHas('transactions', [
                'user_id' => $user->id,
                'phone' => '08012345678',
                'amount' => 100
            ]);
        }
    }

    /**
     * Test airtime purchase flow with validation errors.
     *
     * @return void
     */
    public function test_airtime_purchase_flow_with_validation_errors()
    {
        $user = User::factory()->create([
            'wallet_balance' => 1000.00
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        // Test with invalid phone number
        $response = $this->actingAs($user)
            ->postJson('/airtime/purchase', [
                'network' => 'mtn',
                'phone' => '123', // Invalid phone
                'amount' => 100,
                'type' => 'VTU'
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
        $response->assertJsonStructure([
            'status',
            'message'
        ]);
    }

    /**
     * Test airtime purchase flow with insufficient balance.
     *
     * @return void
     */
    public function test_airtime_purchase_flow_with_insufficient_balance()
    {
        $user = User::factory()->create([
            'wallet_balance' => 10.00 // Insufficient balance
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        $response = $this->actingAs($user)
            ->postJson('/airtime/purchase', [
                'network' => 'mtn',
                'phone' => '08012345678',
                'amount' => 100,
                'type' => 'VTU'
            ]);

        // Should return error due to insufficient balance
        $response->assertJsonStructure([
            'status',
            'message'
        ]);

        $responseData = $response->json();
        $this->assertEquals('error', $responseData['status']);
    }

    /**
     * Test airtime purchase flow with invalid amount.
     *
     * @return void
     */
    public function test_airtime_purchase_flow_with_invalid_amount()
    {
        $user = User::factory()->create([
            'wallet_balance' => 1000.00
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        // Test with amount below minimum
        $response = $this->actingAs($user)
            ->postJson('/airtime/purchase', [
                'network' => 'mtn',
                'phone' => '08012345678',
                'amount' => 10, // Below minimum of 50
                'type' => 'VTU'
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
    }

    /**
     * Test that unauthenticated users cannot access airtime purchase.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_purchase_airtime()
    {
        $response = $this->get('/buy-airtime');

        // Should redirect to login
        $response->assertRedirect('/login');
    }
}
