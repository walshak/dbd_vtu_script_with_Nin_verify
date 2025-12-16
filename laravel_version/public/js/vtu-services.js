/**
 * VTU Service Framework - Service-Specific Components
 * Extended components for specific VTU services
 */

class VTUServiceFramework extends VTUFramework {
    constructor() {
        super();
        this.serviceData = new Map();
        this.pricingData = new Map();
        this.currentService = null;
    }

    /**
     * Airtime Service Components
     */
    initializeAirtimeService(providers) {
        this.currentService = 'airtime';
        this.serviceData.set('airtime', { providers });

        // Setup airtime-specific validators
        this.addValidator('amount', (value) => {
            const amount = parseFloat(value);
            return amount >= 50 && amount <= 10000;
        }, 'Amount must be between ₦50 and ₦10,000');

        this.addValidator('phone', (value) => {
            return this.validatePhoneNumber(value);
        }, 'Please enter a valid 11-digit phone number');

        this.addValidator('transaction_pin', (value) => {
            return this.validateTransactionPin(value);
        }, 'Please enter your 4-digit transaction PIN');
    }

    setupAirtimeAmountButtons(containerSelector) {
        const amounts = [100, 200, 500, 1000, 2000, 5000];
        const container = document.querySelector(containerSelector);

        if (!container) return;

        const buttonsHTML = amounts.map(amount => `
            <button type="button" class="amount-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-amount="${amount}">
                <div class="text-xl font-bold text-green-600">₦${amount}</div>
                <div class="text-xs text-gray-500">${this.getAmountLabel(amount)}</div>
            </button>
        `).join('');

        container.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                ${buttonsHTML}
            </div>
        `;

        // Setup click handlers
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.amount-btn');
            if (!btn) return;

            const amount = btn.dataset.amount;
            const input = document.getElementById('amount');

            if (input) {
                input.value = amount;
                // Remove selection from all buttons
                container.querySelectorAll('.amount-btn').forEach(b => {
                    b.classList.remove('border-green-500', 'bg-green-50', 'shadow-lg');
                });
                // Add selection to clicked button
                btn.classList.add('border-green-500', 'bg-green-50', 'shadow-lg');

                // Trigger input event
                input.dispatchEvent(new Event('input'));
            }
        });
    }

    getAmountLabel(amount) {
        if (amount <= 200) return 'Small';
        if (amount <= 1000) return 'Popular';
        if (amount <= 2000) return 'Medium';
        return 'Large';
    }

    /**
     * Data Bundle Service Components
     */
    initializeDataService(providers) {
        this.currentService = 'data';
        this.serviceData.set('data', { providers });

        // Setup data-specific validators
        this.addValidator('phone', (value) => {
            return this.validatePhoneNumber(value);
        }, 'Please enter a valid 11-digit phone number');

        this.addValidator('transaction_pin', (value) => {
            return this.validateTransactionPin(value);
        }, 'Please enter your 4-digit transaction PIN');
    }

    async loadDataPlans(networkId, planContainerId) {
        this.showLoading(planContainerId, 'Loading data plans...');

        try {
            const response = await this.makeRequest('/api/data/plans', { network: networkId });

            if (response.status === 'success') {
                this.renderDataPlans(response.data, planContainerId);
            } else {
                throw new Error(response.message || 'Failed to load data plans');
            }
        } catch (error) {
            const container = document.getElementById(planContainerId);
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-red-600">${error.message}</p>
                    <button onclick="VTU.loadDataPlans('${networkId}', '${planContainerId}')"
                            class="mt-2 text-blue-600 hover:text-blue-800">
                        Try Again
                    </button>
                </div>
            `;
        }
    }

    renderDataPlans(plans, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const plansHTML = plans.map(plan => `
            <div class="data-plan-card bg-white border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:shadow-md transition-all duration-300" data-plan-id="${plan.id}">
                <input type="radio" name="data_plan" value="${plan.id}" class="hidden">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">${plan.name}</div>
                        <div class="text-sm text-gray-600">${plan.data_volume}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-green-600">₦${parseFloat(plan.fee).toLocaleString()}</div>
                        <div class="text-xs text-gray-500">${plan.validity}</div>
                    </div>
                </div>
                <div class="selection-indicator w-4 h-4 bg-gray-200 rounded-full mx-auto transition-all duration-300">
                    <i class="fas fa-check text-white text-xs"></i>
                </div>
            </div>
        `).join('');

        container.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${plansHTML}
            </div>
        `;

        // Setup selection handlers
        this.setupProviderSelection(`#${containerId}`, (planId) => {
            const selectedPlan = plans.find(p => p.id == planId);
            this.updateDataPlanSummary(selectedPlan);
        });
    }

    updateDataPlanSummary(plan) {
        if (!plan) return;

        const planEl = document.getElementById('summary-plan');
        if (planEl) planEl.textContent = plan.name;

        const volumeEl = document.getElementById('summary-volume');
        if (volumeEl) volumeEl.textContent = plan.data_volume;

        const validityEl = document.getElementById('summary-validity');
        if (validityEl) validityEl.textContent = plan.validity;

        const amountEl = document.getElementById('summary-amount');
        if (amountEl) amountEl.textContent = this.formatCurrency(plan.fee);
    }

    /**
     * Cable TV Service Components
     */
    initializeCableService(providers) {
        this.currentService = 'cable';
        this.serviceData.set('cable', { providers });

        // Setup cable-specific validators
        this.addValidator('iuc_number', (value) => {
            return value.length >= 8 && value.length <= 15 && /^[0-9]+$/.test(value);
        }, 'Please enter a valid IUC/Smart Card number');

        this.addValidator('phone', (value) => {
            return this.validatePhoneNumber(value);
        }, 'Please enter a valid 11-digit phone number');

        this.addValidator('transaction_pin', (value) => {
            return this.validateTransactionPin(value);
        }, 'Please enter your 4-digit transaction PIN');
    }

    async verifyIUCNumber(iucNumber, providerId, statusElementId) {
        const statusElement = document.getElementById(statusElementId);
        if (!statusElement) return;

        statusElement.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500 mr-2"></i>Verifying IUC number...';
        statusElement.classList.remove('hidden');

        try {
            const response = await this.makeRequest('/api/cable/verify-iuc', {
                iuc_number: iucNumber,
                provider: providerId
            });

            if (response.status === 'success') {
                statusElement.innerHTML = `
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Customer: ${response.data.customer_name} | Status: ${response.data.status}
                `;
                statusElement.className = 'text-green-600 text-sm mt-2';
                return response.data;
            } else {
                throw new Error(response.message || 'Invalid IUC number');
            }
        } catch (error) {
            statusElement.innerHTML = `
                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                ${error.message}
            `;
            statusElement.className = 'text-red-600 text-sm mt-2';
            return null;
        }
    }

    async loadCablePackages(providerId, packageContainerId) {
        this.showLoading(packageContainerId, 'Loading packages...');

        try {
            const response = await this.makeRequest('/api/cable/packages', { provider: providerId });

            if (response.status === 'success') {
                this.renderCablePackages(response.data, packageContainerId);
            } else {
                throw new Error(response.message || 'Failed to load packages');
            }
        } catch (error) {
            const container = document.getElementById(packageContainerId);
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-red-600">${error.message}</p>
                    <button onclick="VTU.loadCablePackages('${providerId}', '${packageContainerId}')"
                            class="mt-2 text-blue-600 hover:text-blue-800">
                        Try Again
                    </button>
                </div>
            `;
        }
    }

    renderCablePackages(packages, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const packageHTML = packages.map(pkg => `
            <div class="cable-package-card bg-white border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:shadow-md transition-all duration-300" data-package-id="${pkg.id}">
                <input type="radio" name="package" value="${pkg.id}" class="hidden">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">${pkg.name}</div>
                        <div class="text-sm text-gray-600">${pkg.description || 'Standard package'}</div>
                    </div>
                    <div class="text-lg font-bold text-green-600">₦${parseFloat(pkg.fee).toLocaleString()}</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = `
            <div class="space-y-3">
                ${packageHTML}
            </div>
        `;

        // Setup selection handlers
        this.setupProviderSelection(`#${containerId}`, (packageId) => {
            const selectedPackage = packages.find(p => p.id == packageId);
            this.updateCablePackageSummary(selectedPackage);
        });
    }

    updateCablePackageSummary(cablePackage) {
        if (!cablePackage) return;

        const packageEl = document.getElementById('summary-package');
        if (packageEl) packageEl.textContent = cablePackage.name;

        const amountEl = document.getElementById('summary-amount');
        if (amountEl) amountEl.textContent = this.formatCurrency(cablePackage.fee);
    }

    /**
     * Electricity Service Components
     */
    initializeElectricityService(providers) {
        this.currentService = 'electricity';
        this.serviceData.set('electricity', { providers });

        // Setup electricity-specific validators
        this.addValidator('meter_number', (value) => {
            return value.length >= 8 && value.length <= 15 && /^[0-9]+$/.test(value);
        }, 'Please enter a valid meter number');

        this.addValidator('amount', (value) => {
            const amount = parseFloat(value);
            return amount >= 100 && amount <= 50000;
        }, 'Amount must be between ₦100 and ₦50,000');

        this.addValidator('phone', (value) => {
            return this.validatePhoneNumber(value);
        }, 'Please enter a valid 11-digit phone number');

        this.addValidator('transaction_pin', (value) => {
            return this.validateTransactionPin(value);
        }, 'Please enter your 4-digit transaction PIN');
    }

    async verifyMeterNumber(meterNumber, discoId, statusElementId) {
        const statusElement = document.getElementById(statusElementId);
        if (!statusElement) return;

        statusElement.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500 mr-2"></i>Verifying meter number...';
        statusElement.classList.remove('hidden');

        try {
            const response = await this.makeRequest('/api/electricity/verify-meter', {
                meter_number: meterNumber,
                disco: discoId
            });

            if (response.status === 'success') {
                statusElement.innerHTML = `
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Customer: ${response.data.customer_name} | Address: ${response.data.address}
                `;
                statusElement.className = 'text-green-600 text-sm mt-2';
                return response.data;
            } else {
                throw new Error(response.message || 'Invalid meter number');
            }
        } catch (error) {
            statusElement.innerHTML = `
                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                ${error.message}
            `;
            statusElement.className = 'text-red-600 text-sm mt-2';
            return null;
        }
    }

    setupElectricityAmountButtons(containerSelector) {
        const amounts = [1000, 2000, 5000, 10000, 15000, 20000];
        const container = document.querySelector(containerSelector);

        if (!container) return;

        const buttonsHTML = amounts.map(amount => `
            <button type="button" class="amount-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-amount="${amount}">
                <div class="text-lg font-bold text-green-600">₦${amount.toLocaleString()}</div>
                <div class="text-xs text-gray-500">${this.getElectricityAmountLabel(amount)}</div>
            </button>
        `).join('');

        container.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                ${buttonsHTML}
            </div>
        `;

        // Setup click handlers
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.amount-btn');
            if (!btn) return;

            const amount = btn.dataset.amount;
            const input = document.getElementById('amount');

            if (input) {
                input.value = amount;
                // Remove selection from all buttons
                container.querySelectorAll('.amount-btn').forEach(b => {
                    b.classList.remove('border-green-500', 'bg-green-50', 'shadow-lg');
                });
                // Add selection to clicked button
                btn.classList.add('border-green-500', 'bg-green-50', 'shadow-lg');

                // Trigger input event
                input.dispatchEvent(new Event('input'));
            }
        });
    }

    getElectricityAmountLabel(amount) {
        if (amount <= 2000) return 'Basic';
        if (amount <= 5000) return 'Standard';
        if (amount <= 10000) return 'Premium';
        return 'Maximum';
    }

    /**
     * Exam Pin Service Components
     */
    initializeExamService(providers) {
        this.currentService = 'exam';
        this.serviceData.set('exam', { providers });

        // Setup exam-specific validators
        this.addValidator('quantity', (value) => {
            const qty = parseInt(value);
            return qty >= 1 && qty <= 50;
        }, 'Quantity must be between 1 and 50 pins');

        this.addValidator('phone', (value) => {
            return this.validatePhoneNumber(value);
        }, 'Please enter a valid 11-digit phone number');

        this.addValidator('transaction_pin', (value) => {
            return this.validateTransactionPin(value);
        }, 'Please enter your 4-digit transaction PIN');
    }

    setupExamQuantityButtons(containerSelector) {
        const quantities = [1, 5, 10, 20, 30, 50];
        const container = document.querySelector(containerSelector);

        if (!container) return;

        const buttonsHTML = quantities.map(qty => `
            <button type="button" class="quantity-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-quantity="${qty}">
                <div class="text-2xl font-bold text-green-600">${qty}</div>
                <div class="text-xs text-gray-500">${this.getQuantityLabel(qty)}</div>
            </button>
        `).join('');

        container.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                ${buttonsHTML}
            </div>
        `;

        // Setup click handlers
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.quantity-btn');
            if (!btn) return;

            const quantity = btn.dataset.quantity;
            const input = document.getElementById('quantity');

            if (input) {
                input.value = quantity;
                // Remove selection from all buttons
                container.querySelectorAll('.quantity-btn').forEach(b => {
                    b.classList.remove('border-green-500', 'bg-green-50', 'shadow-lg');
                });
                // Add selection to clicked button
                btn.classList.add('border-green-500', 'bg-green-50', 'shadow-lg');

                // Trigger input event
                input.dispatchEvent(new Event('input'));
            }
        });
    }

    getQuantityLabel(qty) {
        if (qty === 1) return 'Single';
        if (qty <= 5) return 'Small Batch';
        if (qty <= 10) return 'Standard';
        if (qty <= 20) return 'Bulk';
        return 'Maximum';
    }

    /**
     * Universal Service Summary Component
     */
    createServiceSummary(containerId, fields) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const summaryHTML = `
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shopping-cart mr-2 text-green-600"></i>
                    Purchase Summary
                </h3>

                <div class="space-y-4">
                    ${fields.map(field => `
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">${field.label}:</span>
                            <span id="summary-${field.key}" class="font-semibold ${field.highlight ? 'text-green-600' : 'text-gray-800'}">${field.default || '-'}</span>
                        </div>
                    `).join('')}

                    <!-- Wallet Balance Display -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-wallet mr-2 text-blue-500"></i>
                                Wallet Balance:
                            </span>
                            <span class="font-semibold text-blue-600" data-wallet-balance>₦${this.walletBalance.toLocaleString()}</span>
                        </div>
                        <div id="balance-status" class="text-sm mt-2 hidden">
                            <span data-balance-message></span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = summaryHTML;
    }
}

// Extend the global VTU framework
if (typeof window !== 'undefined') {
    window.VTU = new VTUServiceFramework();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VTUServiceFramework;
}
