@extends('layouts.admin')

@section('title', 'Recharge Pin Discounts')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-receipt mr-2"></i>Recharge Pin Discounts
                    </h1>
                    <p class="text-orange-100 text-lg">Manage recharge pin discount rates for different user types</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Add New
                    </button>
                    <button onclick="getStatistics()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>Statistics
                    </button>
                    <button onclick="openBulkUpdateModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Bulk Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Discounts Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">All Pin Discounts</h3>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                    <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="rechargePinTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Network</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">User Pays (%)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Agent Pays (%)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Pays (%)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $cnt = 1; @endphp
                    @forelse($rechargePinDiscounts as $discount)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="discount-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500" value="{{ $discount->aId }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cnt++ }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $discount->network ? strtoupper($discount->network->network) : 'Unknown' }}</div>
                            @if($discount->network)
                            <div class="text-xs text-gray-500">ID: {{ $discount->network->nId }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ number_format($discount->aUserDiscount, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ number_format($discount->aAgentDiscount, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ number_format($discount->aVendorDiscount, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                <button onclick="editDiscount({{ $discount->aId }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-150" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="viewPricing({{ $discount->aId }})" class="text-cyan-600 hover:text-cyan-900 transition-colors duration-150" title="Calculate Pricing">
                                    <i class="fas fa-calculator"></i>
                                </button>
                                <button onclick="deleteDiscount({{ $discount->aId }})" class="text-red-600 hover:text-red-900 transition-colors duration-150" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>No discounts configured. Add discounts for networks to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Discount Modal -->
<div id="addDiscountModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Add New Discount</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="addDiscountForm" onsubmit="submitDiscount(event)">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="network" class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                        <select id="network" name="network" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Network</option>
                            @foreach($networks as $network)
                            <option value="{{ $network->nId }}">{{ strtoupper($network->network) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="userdiscount" class="block text-sm font-medium text-gray-700 mb-1">User Pays (%)</label>
                        <input type="number" step="0.01" id="userdiscount" name="userdiscount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Percentage users pay (e.g., 99 = 99% of face value)</p>
                    </div>

                    <div>
                        <label for="agentdiscount" class="block text-sm font-medium text-gray-700 mb-1">Agent Pays (%)</label>
                        <input type="number" step="0.01" id="agentdiscount" name="agentdiscount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Should be ≤ user discount</p>
                    </div>

                    <div>
                        <label for="vendordiscount" class="block text-sm font-medium text-gray-700 mb-1">Vendor Pays (%)</label>
                        <input type="number" step="0.01" id="vendordiscount" name="vendordiscount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Should be ≤ agent discount</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                        Close
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Add Discount
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Discount Modal -->
<div id="editDiscountModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Discount</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="editDiscountForm" onsubmit="updateDiscount(event)">
                @csrf
                <input type="hidden" id="editDiscountId">
                
                <div class="space-y-4">
                    <div>
                        <label for="editNetwork" class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                        <select id="editNetwork" name="network" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Network</option>
                            @foreach($networks as $network)
                            <option value="{{ $network->nId }}">{{ strtoupper($network->network) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="editUserdiscount" class="block text-sm font-medium text-gray-700 mb-1">User Pays (%)</label>
                        <input type="number" step="0.01" id="editUserdiscount" name="userdiscount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="editAgentdiscount" class="block text-sm font-medium text-gray-700 mb-1">Agent Pays (%)</label>
                        <input type="number" step="0.01" id="editAgentdiscount" name="agentdiscount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="editVendordiscount" class="block text-sm font-medium text-gray-700 mb-1">Vendor Pays (%)</label>
                        <input type="number" step="0.01" id="editVendordiscount" name="vendordiscount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                        Close
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Update Discount
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div id="bulkUpdateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Bulk Update Discounts</h3>
                <button onclick="closeBulkUpdateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="bulkUpdateForm" onsubmit="performBulkUpdate(event)">
                <div class="space-y-4">
                    <div>
                        <label for="discountType" class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                        <select id="discountType" name="discount_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="aUserDiscount">User Discount</option>
                            <option value="aAgentDiscount">Agent Discount</option>
                            <option value="aVendorDiscount">Vendor Discount</option>
                        </select>
                    </div>

                    <div>
                        <label for="adjustmentType" class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type</label>
                        <select id="adjustmentType" name="adjustment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>

                    <div>
                        <label for="adjustmentValue" class="block text-sm font-medium text-gray-700 mb-1">Adjustment Value</label>
                        <input type="number" step="0.01" id="adjustmentValue" name="adjustment_value" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Use positive values to increase, negative to decrease</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeBulkUpdateModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                        Close
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i>Update Discounts
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pricing Calculator Modal -->
<div id="pricingModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pricing Calculator</h3>
                <button onclick="closePricingModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="pricingForm" onsubmit="calculatePricing(event)">
                <input type="hidden" id="pricingNetworkId">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                        <input type="text" id="pricingNetworkName" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label for="pricingAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                        <input type="number" id="pricingAmount" min="100" value="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="pricingQuantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" id="pricingQuantity" min="1" max="20" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="pricingUserType" class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                        <select id="pricingUserType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="user">User</option>
                            <option value="agent">Agent</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        Calculate
                    </button>
                </div>
            </form>

            <div id="pricingResults" class="mt-6 hidden">
                <h6 class="font-semibold text-gray-900 mb-3">Pricing Results:</h6>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Original Amount:</span>
                        <span class="font-semibold text-gray-900" id="originalAmount"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount Rate:</span>
                        <span class="font-semibold text-gray-900" id="discountRate"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Amount to Pay:</span>
                        <span class="font-semibold text-green-600" id="amountToPay"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Savings:</span>
                        <span class="font-semibold text-orange-600" id="totalSavings"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Unit Price:</span>
                        <span class="font-semibold text-gray-900" id="unitPrice"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal functions
function openAddModal() {
    document.getElementById('addDiscountModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addDiscountModal').classList.add('hidden');
    document.getElementById('addDiscountForm').reset();
}

function closeEditModal() {
    document.getElementById('editDiscountModal').classList.add('hidden');
}

function openBulkUpdateModal() {
    const checkedDiscounts = document.querySelectorAll('.discount-checkbox:checked');
    if (checkedDiscounts.length === 0) {
        alert('Please select at least one discount to update');
        return;
    }
    
    window.selectedDiscountIds = Array.from(checkedDiscounts).map(cb => cb.value);
    document.getElementById('bulkUpdateModal').classList.remove('hidden');
}

function closeBulkUpdateModal() {
    document.getElementById('bulkUpdateModal').classList.add('hidden');
    document.getElementById('bulkUpdateForm').reset();
}

function closePricingModal() {
    document.getElementById('pricingModal').classList.add('hidden');
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.discount-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

document.getElementById('selectAllTable')?.addEventListener('change', function() {
    document.querySelectorAll('.discount-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

// Submit discount
function submitDiscount(e) {
    e.preventDefault();
    
    const form = document.getElementById('addDiscountForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.recharge-pins.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add discount'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the discount');
    });
}

// Edit discount
function editDiscount(discountId) {
    fetch(`{{ url('/admin/recharge-pins') }}/${discountId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const discount = data.discount;
            document.getElementById('editDiscountId').value = discount.aId;
            document.getElementById('editNetwork').value = discount.aNetwork;
            document.getElementById('editUserdiscount').value = discount.aUserDiscount;
            document.getElementById('editAgentdiscount').value = discount.aAgentDiscount;
            document.getElementById('editVendordiscount').value = discount.aVendorDiscount;
            document.getElementById('editDiscountModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load discount data');
    });
}

// Update discount
function updateDiscount(e) {
    e.preventDefault();
    
    const discountId = document.getElementById('editDiscountId').value;
    const formData = new FormData(document.getElementById('editDiscountForm'));
    
    fetch(`{{ url('/admin/recharge-pins') }}/${discountId}`, {
        method: 'PUT',
        body: JSON.stringify({
            _token: document.querySelector('meta[name="csrf-token"]').content,
            network: formData.get('network'),
            userdiscount: formData.get('userdiscount'),
            agentdiscount: formData.get('agentdiscount'),
            vendordiscount: formData.get('vendordiscount')
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update discount'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the discount');
    });
}

// Delete discount
function deleteDiscount(discountId) {
    if (confirm('Are you sure you want to delete this discount?')) {
        fetch(`{{ url('/admin/recharge-pins') }}/${discountId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete discount'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the discount');
        });
    }
}

// Bulk update
function performBulkUpdate(e) {
    e.preventDefault();
    
    const formData = new FormData(document.getElementById('bulkUpdateForm'));
    formData.append('discount_ids', JSON.stringify(window.selectedDiscountIds));
    
    fetch('{{ route("admin.recharge-pins.bulk-update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update discounts'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating discounts');
    });
}

// View pricing
function viewPricing(discountId) {
    fetch(`{{ url('/admin/recharge-pins') }}/${discountId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const discount = data.discount;
            document.getElementById('pricingNetworkId').value = discount.aNetwork;
            document.getElementById('pricingNetworkName').value = discount.network ? discount.network.network.toUpperCase() : 'Unknown';
            document.getElementById('pricingModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load discount data');
    });
}

// Calculate pricing
function calculatePricing(e) {
    e.preventDefault();
    
    const networkId = document.getElementById('pricingNetworkId').value;
    const amount = document.getElementById('pricingAmount').value;
    const quantity = document.getElementById('pricingQuantity').value;
    const userType = document.getElementById('pricingUserType').value;
    
    const formData = new FormData();
    formData.append('network_id', networkId);
    formData.append('amount', amount);
    formData.append('quantity', quantity);
    formData.append('user_type', userType);
    
    fetch('{{ route("admin.recharge-pins.calculate-pricing") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pricing = data.pricing;
            document.getElementById('originalAmount').textContent = '₦' + pricing.original_amount.toLocaleString();
            document.getElementById('discountRate').textContent = pricing.discount_rate + '%';
            document.getElementById('amountToPay').textContent = '₦' + pricing.amount_to_pay.toLocaleString();
            document.getElementById('totalSavings').textContent = '₦' + pricing.total_savings.toLocaleString();
            document.getElementById('unitPrice').textContent = '₦' + pricing.unit_price.toLocaleString();
            document.getElementById('pricingResults').classList.remove('hidden');
        } else {
            alert('Error: ' + (data.message || 'Failed to calculate pricing'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while calculating pricing');
    });
}

// Get statistics
function getStatistics() {
    fetch('{{ route("admin.recharge-pins.statistics") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.statistics;
            let message = `Recharge Pin Statistics:\n\n`;
            message += `Total Networks: ${stats.total_networks}\n`;
            message += `Networks with Discounts: ${stats.networks_with_discounts}\n`;
            message += `Average User Discount: ${stats.avg_user_discount?.toFixed(2)}%\n`;
            message += `Average Agent Discount: ${stats.avg_agent_discount?.toFixed(2)}%\n`;
            message += `Average Vendor Discount: ${stats.avg_vendor_discount?.toFixed(2)}%\n`;
            message += `Highest User Discount: ${stats.highest_user_discount}%\n`;
            message += `Lowest User Discount: ${stats.lowest_user_discount}%`;
            alert(message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load statistics');
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
        closeBulkUpdateModal();
        closePricingModal();
    }
});
</script>
@endpush
@endsection
