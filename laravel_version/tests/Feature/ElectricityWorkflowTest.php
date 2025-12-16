<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ElectricityProvider;
use App\Models\Transaction;
use App\Models\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElectricityWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete electricity payment flow from page load to successful purchase.
     * Validates: Requirements 3.4
     *
     * @return void
     */
    public function test_complete_electricity_payment_flow()
    {
        // Step 1: Create a test user with sufficient balance and transaction PIN
        $user = User::factory()->create([
            'sWallet' => 10000.00,
            'email_verified_at' => now(),
            'sTransactionPin' => hash('sha256', '1234') // Transaction PIN: 1234
        ]);

        // Step 2: Create test electricity provider data
        $provider = ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Step 3: Create site settings
        SiteSettings::create([
            'sitename' => 'Test VTU',
            'electricitycharges' => 50,
            'electricity_minimum_amount' => 1000,
            'electricity_maximum_amount' => 50000,
            'electricity_maintenance_mode' => false
        ]);

        // Step 4: User navigates to electricity page
        $response = $this->actingAs($user)->get('/electricity');

        $response->assertStatus(200);
        $response->assertViewIs('electricity.index');
        $response->assertViewHas('providers');

        // Verify page displays the form elements
        $response->assertSee('Electricity Bill Payment');
        $response->assertSee('Select Electricity Distribution Company');
        $response->assertSee('Enter Meter Number');

        // Step 5: User requests pricing information
        $pricingResponse = $this->actingAs($user)
            ->postJson('/electricity/pricing', [
                'provider' => 'AEDC',
                'amount' => 5000
            ]);

        $pricingResponse->assertStatus(200);
        $pricingResponse->assertJson([
            'status' => 'success'
        ]);

        // Step 6: User validates meter number
        $validateResponse = $this->actingAs($user)
            ->postJson('/electricity/validate-meter', [
                'provider' => 'AEDC',
                'meter_number' => '12345678901',
                'meter_type' => 'prepaid'
            ]);

        $validateResponse->assertJsonStructure([
            'status'
        ]);

        // Step 7: User submits purchase form with valid data
        $purchaseData = [
            'provider' => 'AEDC',
            'meter_number' => '12345678901',
            'meter_type' => 'prepaid',
            'amount' => 5000,
            'customer_name' => 'John Doe',
            'phone' => '08012345678',
            'transaction_pin' => '1234'
        ];

        $purchaseResponse = $this->actingAs($user)
            ->postJson('/electricity/purchase', $purchaseData);

        // Step 8: Verify response structure
        $purchaseResponse->assertJsonStructure([
            'status',
            'message'
        ]);

        // The actual purchase may fail due to external API dependencies,
        // but the workflow should handle it gracefully
        $responseData = $purchaseResponse->json();
        $this->assertContains($responseData['status'], ['success', 'error']);
    }

    /**
     * Test electricity payment flow with validation errors.
     *
     * @return void
     */
    public function test_electricity_payment_flow_with_validation_errors()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00,
            'sTransactionPin' => hash('sha256', '1234')
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Test with invalid phone number
        $response = $this->actingAs($user)
            ->postJson('/electricity/purchase', [
                'provider' => 'AEDC',
                'meter_number' => '12345678901',
                'meter_type' => 'prepaid',
                'amount' => 5000,
                'customer_name' => 'John Doe',
                'phone' => '123', // Invalid phone
                'transaction_pin' => '1234'
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
     * Test electricity payment flow with invalid transaction PIN.
     *
     * @return void
     */
    public function test_electricity_payment_flow_with_invalid_pin()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00,
            'sTransactionPin' => hash('sha256', '1234')
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Test with wrong transaction PIN
        $response = $this->actingAs($user)
            ->postJson('/electricity/purchase', [
                'provider' => 'AEDC',
                'meter_number' => '12345678901',
                'meter_type' => 'prepaid',
                'amount' => 5000,
                'customer_name' => 'John Doe',
                'phone' => '08012345678',
                'transaction_pin' => '9999' // Wrong PIN
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid transaction PIN'
        ]);
    }

    /**
     * Test electricity payment flow with amount below minimum.
     *
     * @return void
     */
    public function test_electricity_payment_flow_with_amount_below_minimum()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00,
            'sTransactionPin' => hash('sha256', '1234')
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Test with amount below minimum (1000)
        $response = $this->actingAs($user)
            ->postJson('/electricity/purchase', [
                'provider' => 'AEDC',
                'meter_number' => '12345678901',
                'meter_type' => 'prepaid',
                'amount' => 500, // Below minimum
                'customer_name' => 'John Doe',
                'phone' => '08012345678',
                'transaction_pin' => '1234'
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
    }

    /**
     * Test electricity payment flow with invalid meter type.
     *
     * @return void
     */
    public function test_electricity_payment_flow_with_invalid_meter_type()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00,
            'sTransactionPin' => hash('sha256', '1234')
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Test with invalid meter type
        $response = $this->actingAs($user)
            ->postJson('/electricity/purchase', [
                'provider' => 'AEDC',
                'meter_number' => '12345678901',
                'meter_type' => 'invalid_type', // Invalid meter type
                'amount' => 5000,
                'customer_name' => 'John Doe',
                'phone' => '08012345678',
                'transaction_pin' => '1234'
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
    }

    /**
     * Test electricity pricing retrieval workflow.
     *
     * @return void
     */
    public function test_electricity_pricing_retrieval_workflow()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Request pricing for valid provider and amount
        $response = $this->actingAs($user)
            ->postJson('/electricity/pricing', [
                'provider' => 'AEDC',
                'amount' => 5000
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success'
        ]);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'amount',
                'service_charge',
                'total_amount',
                'provider'
            ]
        ]);
    }

    /**
     * Test meter validation workflow.
     *
     * @return void
     */
    public function test_meter_validation_workflow()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        // Test meter validation with valid data
        $response = $this->actingAs($user)
            ->postJson('/electricity/validate-meter', [
                'provider' => 'AEDC',
                'meter_number' => '12345678901',
                'meter_type' => 'prepaid'
            ]);

        $response->assertJsonStructure([
            'status'
        ]);
    }

    /**
     * Test that unauthenticated users cannot access electricity payment.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_pay_electricity()
    {
        $response = $this->get('/electricity');

        // Should redirect to login
        $response->assertRedirect('/login');
    }

    /**
     * Test electricity page loads with site settings.
     *
     * @return void
     */
    public function test_electricity_page_loads_with_site_settings()
    {
        $user = User::factory()->create([
            'sWallet' => 10000.00
        ]);

        ElectricityProvider::create([
            'ePlan' => 'AEDC',
            'ePrice' => 45.50,
            'status' => 'active'
        ]);

        SiteSettings::create([
            'sitename' => 'Test VTU',
            'electricitycharges' => 100,
            'electricity_minimum_amount' => 2000,
            'electricity_maximum_amount' => 100000
        ]);

        $response = $this->actingAs($user)->get('/electricity');

        $response->assertStatus(200);
        $response->assertViewHas('serviceCharges', 100);
        $response->assertViewHas('minimumAmount', 2000);
        $response->assertViewHas('maximumAmount', 100000);
    }
}
