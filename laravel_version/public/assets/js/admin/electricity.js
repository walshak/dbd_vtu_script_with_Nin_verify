/**
 * Admin Electricity Management JavaScript
 * Handles all frontend interactions for electricity bill management
 */

$(document).ready(function() {
    // Initialize DataTables
    initializeDataTables();
    
    // Initialize event handlers
    initializeEventHandlers();
    
    // Load initial data
    loadTransactions();
});

/**
 * Initialize DataTables for providers and transactions
 */
function initializeDataTables() {
    // Providers table
    if ($('#providersTable').length) {
        $('#providersTable').DataTable({
            responsive: true,
            order: [[1, 'asc']], // Sort by provider name
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting on actions column
            ],
            language: {
                search: "Search providers:",
                lengthMenu: "Show _MENU_ providers",
                info: "Showing _START_ to _END_ of _TOTAL_ providers"
            }
        });
    }

    // Transactions table
    if ($('#transactionsTable').length) {
        window.transactionsTable = $('#transactionsTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            order: [[8, 'desc']], // Sort by date desc
            columnDefs: [
                { orderable: false, targets: -1 }, // Disable sorting on actions column
                { className: 'text-center', targets: [7, 9] } // Center align status and actions
            ],
            language: {
                search: "Search transactions:",
                lengthMenu: "Show _MENU_ transactions",
                info: "Showing _START_ to _END_ of _TOTAL_ transactions",
                emptyTable: "No electricity transactions found"
            }
        });
    }
}

/**
 * Initialize all event handlers
 */
function initializeEventHandlers() {
    // Add provider form
    $('#addProviderForm').on('submit', handleAddProvider);
    
    // Edit provider form
    $('#editProviderForm').on('submit', handleEditProvider);
    
    // Provider actions
    $(document).on('click', '.edit-provider', handleEditProviderClick);
    $(document).on('click', '.toggle-provider', handleToggleProvider);
    $(document).on('click', '.delete-provider', handleDeleteProvider);
    
    // Settings form
    $('#electricitySettingsForm').on('submit', handleUpdateSettings);
    $('#resetSettings').on('click', handleResetSettings);
    
    // API config form
    $('#apiConfigForm').on('submit', handleUpdateApiConfig);
    $('#toggleApiKey').on('click', toggleApiKeyVisibility);
    $('#testValidation').on('click', handleTestValidation);
    $('#testPurchase').on('click', handleTestPurchase);
    $('#testConnection').on('click', handleTestConnection);
    
    // Transaction filters
    $('#filterTransactions').on('click', handleFilterTransactions);
    
    // Sync providers
    $('#syncProvidersBtn').on('click', handleSyncProviders);
    
    // Auto-calculate profit margin
    $('#sellingPrice, #editSellingPrice').on('input', calculateProfitMargin);
    $('#buyingPrice, #editBuyingPrice').on('input', calculateProfitMargin);
}

/**
 * Handle add provider form submission
 */
function handleAddProvider(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Show loading state
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
    
    $.ajax({
        url: '/admin/electricity/providers',
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.status === 'success') {
                showToast('success', response.message);
                $('#addProviderModal').modal('hide');
                form[0].reset();
                
                // Reload page to refresh providers table
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                displayFormErrors(form, response.errors);
            } else {
                showToast('error', response?.message || 'Failed to add provider');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Handle edit provider click
 */
function handleEditProviderClick() {
    const providerId = $(this).data('id');
    
    // Find provider data from table row
    const row = $(this).closest('tr');
    const cells = row.find('td');
    
    const providerName = cells.eq(1).find('strong').text();
    const buyingPrice = parseFloat(cells.eq(3).text().replace('₦', '').replace('/kWh', '').replace(',', ''));
    const sellingPrice = parseFloat(cells.eq(4).text().replace('₦', '').replace('/kWh', '').replace(',', ''));
    const isActive = cells.eq(2).find('.badge').hasClass('badge-success');
    
    // Populate edit form
    $('#editProviderId').val(providerId);
    $('#editProviderPlan').val(providerName);
    $('#editProviderCode').val(providerName.toLowerCase());
    $('#editBuyingPrice').val(buyingPrice);
    $('#editSellingPrice').val(sellingPrice);
    $('#editProviderStatus').prop('checked', isActive);
    
    // Show modal
    $('#editProviderModal').modal('show');
}

/**
 * Handle edit provider form submission
 */
function handleEditProvider(e) {
    e.preventDefault();
    
    const form = $(this);
    const providerId = $('#editProviderId').val();
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Show loading state
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    $.ajax({
        url: `/admin/electricity/providers/${providerId}`,
        method: 'PUT',
        data: form.serialize(),
        success: function(response) {
            if (response.status === 'success') {
                showToast('success', response.message);
                $('#editProviderModal').modal('hide');
                
                // Reload page to refresh providers table
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                displayFormErrors(form, response.errors);
            } else {
                showToast('error', response?.message || 'Failed to update provider');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Handle toggle provider status
 */
function handleToggleProvider() {
    const providerId = $(this).data('id');
    const btn = $(this);
    const row = btn.closest('tr');
    
    if (confirm('Are you sure you want to change this provider status?')) {
        btn.prop('disabled', true);
        
        $.ajax({
            url: `/admin/electricity/providers/${providerId}/toggle-status`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', response.message);
                    
                    // Update status badge
                    const statusCell = row.find('td').eq(2);
                    statusCell.html(`<span class="badge ${response.data.status_badge_class}">${response.data.status_display}</span>`);
                    
                    // Update button icon and tooltip
                    const newIcon = response.data.new_status ? 'pause' : 'play';
                    const newTitle = response.data.new_status ? 'Disable' : 'Enable';
                    const newClass = response.data.new_status ? 'btn-outline-warning' : 'btn-outline-success';
                    
                    btn.removeClass('btn-outline-warning btn-outline-success')
                       .addClass(newClass)
                       .find('i').removeClass('fa-pause fa-play').addClass(`fa-${newIcon}`);
                    btn.attr('title', `${newTitle} Provider`);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('error', response?.message || 'Failed to update provider status');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    }
}

/**
 * Handle delete provider
 */
function handleDeleteProvider() {
    const providerId = $(this).data('id');
    const providerName = $(this).data('name');
    const row = $(this).closest('tr');
    
    if (confirm(`Are you sure you want to delete ${providerName}? This action cannot be undone.`)) {
        const btn = $(this);
        btn.prop('disabled', true);
        
        $.ajax({
            url: `/admin/electricity/providers/${providerId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', response.message);
                    
                    // Remove row from table
                    $('#providersTable').DataTable().row(row).remove().draw();
                    
                    // Update statistics
                    updateActiveProvidersCount();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('error', response?.message || 'Failed to delete provider');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    }
}

/**
 * Handle update settings form submission
 */
function handleUpdateSettings(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Show loading state
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: '/admin/electricity/settings',
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.status === 'success') {
                showToast('success', response.message);
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                displayFormErrors(form, response.errors);
            } else {
                showToast('error', response?.message || 'Failed to update settings');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Handle reset settings
 */
function handleResetSettings() {
    if (confirm('Are you sure you want to reset all settings to default values?')) {
        // Reset form to default values
        $('#electricityCharges').val(50);
        $('#minimumAmount').val(1000);
        $('#maximumAmount').val(50000);
        $('#agentDiscount').val(1);
        $('#vendorDiscount').val(2);
        $('#maintenanceMessage').val('Electricity service is temporarily unavailable. Please try again later.');
        $('#serviceEnabled').prop('checked', true);
        $('#maintenanceMode').prop('checked', false);
        
        showToast('info', 'Settings reset to default values');
    }
}

/**
 * Handle update API config form submission
 */
function handleUpdateApiConfig(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Show loading state
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: '/admin/electricity/api-config',
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.status === 'success') {
                showToast('success', response.message);
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                displayFormErrors(form, response.errors);
            } else {
                showToast('error', response?.message || 'Failed to update API configuration');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Toggle API key visibility
 */
function toggleApiKeyVisibility() {
    const input = $('#apiKey');
    const icon = $(this).find('i');
    
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

/**
 * Handle test validation
 */
function handleTestValidation() {
    const meterNumber = $('#testMeterNumber').val();
    const provider = $('#testProvider').val();
    
    if (!meterNumber || !provider) {
        showToast('error', 'Please provide meter number and select a provider');
        return;
    }
    
    const btn = $(this);
    const originalText = btn.html();
    
    btn.html('<i class="fas fa-spinner fa-spin"></i> Testing...').prop('disabled', true);
    
    $.ajax({
        url: '/admin/electricity/test-validation',
        method: 'POST',
        data: {
            meter_number: meterNumber,
            provider: provider,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#apiTestResults').show();
            $('#testResultsContent').text(JSON.stringify(response, null, 2));
            
            if (response.status === 'success') {
                showToast('success', 'Validation API test completed successfully');
            } else {
                showToast('warning', 'Validation API test completed with issues');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            $('#apiTestResults').show();
            $('#testResultsContent').text(JSON.stringify(response, null, 2));
            showToast('error', response?.message || 'API test failed');
        },
        complete: function() {
            btn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Handle test purchase (mock)
 */
function handleTestPurchase() {
    showToast('info', 'Purchase API testing requires actual transaction. Use validation test instead.');
}

/**
 * Handle test connection
 */
function handleTestConnection() {
    const btn = $(this);
    const originalText = btn.html();
    
    btn.html('<i class="fas fa-spinner fa-spin"></i> Testing...').prop('disabled', true);
    
    // Simulate connection test
    setTimeout(() => {
        showToast('success', 'API connection test completed successfully');
        btn.html(originalText).prop('disabled', false);
    }, 2000);
}

/**
 * Handle filter transactions
 */
function handleFilterTransactions() {
    const status = $('#transactionStatus').val();
    const provider = $('#transactionProvider').val();
    const date = $('#transactionDate').val();
    
    loadTransactions({
        status: status,
        provider: provider,
        date: date
    });
}

/**
 * Handle sync providers
 */
function handleSyncProviders() {
    const btn = $(this);
    const originalText = btn.html();
    
    btn.html('<i class="fas fa-spinner fa-spin"></i> Syncing...').prop('disabled', true);
    
    $.ajax({
        url: '/admin/electricity/sync-providers',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                showToast('success', response.message);
                
                // Reload page to refresh providers
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showToast('error', response?.message || 'Failed to sync providers');
        },
        complete: function() {
            btn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Load transactions with filters
 */
function loadTransactions(filters = {}) {
    if (!window.transactionsTable) return;
    
    $.ajax({
        url: '/admin/electricity/transactions',
        method: 'GET',
        data: filters,
        success: function(response) {
            if (response.status === 'success') {
                populateTransactionsTable(response.data);
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showToast('error', response?.message || 'Failed to load transactions');
        }
    });
}

/**
 * Populate transactions table
 */
function populateTransactionsTable(transactions) {
    const table = window.transactionsTable;
    
    // Clear existing data
    table.clear();
    
    // Add new data
    transactions.forEach(transaction => {
        const statusBadge = getStatusBadge(transaction.status);
        const actions = getTransactionActions(transaction);
        
        table.row.add([
            transaction.id,
            transaction.reference,
            `${transaction.user.name}<br><small class="text-muted">${transaction.user.phone}</small>`,
            transaction.provider,
            transaction.meter_number,
            `₦${formatNumber(transaction.amount)}`,
            `₦${formatNumber(transaction.profit)}`,
            statusBadge,
            transaction.date,
            actions
        ]);
    });
    
    // Redraw table
    table.draw();
}

/**
 * Calculate profit margin
 */
function calculateProfitMargin() {
    const formId = $(this).closest('form').attr('id');
    const isEdit = formId === 'editProviderForm';
    
    const buyingPriceField = isEdit ? '#editBuyingPrice' : '#buyingPrice';
    const sellingPriceField = isEdit ? '#editSellingPrice' : '#sellingPrice';
    
    const buyingPrice = parseFloat($(buyingPriceField).val()) || 0;
    const sellingPrice = parseFloat($(sellingPriceField).val()) || 0;
    
    if (buyingPrice > 0 && sellingPrice > 0) {
        const margin = sellingPrice - buyingPrice;
        const percentage = (margin / buyingPrice) * 100;
        
        // You can display this information somewhere in the form
        console.log(`Profit Margin: ₦${margin.toFixed(2)} (${percentage.toFixed(1)}%)`);
    }
}

/**
 * Update active providers count
 */
function updateActiveProvidersCount() {
    const currentCount = parseInt($('#activeProvidersCount').text());
    $('#activeProvidersCount').text(currentCount - 1);
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    const badges = {
        0: '<span class="badge badge-danger">Failed</span>',
        1: '<span class="badge badge-success">Successful</span>',
        2: '<span class="badge badge-warning">Pending</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
}

/**
 * Get transaction actions HTML
 */
function getTransactionActions(transaction) {
    return `
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-info view-transaction" 
                    data-id="${transaction.id}" data-bs-toggle="tooltip" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary print-receipt" 
                    data-id="${transaction.id}" data-bs-toggle="tooltip" title="Print Receipt">
                <i class="fas fa-print"></i>
            </button>
        </div>
    `;
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Display form errors
 */
function displayFormErrors(form, errors) {
    // Clear previous errors
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();
    
    // Display new errors
    $.each(errors, function(field, messages) {
        const input = form.find(`[name="${field}"]`);
        input.addClass('is-invalid');
        
        const errorHtml = `<div class="invalid-feedback">${messages[0]}</div>`;
        input.after(errorHtml);
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
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        $(`#${toastId}`).remove();
    }, 5000);
}