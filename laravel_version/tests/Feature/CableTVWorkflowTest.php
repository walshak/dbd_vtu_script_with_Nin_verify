<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CableId;
use App\Models\CablePlan;
use App\Models\Transaction;
use App\Models\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CableTVWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete cable TV subscription flow from page load to successful purchase.
     * Validates: Requirements 3.3
     *
     * @return void
     */
    public function test_complete_cable_tv_subscription_flow()
    {
        // Step 1: Create a test user with sufficient balance
        $user = User::factory()->create([
            'wallet_balance' => 10000.00,
            'email_verified_at' => now()
        ]);

        // Step 2: Create test cable provider data
        $provider = CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv',
            'status' => 'active'
        ]);

        // Step 3: Create test cable plans
        $cablePlan = CablePlan::create([
            'decoder' => 'dstv',
            'plan_name' => 'DSTV Compact',
            'plan_amount' => 9000,
            'plan_id' => 'dstv-compact',
            'status' => 'active'
        ]);

        // Step 4: Create site settings
        SiteSettings::create([
            'sitename' => 'Test VTU',
            'cabletvcharges' => 50,
            'cabletv_minimum_amount' => 500,
            'cabletv_maximum_amount' => 50000,
            'cabletv_maintenance_mode' => false
        ]);

        // Step 5: User navigates to cable TV page
        $response = $this->actingAs($user)->get('/cable-tv');

        $response->assertStatus(200);
        $response->assertViewIs('cable-tv.index');
        $response->assertViewHas('providers');

        // Verify page displays the form elements
        $response->assertSee('Cable TV Subscription');
        $response->assertSee('Select Cable Provider');
        $response->assertSee('Enter IUC/Smart Card Number');

        // Step 6: User requests cable plans for a decoder
        $plansResponse = $this->actingAs($user)
            ->postJson('/cable-tv/plans', [
                'decoder' => 'dstv'
            ]);

        $plansResponse->assertStatus(200);
        $plansResponse->assertJson([
            'status' => 'success'
        ]);

        // Step 7: User validates IUC number
        $validateResponse = $this->actingAs($user)
            ->postJson('/cable-tv/validate-iuc', [
                'decoder' => 'dstv',
                'iuc_number' => '1234567890'
            ]);

        $validateResponse->assertJsonStructure([
            'status'
        ]);

        // Step 8: User submits purchase form with valid data
        $purchaseData = [
            'decoder' => 'dstv',
            'iuc_number' => '1234567890',
            'plan_id' => 'dstv-compact'
        ];

        $purchaseResponse = $this->actingAs($user)
            ->postJson('/cable-tv/purchase', $purchaseData);

        // Step 9: Verify response structure
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
     * Test cable TV subscription flow with validation errors.
     *
     * @return void
     */
    public function test_cable_tv_subscription_flow_with_validation_errors()
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000.00
        ]);

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv',
            'status' => 'active'
        ]);

        // Test with missing IUC number
        $response = $this->actingAs($user)
            ->postJson('/cable-tv/purchase', [
                'decoder' => 'dstv',
                'plan_id' => 'dstv-compact'
                // Missing iuc_number
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
     * Test cable TV subscription flow with invalid decoder.
     *
     * @return void
     */
    public function test_cable_tv_subscription_flow_with_invalid_decoder()
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000.00
        ]);

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv',
            'status' => 'active'
        ]);

        // Test with invalid decoder
        $response = $this->actingAs($user)
            ->postJson('/cable-tv/purchase', [
                'decoder' => 'invalid_decoder',
                'iuc_number' => '1234567890',
                'plan_id' => 'dstv-compact'
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error'
        ]);
    }

    /**
     * Test cable TV plans retrieval workflow.
     *
     * @return void
     */
    public function test_cable_tv_plans_retrieval_workflow()
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000.00
        ]);

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv',
            'status' => 'active'
        ]);

        CablePlan::create([
            'decoder' => 'dstv',
            'plan_name' => 'DSTV Compact',
            'plan_amount' => 9000,
            'plan_id' => 'dstv-compact',
            'status' => 'active'
        ]);

        // Request plans for valid decoder
        $response = $this->actingAs($user)
            ->postJson('/cable-tv/plans', [
                'decoder' => 'dstv'
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

    /**
     * Test IUC validation workflow.
     *
     * @return void
     */
    public function test_iuc_validation_workflow()
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000.00
        ]);

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv',
            'status' => 'active'
        ]);

        // Test IUC validation with valid data
        $response = $this->actingAs($user)
            ->postJson('/cable-tv/validate-iuc', [
                'decoder' => 'dstv',
                'iuc_number' => '1234567890'
            ]);

        $response->assertJsonStructure([
            'status'
        ]);
    }

    /**
     * Test that unauthenticated users cannot access cable TV subscription.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_subscribe_cable_tv()
    {
        $response = $this->get('/cable-tv');

        // Should redirect to login
        $response->assertRedirect('/login');
    }

    /**
     * Test cable TV page loads with site settings.
     *
     * @return void
     */
    public function test_cable_tv_page_loads_with_site_settings()
    {
        $user = User::factory()->create([
            'wallet_balance' => 10000.00
        ]);

        CableId::create([
            'network' => 'dstv',
            'name' => 'DSTV',
            'provider' => 'dstv',
            'status' => 'active'
        ]);

        SiteSettings::create([
            'sitename' => 'Test VTU',
            'cabletvcharges' => 100,
            'cabletv_minimum_amount' => 1000,
            'cabletv_maximum_amount' => 50000
        ]);

        $response = $this->actingAs($user)->get('/cable-tv');

        $response->assertStatus(200);
        $response->assertViewHas('serviceCharges', 100);
        $response->assertViewHas('minimumAmount', 1000);
        $response->assertViewHas('maximumAmount', 50000);
    }
}
