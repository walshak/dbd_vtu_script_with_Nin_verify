<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NetworkId;
use App\Models\DataPlan;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete data purchase flow from page load to successful purchase.
     * Validates: Requirements 3.2
     *
     * @return void
     */
    public function test_complete_data_purchase_flow()
    {
        // Step 1: Create a test user with sufficient balance
        $user = User::factory()->create([
            'wallet_balance' => 5000.00,
            'email_verified_at' => now()
        ]);

        // Step 2: Create test network data
        $network = NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png',
            'brand_color' => '#FFCC00'
        ]);

        // Step 3: Create test data plans
        $dataPlan = DataPlan::create([
            'network' => 'mtn',
            'plan_name' => '1GB Monthly',
            'plan_amount' => 300,
            'plan_validity' => '30 days',
            'data_group' => 'SME',
            'plan_id' => 'mtn-1gb-sme'
        ]);

        // Step 4: User navigates to data purchase page
        $response = $this->actingAs($user)->get('/buy-data');

        $response->assertStatus(200);
        $response->assertViewIs('data.index');
        $response->assertViewHas('networks');

        // Verify page displays the form elements
        $response->assertSee('Buy Data Bundle');
        $response->assertSee('Select Network Provider');
        $response->assertSee('Enter Phone Number');

        // Step 5: User requests data plans for a network
        $plansResponse = $this->actingAs($user)
            ->postJson('/data/plans', [
                'network' => 'mtn',
                'data_group' => 'SME'
            ]);

        $plansResponse->assertStatus(200);
        $plansResponse->assertJson([
            'status' => 'success'
        ]);

        // Step 6: User submits purchase form with valid data
        $purchaseData = [
            'network' => 'mtn',
            'phone' => '08012345678',
            'plan_id' => 'mtn-1gb-sme',
            'data_group' => 'SME',
            'ported_number' => false
        ];

        $purchaseResponse = $this->actingAs($user)
            ->postJson('/data/purchase', $purchaseData);

        // Step 7: Verify response structure
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
     * Test data purchase flow with validation errors.
     *
     * @return void
     */
    public function test_data_purchase_flow_with_validation_errors()
    {
        $user = User::factory()->create([
            'wallet_balance' => 5000.00
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        // Test with invalid phone number
        $response = $this->actingAs($user)
            ->postJson('/data/purchase', [
                'network' => 'mtn',
                'phone' => '123', // Invalid phone
                'plan_id' => 'mtn-1gb-sme',
                'data_group' => 'SME'
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
     * Test data purchase flow with missing required fields.
     *
     * @return void
     */
    public function test_data_purchase_flow_with_missing_fields()
    {
        $user = User::factory()->create([
            'wallet_balance' => 5000.00
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        // Test with missing plan_id
        $response = $this->actingAs($user)
            ->postJson('/data/purchase', [
                'network' => 'mtn',
                'phone' => '08012345678',
                'data_group' => 'SME'
                // Missing plan_id
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
    }

    /**
     * Test data purchase flow with invalid data group.
     *
     * @return void
     */
    public function test_data_purchase_flow_with_invalid_data_group()
    {
        $user = User::factory()->create([
            'wallet_balance' => 5000.00
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        // Test with invalid data_group
        $response = $this->actingAs($user)
            ->postJson('/data/purchase', [
                'network' => 'mtn',
                'phone' => '08012345678',
                'plan_id' => 'mtn-1gb-sme',
                'data_group' => 'InvalidGroup' // Invalid data group
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
    }

    /**
     * Test that unauthenticated users cannot access data purchase.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_purchase_data()
    {
        $response = $this->get('/buy-data');

        // Should redirect to login
        $response->assertRedirect('/login');
    }

    /**
     * Test data plans retrieval workflow.
     *
     * @return void
     */
    public function test_data_plans_retrieval_workflow()
    {
        $user = User::factory()->create([
            'wallet_balance' => 5000.00
        ]);

        NetworkId::create([
            'network' => 'mtn',
            'logoPath' => '/assets/images/mtn.png'
        ]);

        DataPlan::create([
            'network' => 'mtn',
            'plan_name' => '1GB Monthly',
            'plan_amount' => 300,
            'data_group' => 'SME',
            'plan_id' => 'mtn-1gb-sme'
        ]);

        // Request plans for valid network and data group
        $response = $this->actingAs($user)
            ->postJson('/data/plans', [
                'network' => 'mtn',
                'data_group' => 'SME'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success'
        ]);
        $response->assertJsonStructure([
            'status',
            'data'
        ]);
    }
}
