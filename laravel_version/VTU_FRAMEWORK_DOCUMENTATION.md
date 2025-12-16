# VTU Framework Documentation

## Overview

The VTU Framework is a comprehensive JavaScript and CSS framework designed specifically for VTU (Virtual Top-Up) service applications. It provides consistent UI components, utilities, and functionality across all service pages including Airtime, Data, Cable TV, Electricity, and Exam Pin services.

## Framework Structure

```
public/
├── js/
│   ├── vtu-framework.js      # Core framework with base utilities
│   └── vtu-services.js       # Service-specific extensions
├── css/
│   └── vtu-framework.css     # Unified styling system
resources/views/layouts/
└── vtu-framework.blade.php   # Laravel integration template
```

## Installation & Setup

### 1. Include Framework in Layout

Add to your main layout file (e.g., `app.blade.php`):

```blade
@include('layouts.vtu-framework')
```

### 2. Service Page Integration

For each service page, initialize the specific service:

```javascript
// For Airtime Service
VTU.initializeAirtimeService(providers);

// For Data Service
VTU.initializeDataService(providers);

// For Cable TV Service
VTU.initializeCableService(providers);

// For Electricity Service
VTU.initializeElectricityService(providers);

// For Exam Pin Service
VTU.initializeExamService(providers);
```

## Core Components

### 1. Progress Indicator

Creates a visual progress indicator for multi-step forms.

```javascript
// Define steps
const steps = [
    { key: 'provider', icon: 'fa-building', label: 'Select Provider' },
    { key: 'amount', icon: 'fa-money-bill', label: 'Enter Amount' },
    { key: 'phone', icon: 'fa-phone', label: 'Phone Number' },
    { key: 'confirm', icon: 'fa-check', label: 'Confirm' }
];

// Create progress indicator
VTU.createProgressIndicator(steps, 'progressContainer');

// Update progress
VTU.updateProgressStep('provider', true); // Mark as completed
```

### 2. Provider Selection Cards

Creates consistent provider selection interfaces.

```javascript
// Generate provider card HTML
const cardHTML = VTU.createProviderCard(provider, 'network', false);

// Setup provider selection with callback
VTU.setupProviderSelection('#providersContainer', function(selectedProvider, card) {
    console.log('Selected:', selectedProvider);
    // Handle selection change
});
```

### 3. Form Validation

Comprehensive form validation system.

```javascript
// Add validators
VTU.addValidator('phone', (value) => {
    return VTU.validatePhoneNumber(value);
}, 'Please enter a valid 11-digit phone number');

VTU.addValidator('amount', (value) => {
    const amount = parseFloat(value);
    return amount >= 50 && amount <= 10000;
}, 'Amount must be between ₦50 and ₦10,000');

// Validate specific field
const isValid = VTU.validateField('phone');

// Validate entire form
const formIsValid = VTU.validateForm(['phone', 'amount', 'transaction_pin']);
```

### 4. Loading States

Manage loading states for better UX.

```javascript
// Show loading
VTU.showLoading('submitBtn', 'Processing payment...');

// Hide loading
VTU.hideLoading('submitBtn');
```

### 5. Modal System

Create and manage modals for notifications.

```javascript
// Success modal
VTU.showSuccessModal('Purchase Successful!', {
    reference: 'TXN123456',
    amount: '1000',
    balance: '5000'
});

// Error modal
VTU.showErrorModal('Transaction failed. Please try again.');

// Custom modal
const modal = VTU.createModal('customModal', 'Title', '<p>Content</p>');
VTU.showModal('customModal');
```

### 6. Wallet Balance Management

Track and display wallet balance.

```javascript
// Update balance display
VTU.updateWalletBalance(5000.00);

// Check sufficient balance
const hasSufficientBalance = VTU.checkSufficientBalance(1000, 'balanceStatus');
```

## Service-Specific Features

### Airtime Service

```javascript
// Initialize airtime service
VTU.initializeAirtimeService(providers);

// Setup quick amount buttons
VTU.setupAirtimeAmountButtons('#amountButtonsContainer');
```

### Data Service

```javascript
// Initialize data service
VTU.initializeDataService(providers);

// Load data plans for selected network
await VTU.loadDataPlans('MTN', 'dataPlansContainer');
```

### Cable TV Service

```javascript
// Initialize cable service
VTU.initializeCableService(providers);

// Verify IUC number
const customerData = await VTU.verifyIUCNumber('1234567890', 'DSTV', 'verificationStatus');

// Load cable packages
await VTU.loadCablePackages('DSTV', 'packagesContainer');
```

### Electricity Service

```javascript
// Initialize electricity service
VTU.initializeElectricityService(providers);

// Verify meter number
const meterData = await VTU.verifyMeterNumber('12345678', 'IKEDC', 'verificationStatus');

// Setup amount buttons
VTU.setupElectricityAmountButtons('#amountButtonsContainer');
```

### Exam Pin Service

```javascript
// Initialize exam service
VTU.initializeExamService(providers);

// Setup quantity buttons
VTU.setupExamQuantityButtons('#quantityButtonsContainer');
```

## Utility Functions

### Phone Number Utilities

```javascript
// Format phone number (removes non-digits, limits to 11)
const formatted = VTU.formatPhoneNumber('080-123-45678');

// Validate phone number
const isValid = VTU.validatePhoneNumber('08012345678');
```

### Transaction PIN Utilities

```javascript
// Format transaction PIN
const formatted = VTU.formatTransactionPin('1234abc');

// Validate transaction PIN
const isValid = VTU.validateTransactionPin('1234');

// Setup PIN visibility toggle
VTU.setupPinToggle('transaction_pin', 'togglePinBtn');
```

### Currency Formatting

```javascript
// Format currency
const formatted = VTU.formatCurrency(1500.50); // Returns "₦1,500.50"
```

### API Request Handler

```javascript
// Make API request
try {
    const response = await VTU.makeRequest('/api/airtime/purchase', {
        provider: 'MTN',
        amount: 1000,
        phone: '08012345678',
        transaction_pin: '1234'
    });
    
    if (response.status === 'success') {
        VTU.showSuccessModal('Purchase Successful!', response.data);
    }
} catch (error) {
    VTU.showErrorModal(error.message);
}
```

## CSS Framework

### CSS Variables

The framework uses CSS custom properties for consistent theming:

```css
:root {
    --vtu-primary: #10b981;
    --vtu-secondary: #3b82f6;
    --vtu-success: #10b981;
    --vtu-error: #ef4444;
    --vtu-warning: #f59e0b;
    /* ... more variables */
}
```

### Component Classes

#### Buttons

```html
<button class="vtu-btn vtu-btn-primary">Primary Button</button>
<button class="vtu-btn vtu-btn-secondary">Secondary Button</button>
<button class="vtu-btn vtu-btn-success vtu-btn-lg">Large Success Button</button>
```

#### Cards

```html
<div class="vtu-card">
    <div class="vtu-card-header">
        <h3 class="vtu-card-title">Card Title</h3>
        <p class="vtu-card-subtitle">Subtitle</p>
    </div>
    <div class="vtu-card-body">
        Content goes here
    </div>
</div>
```

#### Inputs

```html
<div class="vtu-input-group">
    <input type="text" class="vtu-input" placeholder="Enter value">
    <div class="vtu-input-icon">
        <i class="fas fa-check"></i>
    </div>
</div>
```

#### Alerts

```html
<div class="vtu-alert vtu-alert-success">
    <i class="fas fa-check-circle"></i>
    <div>Success message here</div>
</div>
```

#### Provider Cards

```html
<div class="vtu-provider-card">
    <input type="radio" name="provider" value="MTN" class="hidden">
    <div class="selection-indicator">
        <i class="fas fa-check"></i>
    </div>
    <!-- Card content -->
</div>
```

## Best Practices

### 1. Initialization

Always initialize the appropriate service before using service-specific features:

```javascript
$(document).ready(function() {
    // Initialize service
    VTU.initializeAirtimeService(providers);
    
    // Setup components
    VTU.setupAirtimeAmountButtons('#amountButtons');
    VTU.createProgressIndicator(steps, 'progressContainer');
    
    // Add event listeners
    // ... your code
});
```

### 2. Error Handling

Always wrap API calls in try-catch blocks:

```javascript
try {
    const response = await VTU.makeRequest('/api/endpoint', data);
    // Handle success
} catch (error) {
    VTU.showErrorModal(error.message);
}
```

### 3. Form Validation

Add validators early and validate before submission:

```javascript
// Add validators during initialization
VTU.addValidator('phone', validator, message);

// Validate before submission
$('#form').submit(function(e) {
    e.preventDefault();
    
    if (!VTU.validateForm(['phone', 'amount', 'pin'])) {
        return; // Stop if validation fails
    }
    
    // Proceed with submission
});
```

### 4. Responsive Design

Use responsive utility classes and test on mobile devices:

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Content -->
</div>
```

## Integration Examples

### Complete Service Page Setup

```javascript
$(document).ready(function() {
    // 1. Initialize service
    VTU.initializeAirtimeService(@json($providers));
    
    // 2. Create progress indicator
    const steps = [
        { key: 'provider', icon: 'fa-building', label: 'Select Network' },
        { key: 'amount', icon: 'fa-money-bill', label: 'Enter Amount' },
        { key: 'phone', icon: 'fa-phone', label: 'Phone Number' },
        { key: 'confirm', icon: 'fa-check', label: 'Confirm' }
    ];
    VTU.createProgressIndicator(steps, 'progressContainer');
    
    // 3. Setup provider selection
    VTU.setupProviderSelection('#providersGrid', function(provider) {
        VTU.updateProgressStep('provider', true);
        updateSummary();
    });
    
    // 4. Setup amount buttons
    VTU.setupAirtimeAmountButtons('#amountButtons');
    
    // 5. Setup PIN toggle
    VTU.setupPinToggle('transaction_pin', 'togglePin');
    
    // 6. Setup form validation
    $('#phone').on('input', function() {
        const isValid = VTU.validateField('phone');
        if (isValid) VTU.updateProgressStep('phone', true);
    });
    
    // 7. Handle form submission
    $('#airtimeForm').submit(async function(e) {
        e.preventDefault();
        
        if (!VTU.validateForm(['phone', 'amount', 'transaction_pin'])) {
            return;
        }
        
        VTU.showLoading('submitBtn', 'Processing...');
        
        try {
            const response = await VTU.makeRequest($(this).attr('action'), 
                new FormData(this));
            
            VTU.showSuccessModal('Airtime Purchase Successful!', response.data);
            $(this)[0].reset();
        } catch (error) {
            VTU.showErrorModal(error.message);
        } finally {
            VTU.hideLoading('submitBtn');
        }
    });
});
```

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Dependencies

- jQuery 3.6+ (optional, for backward compatibility)
- Font Awesome 5+ (for icons)
- Modern browser with ES6+ support

## Troubleshooting

### Common Issues

1. **Framework not loading**: Check that CSS and JS files are properly included
2. **Validation not working**: Ensure validators are added before validation
3. **Modals not showing**: Check z-index conflicts with other CSS
4. **API requests failing**: Verify CSRF token is properly set

### Debug Mode

Enable debug logging:

```javascript
VTU.debug = true; // Enable debug mode
```

This will log framework operations to the browser console.

## Contributing

To extend the framework for new services or features:

1. Add service-specific methods to `VTUServiceFramework` class
2. Include appropriate CSS classes in `vtu-framework.css`
3. Update this documentation
4. Test across all supported browsers

## Changelog

### v1.0.0 (Current)
- Initial release with complete VTU service support
- Core framework with utilities and components
- Service-specific extensions for all VTU services
- Comprehensive CSS framework with responsive design
- Laravel integration templates