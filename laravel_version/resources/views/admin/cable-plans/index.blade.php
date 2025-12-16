@extends('layouts.admin')

@section('title', 'Cable Plans Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Cable Plans Management</h1>
                    <p class="text-purple-100 text-lg">Manage all cable TV plans and pricing</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Add New
                    </button>
                    <button onclick="exportPlans()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <button onclick="bulkUpdatePrices()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Bulk Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="mb-6 bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-purple-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-purple-800">Cable Plans Pricing</h3>
                <p class="mt-1 text-sm text-purple-700">
                    Cable plans are manually configured. Set selling prices based on your Uzobest API costs.
                    Uzobest does not provide a cable plan listing API, so plans must be added manually with their correct plan IDs.
                </p>
            </div>
        </div>
    </div>

    <!-- Cable Plans Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">All Cable Plans</h3>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="cablePlansTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-cloud-download-alt text-blue-500 mr-1"></i>
                                Uzobest Cost
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-green-500 mr-1"></i>
                                Selling Price
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-chart-line text-purple-500 mr-1"></i>
                                Profit
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $cnt = 1; @endphp
                    @forelse($cablePlans as $plan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="plan-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $plan->cpId }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cnt++ }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $plan->provider ? $plan->provider->provider : 'Unknown Provider' }}
                                ({{ $plan->day }} Days)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $plan->planid }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-blue-600">
                                ₦{{ number_format($plan->cost_price ?? $plan->price, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">From Uzobest</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-green-600">
                                ₦{{ number_format($plan->selling_price ?? $plan->userprice, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">Your Price</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $cost = $plan->cost_price ?? $plan->price ?? 0;
                                $selling = $plan->selling_price ?? $plan->userprice ?? 0;
                                $profit = $selling - $cost;
                                $profitClass = $profit > 0 ? 'text-green-600' : ($profit < 0 ? 'text-red-600' : 'text-gray-600');
                            @endphp
                            <div class="text-sm font-medium {{ $profitClass }}">
                                ₦{{ number_format($profit, 2) }}
                            </div>
                            @if($cost > 0)
                                <div class="text-xs text-gray-500">{{ number_format(($profit / $cost) * 100, 1) }}%</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $plan->status ?? 'active';
                                $statusClass = $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editCablePlan({{ $plan->cpId }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="togglePlanStatus({{ $plan->cpId }})"
                                        class="text-{{ $status === 'active' ? 'yellow' : 'green' }}-600 hover:text-{{ $status === 'active' ? 'yellow' : 'green' }}-900 transition-colors duration-150">
                                    <i class="fas fa-{{ $status === 'active' ? 'pause' : 'play' }}"></i>
                                </button>
                                <button onclick="deletePlan({{ $plan->cpId }})"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-tv text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No cable plans found</p>
                                <p class="text-gray-400">Click "Add New" to create your first cable plan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Cable Plan Modal -->
<div id="addCablePlans" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addCablePlansLabel">Add New Cable Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCablePlanForm" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="provider" class="form-label">Provider</label>
                            <select name="provider" id="provider" class="form-control" required>
                                <option value="">Select Provider</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->cId }}">{{ strtoupper($provider->provider) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="planname" class="form-label">Plan Name</label>
                            <input type="text" placeholder="Plan Name" name="planname" id="planname" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="planid" class="form-label">Plan Id</label>
                            <input type="text" placeholder="Plan Id" name="planid" id="planid" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="duration" class="form-label">Duration In Days</label>
                            <input type="number" placeholder="Days" name="duration" id="duration" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="price" class="form-label">Buying Price</label>
                            <input type="number" step="0.01" placeholder="Price" name="price" id="price" class="form-control" required>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="userprice" class="form-label">User Price</label>
                            <input type="number" step="0.01" placeholder="User Price" name="userprice" id="userprice" class="form-control" required>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="agentprice" class="form-label">Agent Price</label>
                            <input type="number" step="0.01" placeholder="Agent Price" name="agentprice" id="agentprice" class="form-control" required>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="vendorprice" class="form-label">Vendor Price</label>
                            <input type="number" step="0.01" placeholder="Vendor Price" name="vendorprice" id="vendorprice" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="submitCablePlan()">
                    <i class="fa fa-plus"></i> Add Plan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Cable Plan Modal (Simplified - Selling Price Only) -->
<div id="editCablePlans" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-xl bg-white">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 -m-6 mb-4 rounded-t-xl p-4">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>Edit Cable Plan Pricing
            </h3>
        </div>

        <div class="mt-4">
            <!-- Plan Info (Read-only) -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Plan:</span>
                    <span id="editPlanName" class="font-semibold text-gray-900"></span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Provider:</span>
                    <span id="editPlanProvider" class="font-semibold text-gray-900"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Duration:</span>
                    <span id="editPlanDuration" class="font-semibold text-gray-900"></span>
                </div>
            </div>

            <!-- Uzobest Cost (Read-only) -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-cloud-download-alt text-blue-500 mr-1"></i>Uzobest Cost Price
                </label>
                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-3">
                    <div class="text-2xl font-bold text-blue-600">
                        ₦<span id="editCostPrice">0.00</span>
                    </div>
                    <div class="text-xs text-blue-700 mt-1">From Uzobest API (cannot be edited)</div>
                </div>
            </div>

            <!-- Selling Price (Editable) -->
            <div class="mb-4">
                <label for="editSellingPrice" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag text-green-500 mr-1"></i>Your Selling Price
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg">₦</span>
                    <input type="number"
                           id="editSellingPrice"
                           step="0.01"
                           class="w-full pl-8 pr-4 py-3 border-2 border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-lg font-semibold"
                           placeholder="0.00"
                           oninput="updateEditProfit()">
                </div>
                <p class="text-xs text-gray-500 mt-1">This is the price customers will pay</p>
            </div>

            <!-- Profit Display (Calculated) -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-chart-line text-purple-500 mr-1"></i>Profit Per Transaction:
                    </span>
                    <span id="editProfitAmount" class="text-xl font-bold text-purple-600">₦0.00</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Profit Margin:</span>
                    <span id="editProfitMargin" class="text-lg font-semibold text-purple-700">0%</span>
                </div>
            </div>

            <input type="hidden" id="editPlanId">

            <!-- Action Buttons -->
            <div class="flex space-x-3">
                <button onclick="closeEditModal()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button onclick="submitEditCablePlan()"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium py-3 rounded-lg transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Prices Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="bulkUpdateLabel">Bulk Update Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateForm">
                    <div class="form-group mb-3">
                        <label for="priceType" class="form-label">Price Type</label>
                        <select name="price_type" id="priceType" class="form-control" required>
                            <option value="userprice">User Price</option>
                            <option value="agentprice">Agent Price</option>
                            <option value="vendorprice">Vendor Price</option>
                            <option value="price">Buying Price</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                        <select name="adjustment_type" id="adjustmentType" class="form-control" required>
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="adjustmentValue" class="form-label">Adjustment Value</label>
                        <input type="number" step="0.01" name="adjustment_value" id="adjustmentValue" class="form-control" required>
                        <small class="form-text text-muted">Use positive values to increase, negative to decrease</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="performBulkUpdate()">
                    <i class="fa fa-edit"></i> Update Prices
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#cablePlansTable').DataTable({
        "pageLength": 25,
        "order": [[ 1, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 9] }
        ],
        "responsive": true
    });

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.plan-checkbox').prop('checked', this.checked);
    });

    // Update select all when individual checkboxes change
    $('.plan-checkbox').change(function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        } else if ($('.plan-checkbox:checked').length === $('.plan-checkbox').length) {
            $('#selectAll').prop('checked', true);
        }
    });
});

function submitCablePlan() {
    const form = document.getElementById('addCablePlanForm');
    const formData = new FormData(form);

    // Show loading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
    submitBtn.disabled = true;

    fetch('{{ route("admin.cable-plans.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('addCablePlans')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add plan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the plan');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editCablePlan(planId) {
    // Fetch plan data and populate edit form
    fetch(`{{ url('/admin/cable-plans') }}/${planId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const plan = data.plan;

            // Populate plan info
            document.getElementById('editPlanId').value = plan.cpId;
            document.getElementById('editPlanName').textContent = plan.name;
            document.getElementById('editPlanProvider').textContent = plan.provider ? plan.provider.provider : 'Unknown';
            document.getElementById('editPlanDuration').textContent = plan.day + ' Days';

            // Set cost and selling prices
            const costPrice = parseFloat(plan.cost_price || plan.price || 0);
            const sellingPrice = parseFloat(plan.selling_price || plan.userprice || 0);

            document.getElementById('editCostPrice').textContent = costPrice.toFixed(2);
            document.getElementById('editSellingPrice').value = sellingPrice.toFixed(2);

            // Calculate and show profit
            updateEditProfit();

            // Show modal
            document.getElementById('editCablePlans').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load plan data');
    });
}

function updateEditProfit() {
    const costPrice = parseFloat(document.getElementById('editCostPrice').textContent || 0);
    const sellingPrice = parseFloat(document.getElementById('editSellingPrice').value || 0);

    const profit = sellingPrice - costPrice;
    const profitMargin = costPrice > 0 ? (profit / costPrice) * 100 : 0;

    document.getElementById('editProfitAmount').textContent = '₦' + profit.toFixed(2);
    document.getElementById('editProfitMargin').textContent = profitMargin.toFixed(1) + '%';

    // Color code the profit
    const profitElement = document.getElementById('editProfitAmount');
    if (profit > 0) {
        profitElement.classList.remove('text-red-600', 'text-gray-600');
        profitElement.classList.add('text-green-600');
    } else if (profit < 0) {
        profitElement.classList.remove('text-green-600', 'text-gray-600');
        profitElement.classList.add('text-red-600');
    } else {
        profitElement.classList.remove('text-green-600', 'text-red-600');
        profitElement.classList.add('text-gray-600');
    }
}

function closeEditModal() {
    document.getElementById('editCablePlans').classList.add('hidden');
}

function submitEditCablePlan() {
    const planId = document.getElementById('editPlanId').value;
    const sellingPrice = document.getElementById('editSellingPrice').value;

    if (!sellingPrice || parseFloat(sellingPrice) <= 0) {
        alert('Please enter a valid selling price');
        return;
    }

    const formData = new FormData();
    formData.append('selling_price', sellingPrice);
    formData.append('_method', 'PUT');

    fetch(`{{ url('/admin/cable-plans') }}/${planId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update plan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the plan');
    });
}

function updateCablePlan() {
    // This function is deprecated, using submitEditCablePlan instead
    submitEditCablePlan();
}

function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this cable plan?')) {
        fetch(`{{ url('/admin/cable-plans') }}/${planId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete plan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the plan');
        });
    }
}

function togglePlanStatus(planId) {
    fetch(`{{ url('/admin/cable-plans') }}/${planId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to toggle status'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while toggling status');
    });
}

function bulkUpdatePrices() {
    const checkedPlans = $('.plan-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (checkedPlans.length === 0) {
        alert('Please select at least one plan to update');
        return;
    }

    // Store selected plan IDs
    window.selectedPlanIds = checkedPlans;

    // Show bulk update modal
    new bootstrap.Modal(document.getElementById('bulkUpdateModal')).show();
}

function performBulkUpdate() {
    const form = document.getElementById('bulkUpdateForm');
    const formData = new FormData(form);
    formData.append('plan_ids', JSON.stringify(window.selectedPlanIds));

    fetch('{{ route("admin.cable-plans.bulk-update-prices") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('bulkUpdateModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update prices'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating prices');
    });
}

function exportPlans() {
    window.location.href = '{{ route("admin.cable-plans.export") }}?format=csv';
}
</script>
@endpush
