<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponsiveDesignTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'sWallet' => 5000.00,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test that all service views have responsive grid layouts
     */
    public function test_all_views_have_responsive_grid_classes()
    {
        $views = [
            '/buy-airtime' => 'airtime.index',
            '/buy-data' => 'data.index',
            '/cable-tv' => 'cable-tv.index',
            '/electricity' => 'electricity.index',
        ];

        foreach ($views as $route => $viewName) {
            $response = $this->actingAs($this->user)->get($route);

            $response->assertStatus(200);
            $response->assertViewIs($viewName);

            // Check for responsive grid classes
            $content = $response->getContent();

            // Verify main container has responsive grid
            $this->assertStringContainsString('grid-cols-1', $content,
                "View {$viewName} should have mobile-first grid-cols-1");

            // Verify responsive breakpoints exist
            $this->assertTrue(
                str_contains($content, 'lg:grid-cols-') ||
                str_contains($content, 'md:grid-cols-') ||
                str_contains($content, 'sm:grid-cols-'),
                "View {$viewName} should have responsive grid breakpoints"
            );
        }
    }

    /**
     * Test airtime view responsive design
     */
    public function test_airtime_view_has_proper_responsive_classes()
    {
        $response = $this->actingAs($this->user)->get('/buy-airtime');
        $content = $response->getContent();

        // Check network selection grid
        $this->assertStringContainsString('grid-cols-2', $content);
        $this->assertStringContainsString('md:grid-cols-4', $content);

        // Check main layout grid
        $this->assertStringContainsString('lg:grid-cols-3', $content);

        // Check amount buttons grid
        $this->assertStringContainsString('grid-cols-3', $content);
        $this->assertStringContainsString('md:grid-cols-6', $content);

        // Check responsive padding
        $this->assertStringContainsString('px-', $content);
        $this->assertStringContainsString('py-', $content);
    }

    /**
     * Test data view responsive design
     */
    public function test_data_view_has_proper_responsive_classes()
    {
        $response = $this->actingAs($this->user)->get('/buy-data');
        $content = $response->getContent();

        // Check network selection grid
        $this->assertStringContainsString('grid-cols-2', $content);
        $this->assertStringContainsString('lg:grid-cols-4', $content);

        // Check data type selection grid
        $this->assertStringContainsString('md:grid-cols-3', $content);

        // Check main layout grid
        $this->assertStringContainsString('lg:grid-cols-3', $content);

        // Check phone number section grid
        $this->assertStringContainsString('lg:grid-cols-3', $content);
    }

    /**
     * Test cable TV view responsive design
     */
    public function test_cable_tv_view_has_proper_responsive_classes()
    {
        $response = $this->actingAs($this->user)->get('/cable-tv');
        $content = $response->getContent();

        // Check provider selection grid
        $this->assertStringContainsString('grid-cols-2', $content);
        $this->assertStringContainsString('lg:grid-cols-4', $content);

        // Check main layout grid
        $this->assertStringContainsString('lg:grid-cols-3', $content);

        // Check package details grid
        $this->assertStringContainsString('md:grid-cols-3', $content);
    }

    /**
     * Test electricity view responsive design
     */
    public function test_electricity_view_has_proper_responsive_classes()
    {
        $response = $this->actingAs($this->user)->get('/electricity');
        $content = $response->getContent();

        // Check DISCO selection grid
        $this->assertStringContainsString('grid-cols-2', $content);
        $this->assertStringContainsString('lg:grid-cols-3', $content);
        $this->assertStringContainsString('xl:grid-cols-4', $content);

        // Check meter type selection grid
        $this->assertStringContainsString('md:grid-cols-2', $content);

        // Check main layout grid
        $this->assertStringContainsString('lg:grid-cols-3', $content);

        // Check transaction summary grid
        $this->assertStringContainsString('md:grid-cols-4', $content);
    }

    /**
     * Test that header sections are responsive
     */
    public function test_header_sections_are_responsive()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for responsive flex layouts in headers
            $this->assertTrue(
                str_contains($content, 'flex-col') && str_contains($content, 'lg:flex-row'),
                "Route {$route} header should have responsive flex layout"
            );
        }
    }

    /**
     * Test that forms have responsive layouts
     */
    public function test_forms_have_responsive_layouts()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for responsive form layouts
            $this->assertStringContainsString('w-full', $content,
                "Route {$route} should have full-width form elements");
        }
    }

    /**
     * Test that buttons are responsive
     */
    public function test_buttons_are_responsive()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for full-width buttons on mobile
            $this->assertStringContainsString('w-full', $content);
        }
    }

    /**
     * Test that spacing is responsive
     */
    public function test_spacing_is_responsive()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for responsive padding
            $this->assertTrue(
                str_contains($content, 'px-4') || str_contains($content, 'px-6'),
                "Route {$route} should have responsive horizontal padding"
            );

            // Check for responsive vertical spacing
            $this->assertTrue(
                str_contains($content, 'py-4') || str_contains($content, 'py-8'),
                "Route {$route} should have responsive vertical padding"
            );
        }
    }

    /**
     * Test that text sizes are responsive
     */
    public function test_text_sizes_are_responsive()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for responsive text sizes
            $this->assertTrue(
                str_contains($content, 'text-3xl') && str_contains($content, 'lg:text-4xl'),
                "Route {$route} should have responsive heading sizes"
            );
        }
    }

    /**
     * Test that images and icons are responsive
     */
    public function test_images_are_responsive()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for responsive image sizing
            $this->assertTrue(
                str_contains($content, 'h-12') || str_contains($content, 'w-auto'),
                "Route {$route} should have responsive image sizing"
            );
        }
    }

    /**
     * Test container max-width is properly set
     */
    public function test_containers_have_proper_max_width()
    {
        $views = ['/buy-airtime', '/buy-data', '/cable-tv', '/electricity'];

        foreach ($views as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $content = $response->getContent();

            // Check for container classes
            $this->assertStringContainsString('container', $content);
            $this->assertStringContainsString('mx-auto', $content);
        }
    }
}
