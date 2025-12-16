/**
 * VTU Framework - Core JavaScript Library
 * Consolidates common functionality across all VTU service views
 * Version: 1.0.0
 */

class VTUFramework {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.validators = new Map();
        this.walletBalance = 0;
        this.currentStep = 1;
        this.formData = {};
        this.lastTransaction = null;

        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    /**
     * Initialize framework
     */
    init() {
        this.setupCSRFToken();
        this.loadWalletBalance();
        this.setupGlobalErrorHandlers();
    }

    /**
     * Setup CSRF token for all AJAX requests
     */
    setupCSRFToken() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            }
        });
    }

    /**
     * Load wallet balance
     */
    loadWalletBalance() {
        const balanceElement = document.getElementById('walletBalance');
        if (balanceElement) {
            const balance = balanceElement.textContent.replace(/[₦,]/g, '');
            this.walletBalance = parseFloat(balance) || 0;
        }
    }

    /**
     * Update wallet balance display
     */
    updateWalletBalance(newBalance) {
        this.walletBalance = parseFloat(newBalance);
        const balanceElements = document.querySelectorAll('[id="walletBalance"], [data-wallet-balance]');
        balanceElements.forEach(el => {
            el.textContent = this.formatCurrency(this.walletBalance);
        });
    }

    /**
     * Format currency
     */
    formatCurrency(amount) {
        return '₦' + parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * Form Validation
     */
    addValidator(fieldName, validatorFn, errorMessage) {
        this.validators.set(fieldName, { fn: validatorFn, message: errorMessage });
    }

    validateField(fieldName, value) {
        const validator = this.validators.get(fieldName);
        if (!validator) return { valid: true };

        const isValid = validator.fn(value);
        return {
            valid: isValid,
            message: isValid ? '' : validator.message
        };
    }

    showFieldError(fieldName, message) {
        const errorElement = document.getElementById(`${fieldName}-error`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }

        const inputElement = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (inputElement) {
            inputElement.classList.add('border-red-500');
        }
    }

    hideFieldError(fieldName) {
        const errorElement = document.getElementById(`${fieldName}-error`);
        if (errorElement) {
            errorElement.classList.add('hidden');
        }

        const inputElement = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (inputElement) {
            inputElement.classList.remove('border-red-500');
        }
    }

    validateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        let isValid = true;
        const formData = new FormData(form);

        for (const [fieldName, value] of formData.entries()) {
            const result = this.validateField(fieldName, value);
            if (!result.valid) {
                this.showFieldError(fieldName, result.message);
                isValid = false;
            } else {
                this.hideFieldError(fieldName);
            }
        }

        return isValid;
    }

    /**
     * Phone number validation
     */
    validatePhoneNumber(phone) {
        const cleaned = phone.replace(/\D/g, '');
        return cleaned.length === 11 && /^0[789][01]\d{8}$/.test(cleaned);
    }

    /**
     * Transaction PIN validation
     */
    validateTransactionPin(pin) {
        return /^\d{4}$/.test(pin);
    }

    /**
     * AJAX Request Handler
     */
    async makeRequest(url, data = {}, method = 'POST') {
        try {
            const response = await $.ajax({
                url: url,
                type: method,
                data: { ...data, _token: this.csrfToken },
                dataType: 'json'
            });
            return response;
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }

    /**
     * Modal Management
     */
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    showLoadingModal(message = 'Processing...') {
        const modal = document.getElementById('loadingModal');
        if (modal) {
            const messageElement = modal.querySelector('[data-loading-message]');
            if (messageElement) {
                messageElement.textContent = message;
            }
            this.showModal('loadingModal');
        }
    }

    hideLoadingModal() {
        this.hideModal('loadingModal');
    }

    showSuccessModal(title, message, data = null) {
        const modal = document.getElementById('successModal');
        if (!modal) return;

        // Update title
        const titleElement = modal.querySelector('[data-success-title]');
        if (titleElement) {
            titleElement.textContent = title;
        }

        // Update message
        const messageElement = modal.querySelector('[data-success-message]') || document.getElementById('successMessage');
        if (messageElement) {
            let html = `<p class="text-lg font-medium text-green-800 mb-4">${message}</p>`;

            if (data) {
                html += `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-left">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            ${data.reference ? `<div><strong>Reference:</strong> ${data.reference}</div>` : ''}
                            ${data.amount ? `<div><strong>Amount:</strong> ${this.formatCurrency(data.amount)}</div>` : ''}
                            ${data.phone ? `<div><strong>Phone:</strong> ${data.phone}</div>` : ''}
                            ${data.network ? `<div><strong>Network:</strong> ${data.network}</div>` : ''}
                            ${data.new_balance ? `<div><strong>New Balance:</strong> ${this.formatCurrency(data.new_balance)}</div>` : ''}
                            <div><strong>Status:</strong> <span class="text-green-600">Success</span></div>
                        </div>
                    </div>
                `;
            }

            messageElement.innerHTML = html;
        }

        this.lastTransaction = data;
        this.showModal('successModal');
    }

    showErrorModal(title, message) {
        const modal = document.getElementById('errorModal');
        if (!modal) return;

        // Update title
        const titleElement = modal.querySelector('[data-error-title]');
        if (titleElement) {
            titleElement.textContent = title;
        }

        // Update message
        const messageElement = modal.querySelector('[data-error-message]') || document.getElementById('errorMessage');
        if (messageElement) {
            messageElement.innerHTML = `<p class="text-red-600">${message}</p>`;
        }

        this.showModal('errorModal');
    }

    /**
     * Toast Notifications
     */
    showToast(message, type = 'info', duration = 3000) {
        const bgColor = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        }[type] || 'bg-blue-500';

        const icon = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        }[type] || 'fa-info-circle';

        const toast = $(`
            <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full flex items-center space-x-3">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            </div>
        `);

        $('body').append(toast);

        // Animate in
        setTimeout(() => {
            toast.removeClass('translate-x-full');
        }, 10);

        // Animate out and remove
        setTimeout(() => {
            toast.addClass('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    /**
     * Progress Step Management
     */
    updateProgressStep(step) {
        this.currentStep = step;
        if (typeof window.updateProgressStep === 'function') {
            window.updateProgressStep(step);
        }
    }

    /**
     * Provider/Network Selection Handler
     */
    setupProviderSelection(containerSelector, onSelectCallback) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        container.addEventListener('click', (e) => {
            const card = e.target.closest('[data-network], [data-provider], [data-plan-id], [data-package-id]');
            if (!card) return;

            // Get the value
            const value = card.dataset.network || card.dataset.provider || card.dataset.planId || card.dataset.packageId;

            // Update radio button
            const radio = card.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }

            // Remove selection from all cards
            container.querySelectorAll('.network-card, .provider-card, .plan-card, .package-card, .disco-card, .meter-type-card').forEach(c => {
                c.classList.remove('selected', 'border-green-500', 'border-blue-500', 'border-purple-500', 'border-orange-500', 'bg-green-50', 'bg-blue-50', 'bg-purple-50', 'bg-orange-50');
            });

            // Add selection to clicked card
            const cardElement = card.querySelector('.network-card, .provider-card, .plan-card, .package-card, .disco-card, .meter-type-card');
            if (cardElement) {
                cardElement.classList.add('selected');
            }

            // Call callback
            if (onSelectCallback) {
                onSelectCallback(value);
            }
        });
    }

    /**
     * Form Submission Handler
     */
    setupFormSubmission(formId, submitUrl, options = {}) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate form
            if (options.validate && !this.validateForm(formId)) {
                this.showToast('Please fill all required fields correctly', 'error');
                return;
            }

            // Get form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Show loading
            this.showLoadingModal(options.loadingMessage || 'Processing your request...');

            // Disable submit button
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.querySelector('[id*="text"]')?.textContent;
                const textElement = submitBtn.querySelector('[id*="text"]');
                const iconElement = submitBtn.querySelector('[id*="icon"]');
                const loadingElement = submitBtn.querySelector('[id*="loading"]');

                if (textElement) textElement.textContent = 'Processing...';
                if (iconElement) iconElement.classList.add('hidden');
                if (loadingElement) loadingElement.classList.remove('hidden');
            }

            try {
                const response = await this.makeRequest(submitUrl, data);

                this.hideLoadingModal();

                if (response.status === 'success') {
                    // Update wallet balance
                    if (response.data?.new_balance) {
                        this.updateWalletBalance(response.data.new_balance);
                    }

                    // Show success modal
                    this.showSuccessModal(
                        options.successTitle || 'Success!',
                        response.message || 'Transaction completed successfully',
                        response.data
                    );

                    // Reset form if specified
                    if (options.resetOnSuccess) {
                        form.reset();
                        this.currentStep = 1;
                        this.updateProgressStep(1);
                    }

                    // Call success callback
                    if (options.onSuccess) {
                        options.onSuccess(response);
                    }
                } else {
                    this.showErrorModal(
                        options.errorTitle || 'Transaction Failed',
                        response.message || 'An error occurred. Please try again.'
                    );

                    if (options.onError) {
                        options.onError(response);
                    }
                }
            } catch (error) {
                this.hideLoadingModal();

                const errorMessage = error.responseJSON?.message ||
                                   error.message ||
                                   'Network error. Please check your connection and try again.';

                this.showErrorModal(
                    options.errorTitle || 'Error',
                    errorMessage
                );

                if (options.onError) {
                    options.onError(error);
                }
            } finally {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    const textElement = submitBtn.querySelector('[id*="text"]');
                    const iconElement = submitBtn.querySelector('[id*="icon"]');
                    const loadingElement = submitBtn.querySelector('[id*="loading"]');

                    if (textElement && options.submitText) textElement.textContent = options.submitText;
                    if (iconElement) iconElement.classList.remove('hidden');
                    if (loadingElement) loadingElement.classList.add('hidden');
                }
            }
        });
    }

    /**
     * Dynamic Summary Update
     */
    updateSummary(summaryData) {
        Object.keys(summaryData).forEach(key => {
            const element = document.getElementById(`summary-${key}`);
            if (element) {
                element.textContent = summaryData[key];
            }
        });

        // Show/hide summary container
        const summaryContainer = document.getElementById('summary-content');
        const emptyState = document.getElementById('summary-empty');

        if (summaryContainer && emptyState) {
            const hasData = Object.values(summaryData).some(val => val && val !== '-');
            if (hasData) {
                summaryContainer.classList.remove('hidden');
                emptyState.classList.add('hidden');
            } else {
                summaryContainer.classList.add('hidden');
                emptyState.classList.remove('hidden');
            }
        }
    }

    /**
     * Loading State Management
     */
    showLoading(elementId, message = 'Loading...') {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
                    <span class="text-gray-600">${message}</span>
                </div>
            `;
        }
    }

    /**
     * Global Error Handlers
     */
    setupGlobalErrorHandlers() {
        // Handle AJAX errors globally
        $(document).ajaxError((event, jqXHR, settings, thrownError) => {
            if (jqXHR.status === 401) {
                this.showToast('Session expired. Please login again.', 'error');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else if (jqXHR.status === 419) {
                this.showToast('Page expired. Refreshing...', 'warning');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }

    /**
     * Download Receipt
     */
    downloadReceipt(reference) {
        if (!reference && this.lastTransaction?.reference) {
            reference = this.lastTransaction.reference;
        }

        if (reference) {
            window.open(`/transactions/receipt/${reference}`, '_blank');
        }
    }

    /**
     * Load Recent Transactions
     */
    async loadRecentTransactions(service, containerId, limit = 3) {
        try {
            const response = await this.makeRequest('/transactions/recent', { service, limit }, 'GET');

            if (response.status === 'success' && response.data.length > 0) {
                this.renderRecentTransactions(response.data, containerId);
            }
        } catch (error) {
            console.error('Failed to load recent transactions:', error);
        }
    }

    renderRecentTransactions(transactions, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const html = transactions.map(transaction => {
            const statusClass = {
                'Completed': 'text-green-600',
                'Success': 'text-green-600',
                'Pending': 'text-yellow-600',
                'Failed': 'text-red-600'
            }[transaction.status] || 'text-gray-600';

            return `
                <div class="p-3 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-sm">${transaction.network || transaction.provider || transaction.service}</p>
                            <p class="text-xs text-gray-500">${transaction.phone || transaction.meter_number || transaction.iuc_number || ''}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-sm">${this.formatCurrency(transaction.amount)}</p>
                            <p class="text-xs ${statusClass}">${transaction.status}</p>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }
}

// Initialize global instance
if (typeof window !== 'undefined') {
    window.VTU = new VTUFramework();

    // Expose hideModal globally for modal close buttons
    window.hideModal = (modalId) => window.VTU.hideModal(modalId);

    // Expose downloadReceipt globally
    window.downloadReceipt = (reference) => window.VTU.downloadReceipt(reference);
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VTUFramework;
}
