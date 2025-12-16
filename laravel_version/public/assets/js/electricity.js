/**
 * User Electricity Bill Payment JavaScript
 * Handles all frontend interactions for electricity bill payments
 */

$(document).ready(function() {
    // Initialize the electricity payment system
    initializeElectricityPayment();
});

/**
 * Initialize electricity payment system
 */
function initializeElectricityPayment() {
    // Bind event handlers
    bindEventHandlers();
    
    // Initialize provider selection
    initializeProviderSelection();
    
    // Initialize form validation
    initializeFormValidation();
}

/**
 * Bind all event handlers
 */
function bindEventHandlers() {
    // Form submission
    $('#electricityForm').on('submit', handleFormSubmission);
    
    // Validate meter button
    $('#validateMeterBtn').on('click', handleMeterValidation);
    
    // Reset form button
    $('#resetFormBtn').on('click', handleFormReset);
    
    // Provider selection from sidebar
    $('.provider-card').on('click', handleProviderCardSelection);
    
    // Amount input for calculation
    $('#amount').on('input', calculateTransactionSummary);
    $('#provider').on('change', calculateTransactionSummary);
    
    // PIN modal handlers
    $('#confirmPurchaseBtn').on('click', handlePurchaseConfirmation);
    $('#transactionPin').on('input', handlePinInput);
    
    // Print receipt
    $('#printReceiptBtn').on('click', handlePrintReceipt);
    
    // Auto-validation when meter number changes
    $('#meterNumber').on('blur', function() {
        if ($(this).val().length >= 10 && $('#provider').val()) {
            setTimeout(() => {
                $('#validateMeterBtn').click();
            }, 500);
        }
    });
}

/**
 * Initialize provider selection functionality
 */
function initializeProviderSelection() {
    // Highlight provider cards when dropdown changes
    $('#provider').on('change', function() {
        const selectedProvider = $(this).val();
        
        $('.provider-card').removeClass('selected');
        if (selectedProvider) {
            $(`.provider-card[data-provider="${selectedProvider}"]`).addClass('selected');
        }
    });
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    // Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Custom validations
    $('#meterNumber').on('input', function() {
        const meterNumber = $(this).val().replace(/\D/g, ''); // Remove non-digits
        $(this).val(meterNumber);
        
        if (meterNumber.length < 10 || meterNumber.length > 12) {
            this.setCustomValidity('Meter number must be 10-12 digits');
        } else {
            this.setCustomValidity('');
        }
    });
    
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val());
        const min = parseFloat($(this).attr('min'));
        const max = parseFloat($(this).attr('max'));
        
        if (amount < min || amount > max) {
            this.setCustomValidity(`Amount must be between ₦${formatNumber(min)} and ₦${formatNumber(max)}`);
        } else {
            this.setCustomValidity('');
        }
    });
}

/**
 * Handle provider card selection
 */
function handleProviderCardSelection() {
    const provider = $(this).data('provider');
    const rate = $(this).data('rate');
    
    // Update dropdown
    $('#provider').val(provider).trigger('change');
    
    // Update visual selection
    $('.provider-card').removeClass('selected');
    $(this).addClass('selected');
    
    // Recalculate if amount is present
    if ($('#amount').val()) {
        calculateTransactionSummary();
    }
    
    showToast('info', `Selected ${provider.toUpperCase()} - ₦${formatNumber(rate)}/kWh`);
}

/**
 * Handle meter validation
 */
function handleMeterValidation() {
    const provider = $('#provider').val();
    const meterNumber = $('#meterNumber').val();
    const meterType = $('#meterType').val();
    
    if (!provider) {
        showToast('error', 'Please select an electricity provider');
        $('#provider').focus();
        return;
    }
    
    if (!meterNumber) {
        showToast('error', 'Please enter your meter number');
        $('#meterNumber').focus();
        return;
    }
    
    if (!meterType) {
        showToast('error', 'Please select your meter type');
        $('#meterType').focus();
        return;
    }
    
    const btn = $('#validateMeterBtn');
    const originalText = btn.html();
    
    // Show loading state
    btn.html('<i class="fas fa-spinner fa-spin"></i> Validating...').prop('disabled', true);
    
    $.ajax({
        url: '/electricity/validate-meter',
        method: 'POST',
        data: {
            provider: provider,
            meter_number: meterNumber,
            meter_type: meterType,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                // Display customer information
                $('#customerName').text(response.data.customer_name);
                $('#customerMeterType').text(response.data.meter_type.toUpperCase());
                $('#customerInfo').slideDown();
                
                // Enable purchase button
                $('#purchaseBtn').prop('disabled', false);
                
                showToast('success', 'Meter validated successfully');
                
                // Focus on amount if not filled
                if (!$('#amount').val()) {
                    $('#amount').focus();
                }
            } else {
                showToast('error', response.message);
                $('#customerInfo').slideUp();
                $('#purchaseBtn').prop('disabled', true);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showToast('error', response?.message || 'Meter validation failed');
            $('#customerInfo').slideUp();
            $('#purchaseBtn').prop('disabled', true);
        },
        complete: function() {
            btn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Calculate transaction summary
 */
function calculateTransactionSummary() {
    const amount = parseFloat($('#amount').val()) || 0;
    const provider = $('#provider').val();
    
    if (amount > 0 && provider) {
        const selectedOption = $(`#provider option[value="${provider}"]`);
        const rate = parseFloat(selectedOption.data('rate')) || 0;
        const charges = parseFloat(selectedOption.data('charges')) || 0;
        
        const total = amount + charges;
        const estimatedUnits = rate > 0 ? (amount / rate).toFixed(2) : 0;
        
        // Update summary
        $('#summaryAmount').text(`₦${formatNumber(amount)}`);
        $('#summaryCharges').text(`₦${formatNumber(charges)}`);
        $('#summaryTotal').text(`₦${formatNumber(total)}`);
        $('#summaryUnits').text(`${estimatedUnits} kWh`);
        
        // Show summary
        $('#transactionSummary').slideDown();
        
        // Check wallet balance
        const walletBalance = parseFloat($('.badge-success').text().replace(/[^0-9.]/g, ''));
        if (total > walletBalance) {
            showToast('warning', 'Insufficient wallet balance. Please fund your wallet.');
            $('#purchaseBtn').prop('disabled', true);
        } else if ($('#customerInfo').is(':visible')) {
            $('#purchaseBtn').prop('disabled', false);
        }
    } else {
        $('#transactionSummary').slideUp();
    }
}

/**
 * Handle form submission
 */
function handleFormSubmission(e) {
    e.preventDefault();
    
    // Validate form
    if (!this.checkValidity()) {
        e.stopPropagation();
        $(this).addClass('was-validated');
        return;
    }
    
    // Check if meter is validated
    if (!$('#customerInfo').is(':visible')) {
        showToast('error', 'Please validate your meter number first');
        return;
    }
    
    // Populate confirmation modal
    const provider = $('#provider').val();
    const meterNumber = $('#meterNumber').val();
    const amount = $('#amount').val();
    const total = $('#summaryTotal').text();
    
    $('#confirmProvider').text(provider.toUpperCase());
    $('#confirmMeterNumber').text(meterNumber);
    $('#confirmAmount').text(`₦${formatNumber(amount)}`);
    $('#confirmTotal').text(total);
    
    // Clear previous PIN
    $('#transactionPin').val('');
    
    // Show PIN modal
    $('#transactionPinModal').modal('show');
    setTimeout(() => {
        $('#transactionPin').focus();
    }, 500);
}

/**
 * Handle PIN input formatting
 */
function handlePinInput() {
    const pin = $(this).val();
    
    // Only allow digits
    $(this).val(pin.replace(/\D/g, ''));
    
    // Enable confirm button when PIN is 4 digits
    if (pin.length === 4) {
        $('#confirmPurchaseBtn').prop('disabled', false);
    } else {
        $('#confirmPurchaseBtn').prop('disabled', true);
    }
}

/**
 * Handle purchase confirmation
 */
function handlePurchaseConfirmation() {
    const pin = $('#transactionPin').val();
    
    if (pin.length !== 4) {
        showToast('error', 'Please enter a 4-digit transaction PIN');
        return;
    }
    
    const btn = $('#confirmPurchaseBtn');
    const originalText = btn.html();
    
    // Show loading state
    btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    
    // Prepare form data
    const formData = {
        provider: $('#provider').val(),
        meter_number: $('#meterNumber').val(),
        meter_type: $('#meterType').val(),
        amount: $('#amount').val(),
        transaction_pin: pin,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    $.ajax({
        url: '/electricity/purchase',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.status === 'success') {
                // Hide PIN modal
                $('#transactionPinModal').modal('hide');
                
                // Populate success modal
                $('#successReference').text(response.data.reference);
                $('#successProvider').text(response.data.provider);
                $('#successMeterNumber').text(response.data.meter_number);
                $('#successAmount').text(`₦${formatNumber(response.data.amount)}`);
                $('#successToken').text(response.data.token || 'Check SMS');
                $('#successUnits').text(response.data.units || 'N/A');
                
                // Show success modal
                $('#successModal').modal('show');
                
                // Update wallet balance
                updateWalletBalance(response.data.balance);
                
                // Reset form
                setTimeout(() => {
                    handleFormReset();
                }, 1000);
                
                showToast('success', 'Electricity token purchased successfully!');
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            
            if (response && response.errors) {
                // Handle validation errors
                Object.keys(response.errors).forEach(field => {
                    showToast('error', response.errors[field][0]);
                });
            } else {
                showToast('error', response?.message || 'Purchase failed. Please try again.');
            }
        },
        complete: function() {
            btn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Handle form reset
 */
function handleFormReset() {
    // Reset form fields
    $('#electricityForm')[0].reset();
    
    // Hide info sections
    $('#customerInfo').slideUp();
    $('#transactionSummary').slideUp();
    
    // Reset validation states
    $('#electricityForm').removeClass('was-validated');
    
    // Disable purchase button
    $('#purchaseBtn').prop('disabled', true);
    
    // Clear provider selection
    $('.provider-card').removeClass('selected');
    
    showToast('info', 'Form has been reset');
}

/**
 * Handle print receipt
 */
function handlePrintReceipt() {
    // Create printable receipt
    const receiptContent = `
        <div style="text-align: center; font-family: Arial, sans-serif; margin: 20px;">
            <h2>Electricity Token Receipt</h2>
            <hr>
            <div style="text-align: left; margin: 20px 0;">
                <p><strong>Reference:</strong> ${$('#successReference').text()}</p>
                <p><strong>Provider:</strong> ${$('#successProvider').text()}</p>
                <p><strong>Meter Number:</strong> ${$('#successMeterNumber').text()}</p>
                <p><strong>Amount:</strong> ${$('#successAmount').text()}</p>
                <p><strong>Token:</strong> ${$('#successToken').text()}</p>
                <p><strong>Units:</strong> ${$('#successUnits').text()}</p>
                <p><strong>Date:</strong> ${new Date().toLocaleString()}</p>
            </div>
            <hr>
            <p style="font-size: 12px;">Thank you for using our service!</p>
        </div>
    `;
    
    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(receiptContent);
    printWindow.document.close();
    printWindow.print();
}

/**
 * Update wallet balance display
 */
function updateWalletBalance(newBalance) {
    $('.badge-success').html(`<i class="fas fa-wallet"></i> ₦${formatNumber(newBalance)}`);
}

/**
 * Format number with commas and decimal places
 */
function formatNumber(num, decimals = 2) {
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

/**
 * Show toast notification
 */
function showToast(type, message) {
    // Create toast HTML
    const toastId = 'toast-' + Date.now();
    const iconClass = {
        'success': 'fa-check-circle text-success',
        'error': 'fa-exclamation-circle text-danger',
        'warning': 'fa-exclamation-triangle text-warning',
        'info': 'fa-info-circle text-info'
    }[type] || 'fa-info-circle text-info';
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${iconClass} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Add to toast container (create if doesn't exist)
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    $('#toastContainer').append(toastHtml);
    
    // Show toast
    const toast = new bootstrap.Toast(document.getElementById(toastId), {
        autohide: true,
        delay: 5000
    });
    toast.show();
    
    // Auto remove after hiding
    setTimeout(() => {
        $(`#${toastId}`).remove();
    }, 6000);
}

/**
 * Handle responsive design adjustments
 */
$(window).on('resize', function() {
    // Adjust layout for mobile devices
    if ($(window).width() < 768) {
        $('.provider-card').addClass('mb-2');
    } else {
        $('.provider-card').removeClass('mb-2');
    }
});

// Initialize responsive adjustments
$(window).trigger('resize');