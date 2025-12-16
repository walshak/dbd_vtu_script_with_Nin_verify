# Design Document

## Overview

This design addresses the systematic cleanup and standardization of frontend user views in the VTU application. The primary focus is fixing Blade template errors (specifically the "Cannot end a section without first starting one" error), ensuring consistent layout structure, removing duplicate code, and improving overall maintainability.

The design follows a template-first approach, establishing a clear hierarchy of layouts and ensuring all service views properly extend the base layout with correctly matched section directives.

## Architecture

### Layout Hierarchy

```
layouts/app.blade.php (Root)
    └── layouts/user-layout.blade.php (User Dashboard Layout)
            ├── airtime/index.blade.php
            ├── data/index.blade.php
            ├── cable-tv/index.blade.php
            └── electricity/index.blade.php
```

### Section Structure

The user-layout expects child views to define content in the `page-content` section:

```blade
@extends('layouts.user-layout')

@section('page-content')
    <!-- Service-specific content here -->
@endsection
```

### Component Architecture

- **Shared Components**: Sidebar, topbar, modals
- **Service Views**: Individual purchase pages for each service
- **Reusable Partials**: Common UI elements (progress indicators, transaction summaries)

## Components and Interfaces

### 1. Base Layout (layouts/user-layout.blade.php)

**Purpose**: Provides the main dashboard structure with sidebar and topbar

**Sections Provided**:
- `page-content`: Main content area for service views
- `styles`: Additional CSS via @push directive
- `scripts`: Additional JavaScript via @push directive

**Variables Expected**:
- `$title`: Page title (optional, defaults to 'Page')

### 2. Service View Template Structure

Each service view (airtime, data, cable TV, electricity) should follow this structure:

```blade
@extends('layouts.user-layout')

@php
    $title = 'Service Name';
@endphp

@push('styles')
<!-- Service-specific styles -->
@endpush

@section('page-content')
<!-- Service content -->
@endsection

@push('scripts')
<!-- Service-specific scripts -->
@endpush
```

### 3. Common UI Components

#### Progress Indicator Component
- Visual step indicator (1-4 steps)
- Consistent across all services
- Shows current step and completion status

#### Transaction Summary Component
- Displays purchase details
- Shows pricing breakdown
- Consistent styling across services

#### Modal Components
- Loading modal
- Success modal
- Error modal

## Data Models

### View Data Structure

Each service view receives the following data from its controller:

```php
[
    'networks' => Collection,      // For airtime/data
    'providers' => Collection,     // For cable/electricity
    'plans' => Collection,         // Service-specific plans
    'serviceCharges' => float,     // Service fees
    'minimumAmount' => float,      // Min transaction amount
    'maximumAmount' => float,      // Max transaction amount
    'maintenanceMode' => bool,     // Service availability
    'maintenanceMessage' => string // Maintenance notice
]
```

### Form Data Structure

Each service form submits data in a consistent format:

```javascript
{
    network/provider: string,
    phone/iuc_number/meter_number: string,
    amount: number,
    plan_id: string (optional),
    meter_type: string (for electricity),
    ported_number: boolean (for data),
    _token: string
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Section Matching Integrity

*For any* Blade template that uses @section directive, the template must include a corresponding @endsection directive at the same nesting level
**Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5**

### Property 2: Layout Extension Consistency

*For any* service view file, it must extend 'layouts.user-layout' and define content within the 'page-content' section
**Validates: Requirements 2.1, 2.2**

### Property 3: Push Directive Consistency

*For any* service view that adds styles or scripts, it must use @push('styles') and @push('scripts') directives respectively
**Validates: Requirements 2.3, 2.4**

### Property 4: Form Structure Completeness

*For any* service form, all input fields must have corresponding labels, proper name attributes, and validation error display elements
**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 6.2**

### Property 5: HTML Nesting Validity

*For any* HTML element opened in a template, it must be properly closed before its parent element closes
**Validates: Requirements 6.1**

### Property 6: Responsive Grid Consistency

*For any* grid layout using Tailwind CSS classes, it must include responsive breakpoints (sm:, md:, lg:, xl:) for different screen sizes
**Validates: Requirements 8.1, 8.2, 8.3, 8.4**

## Error Handling

### Template Rendering Errors

**Error**: InvalidArgumentException - "Cannot end a section without first starting one"

**Cause**: Mismatched or missing @section/@endsection directives

**Solution**:
1. Scan template for all @section directives
2. Ensure each has a matching @endsection
3. Verify proper nesting (no overlapping sections)
4. Check for duplicate section definitions

### Missing Layout Sections

**Error**: Undefined variable or missing content

**Cause**: Child view doesn't define required sections

**Solution**:
1. Ensure @extends directive is first line (after @php blocks)
2. Define all required sections from parent layout
3. Use @yield with defaults in parent layouts

### JavaScript Errors

**Error**: $ is not defined, or function not found

**Cause**: Scripts loading in wrong order or missing dependencies

**Solution**:
1. Ensure jQuery is loaded before custom scripts
2. Use @push('scripts') to add scripts after jQuery
3. Wrap code in $(document).ready()

## Testing Strategy

### Manual Testing Checklist

For each service view (airtime, data, cable TV, electricity):

1. **Template Rendering**
   - Navigate to the page
   - Verify no Blade errors appear
   - Check browser console for JavaScript errors
   - Verify all sections render correctly

2. **Responsive Design**
   - Test on mobile viewport (320px-767px)
   - Test on tablet viewport (768px-1023px)
   - Test on desktop viewport (1024px+)
   - Verify all elements are accessible and properly sized

3. **Form Functionality**
   - Fill out form with valid data
   - Submit and verify AJAX handling
   - Test validation with invalid data
   - Verify error messages display correctly

4. **Interactive Elements**
   - Test all buttons and links
   - Verify modals open and close
   - Test dropdown selections
   - Verify progress indicators update

### Automated Testing

#### Unit Tests

Test individual Blade components:

```php
public function test_airtime_view_renders_without_errors()
{
    $response = $this->actingAs($user)->get('/buy-airtime');
    $response->assertStatus(200);
    $response->assertViewIs('airtime.index');
    $response->assertViewHas('networks');
}
```

#### Browser Tests (Laravel Dusk)

Test full user workflows:

```php
public function test_user_can_purchase_airtime()
{
    $this->browse(function (Browser $browser) {
        $browser->loginAs($user)
                ->visit('/buy-airtime')
                ->select('network', 'mtn')
                ->type('phone', '08012345678')
                ->type('amount', '100')
                ->press('Purchase Airtime')
                ->waitForText('Purchase Successful');
    });
}
```

### Validation Tests

#### Template Syntax Validation

Create a test that parses all Blade templates and validates:
- All @section directives have matching @endsection
- All @if directives have matching @endif
- All @foreach directives have matching @endforeach
- No orphaned @endsection directives

#### HTML Validation

Use an HTML validator to check:
- Proper tag nesting
- Closed tags
- Valid attributes
- Accessibility attributes (alt, aria-*)

### Regression Testing

After fixes are applied:
1. Test all service pages load without errors
2. Verify existing functionality still works
3. Check that no new console errors appear
4. Validate responsive behavior on all devices

## Implementation Notes

### Fixing the Airtime View Error

The current airtime/index.blade.php has duplicate and conflicting sections. The fix involves:

1. Remove duplicate form sections
2. Ensure single @section('page-content') wraps all content
3. Remove any orphaned @endsection directives
4. Consolidate duplicate JavaScript code

### Standardization Approach

1. **Choose a Reference Template**: Use the best-structured view as a template
2. **Extract Common Patterns**: Identify reusable components
3. **Apply Consistently**: Update all views to follow the same pattern
4. **Test Incrementally**: Fix and test one view at a time

### Code Organization

```
resources/views/
├── layouts/
│   ├── app.blade.php
│   └── user-layout.blade.php
├── components/
│   ├── user-sidebar.blade.php
│   ├── user-topbar.blade.php
│   └── modals/
│       ├── loading.blade.php
│       ├── success.blade.php
│       └── error.blade.php
├── airtime/
│   └── index.blade.php
├── data/
│   └── index.blade.php
├── cable-tv/
│   └── index.blade.php
└── electricity/
    └── index.blade.php
```

### Performance Considerations

- Minimize inline styles and scripts
- Use asset bundling for common JavaScript
- Lazy load images where appropriate
- Cache compiled Blade templates (automatic in production)

### Accessibility Guidelines

- Use semantic HTML elements
- Include ARIA labels for screen readers
- Ensure keyboard navigation works
- Maintain sufficient color contrast
- Provide text alternatives for icons
