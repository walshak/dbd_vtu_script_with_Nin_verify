<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

/**
 * Validates Blade template syntax across all view files.
 *
 * This test ensures that all Blade directives are properly matched:
 * - @section/@endsection
 * - @if/@endif
 * - @foreach/@endforeach
 * - @while/@endwhile
 * - @for/@endfor
 * - @push/@endpush
 * - @php/@endphp
 *
 * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
 */
class BladeTemplateValidationTest extends TestCase
{
    /**
     * Get all Blade template files in the views directory.
     *
     * @return array
     */
    private function getAllBladeTemplates(): array
    {
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);

        return array_filter($files, function ($file) {
            return $file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php');
        });
    }

    /**
     * Parse a Blade template and extract all directives with their positions.
     *
     * @param string $content
     * @return array
     */
    private function parseDirectives(string $content): array
    {
        $directives = [];
        $lines = explode("\n", $content);

        foreach ($lines as $lineNumber => $line) {
            // Skip inline @section with second parameter (shorthand syntax that doesn't need @endsection)
            // Example: @section('title', 'Page Title')
            if (preg_match('/@section\s*\(\s*[\'"][^\'"]+[\'"]\s*,/', $line)) {
                continue; // This is a shorthand section that doesn't need closing
            }

            // Match opening directives (excluding @empty which is part of @forelse)
            if (preg_match_all('/@(section|if|foreach|while|for|push|php|unless|forelse|isset|switch|case|auth|guest|env|production|hasSection|sectionMissing|can|cannot|canany)\b/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $directives[] = [
                        'type' => 'open',
                        'directive' => $match[0],
                        'line' => $lineNumber + 1,
                        'content' => trim($line)
                    ];
                }
            }

            // Match closing directives
            if (preg_match_all('/@end(section|if|foreach|while|for|push|php|unless|forelse|isset|switch|auth|guest|env|production|hasSection|sectionMissing|can|cannot|canany)\b/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $directives[] = [
                        'type' => 'close',
                        'directive' => $match[0],
                        'line' => $lineNumber + 1,
                        'content' => trim($line)
                    ];
                }
            }

            // @empty is part of @forelse, not a standalone directive
            // @else, @elseif, @break, @continue, @default don't need closing
        }

        return $directives;
    }

    /**
     * Validate that directives are properly matched in a template.
     *
     * @param array $directives
     * @param string $filename
     * @return array Array of errors found
     */
    private function validateDirectiveMatching(array $directives, string $filename): array
    {
        $errors = [];
        $stack = [];

        foreach ($directives as $directive) {
            if ($directive['type'] === 'open') {
                $stack[] = $directive;
            } elseif ($directive['type'] === 'close') {
                if (empty($stack)) {
                    $errors[] = sprintf(
                        "Orphaned @end%s at line %d in %s: %s",
                        $directive['directive'],
                        $directive['line'],
                        $filename,
                        $directive['content']
                    );
                } else {
                    $lastOpen = array_pop($stack);

                    // Special case: @endforelse can close @forelse (even if @empty was in between)
                    // @empty is part of @forelse structure, not a separate directive
                    if ($directive['directive'] === 'forelse' && $lastOpen['directive'] === 'forelse') {
                        // Correct match
                        continue;
                    }

                    if ($lastOpen['directive'] !== $directive['directive']) {
                        $errors[] = sprintf(
                            "Mismatched directives in %s: @%s at line %d closed with @end%s at line %d",
                            $filename,
                            $lastOpen['directive'],
                            $lastOpen['line'],
                            $directive['directive'],
                            $directive['line']
                        );
                        // Put it back for further checking
                        $stack[] = $lastOpen;
                    }
                }
            }
        }

        // Check for unclosed directives
        foreach ($stack as $unclosed) {
            $errors[] = sprintf(
                "Unclosed @%s at line %d in %s: %s",
                $unclosed['directive'],
                $unclosed['line'],
                $filename,
                $unclosed['content']
            );
        }

        return $errors;
    }

    /**
     * Test that all Blade templates have properly matched @section/@endsection directives.
     *
     * @return void
     */
    public function test_all_templates_have_matched_section_directives()
    {
        $templates = $this->getAllBladeTemplates();
        $allErrors = [];

        foreach ($templates as $template) {
            $content = File::get($template->getPathname());
            $relativePath = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $template->getPathname());

            $directives = $this->parseDirectives($content);
            $errors = $this->validateDirectiveMatching($directives, $relativePath);

            if (!empty($errors)) {
                $allErrors[$relativePath] = $errors;
            }
        }

        if (!empty($allErrors)) {
            $errorMessage = "Blade template validation errors found:\n\n";
            foreach ($allErrors as $file => $errors) {
                $errorMessage .= "File: $file\n";
                foreach ($errors as $error) {
                    $errorMessage .= "  - $error\n";
                }
                $errorMessage .= "\n";
            }

            $this->fail($errorMessage);
        }

        $this->assertTrue(true, sprintf('All %d Blade templates have properly matched directives', count($templates)));
    }

    /**
     * Test that specific service views have no directive errors.
     *
     * @return void
     */
    public function test_service_views_have_no_directive_errors()
    {
        $serviceViews = [
            'airtime/index.blade.php',
            'data/index.blade.php',
            'cable-tv/index.blade.php',
            'electricity/index.blade.php',
        ];

        $allErrors = [];

        foreach ($serviceViews as $view) {
            $path = resource_path('views/' . $view);

            if (!File::exists($path)) {
                $allErrors[$view] = ["File does not exist"];
                continue;
            }

            $content = File::get($path);
            $directives = $this->parseDirectives($content);
            $errors = $this->validateDirectiveMatching($directives, $view);

            if (!empty($errors)) {
                $allErrors[$view] = $errors;
            }
        }

        $this->assertEmpty($allErrors,
            "Service views have directive errors:\n" .
            json_encode($allErrors, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that layout files have properly matched directives.
     *
     * @return void
     */
    public function test_layout_files_have_matched_directives()
    {
        $layouts = [
            'layouts/app.blade.php',
            'layouts/user-layout.blade.php',
            'layouts/admin.blade.php',
        ];

        $allErrors = [];

        foreach ($layouts as $layout) {
            $path = resource_path('views/' . $layout);

            if (!File::exists($path)) {
                continue; // Skip if layout doesn't exist
            }

            $content = File::get($path);
            $directives = $this->parseDirectives($content);
            $errors = $this->validateDirectiveMatching($directives, $layout);

            if (!empty($errors)) {
                $allErrors[$layout] = $errors;
            }
        }

        $this->assertEmpty($allErrors,
            "Layout files have directive errors:\n" .
            json_encode($allErrors, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that component files have properly matched directives.
     *
     * @return void
     */
    public function test_component_files_have_matched_directives()
    {
        $componentsPath = resource_path('views/components');

        if (!File::exists($componentsPath)) {
            $this->markTestSkipped('Components directory does not exist');
        }

        $components = File::allFiles($componentsPath);
        $allErrors = [];

        foreach ($components as $component) {
            if ($component->getExtension() !== 'php' || !str_ends_with($component->getFilename(), '.blade.php')) {
                continue;
            }

            $content = File::get($component->getPathname());
            $relativePath = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $component->getPathname());

            $directives = $this->parseDirectives($content);
            $errors = $this->validateDirectiveMatching($directives, $relativePath);

            if (!empty($errors)) {
                $allErrors[$relativePath] = $errors;
            }
        }

        $this->assertEmpty($allErrors,
            "Component files have directive errors:\n" .
            json_encode($allErrors, JSON_PRETTY_PRINT)
        );
    }
}
