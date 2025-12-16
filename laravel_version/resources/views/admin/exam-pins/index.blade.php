@extends('layouts.admin')

@section('title', 'Exam Pin Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Exam Pin Management</h1>
                    <p class="text-indigo-100 text-lg">Manage exam pin providers, pricing, and statistics</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Exam Provider
                    </button>
                    <button onclick="exportExamPins()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Providers</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $statistics['total_providers'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Providers</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $statistics['active_providers'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-cyan-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">30-Day Revenue</p>
                    <p class="text-3xl font-bold text-gray-800">₦{{ number_format($statistics['total_revenue_30d'], 2) }}</p>
                </div>
                <div class="bg-cyan-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-cyan-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Success Rate</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $statistics['success_rate'] }}%</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Pins Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Exam Pin Providers</h3>
                <div class="flex space-x-2">
                    <button onclick="openBulkUpdateModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>Bulk Update
                    </button>
                    <button onclick="showPricingCalculator()" class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-calculator mr-1"></i>Pricing Calculator
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="examPinsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buying Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit Margin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($examPins as $examPin)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="exam-pin-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="{{ $examPin->eId }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $examPin->eId }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="{{ $examPin->logo_path ?? '/assets/images/exam-default.png' }}" alt="{{ $examPin->ePlan }}"
                                     class="rounded-full mr-3" width="30" height="30">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ strtoupper($examPin->ePlan) }}</div>
                                    <div class="text-sm text-gray-500">{{ $examPin->description ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₦{{ number_format($examPin->ePrice, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($examPin->eBuyingPrice, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $profit = $examPin->ePrice - $examPin->eBuyingPrice;
                                $profitPercentage = $examPin->eBuyingPrice > 0 ?
                                    round(($profit / $examPin->eBuyingPrice) * 100, 2) : 0;
                                $badgeClass = $profit > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                ₦{{ number_format($profit, 2) }} ({{ $profitPercentage }}%)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $examPin->eStatus == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $examPin->eStatus == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editExamPin({{ $examPin->eId }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="toggleExamPinStatus({{ $examPin->eId }})"
                                        class="text-{{ $examPin->eStatus == 1 ? 'yellow' : 'green' }}-600 hover:text-{{ $examPin->eStatus == 1 ? 'yellow' : 'green' }}-900 transition-colors duration-150">
                                    <i class="fas fa-{{ $examPin->eStatus == 1 ? 'pause' : 'play' }}"></i>
                                </button>
                                <button onclick="deleteExamPin({{ $examPin->eId }})"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Exam Pin Modal -->
<div class="modal fade" id="addExamPinModal" tabindex="-1" aria-labelledby="addExamPinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExamPinModalLabel">Add Exam Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addExamPinForm">
                    <div class="mb-3">
                        <label for="examPlan" class="form-label">Exam Provider Name</label>
                        <input type="text" class="form-control" id="examPlan" name="plan" required>
                        <div class="form-text">e.g., WAEC, NECO, JAMB, NABTEB</div>
                    </div>
                    <div class="mb-3">
                        <label for="examPrice" class="form-label">Selling Price (₦)</label>
                        <input type="number" class="form-control" id="examPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="examBuyingPrice" class="form-label">Buying Price (₦)</label>
                        <input type="number" class="form-control" id="examBuyingPrice" name="buying_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="examStatus" class="form-label">Status</label>
                        <select class="form-select" id="examStatus" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveExamPin()">Save Exam Provider</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Exam Pin Modal -->
<div class="modal fade" id="editExamPinModal" tabindex="-1" aria-labelledby="editExamPinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExamPinModalLabel">Edit Exam Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editExamPinForm">
                    <input type="hidden" id="editExamPinId" name="exam_pin_id">
                    <div class="mb-3">
                        <label for="editExamPlan" class="form-label">Exam Provider Name</label>
                        <input type="text" class="form-control" id="editExamPlan" name="plan" required>
                    </div>
                    <div class="mb-3">
                        <label for="editExamPrice" class="form-label">Selling Price (₦)</label>
                        <input type="number" class="form-control" id="editExamPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editExamBuyingPrice" class="form-label">Buying Price (₦)</label>
                        <input type="number" class="form-control" id="editExamBuyingPrice" name="buying_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editExamStatus" class="form-label">Status</label>
                        <select class="form-select" id="editExamStatus" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateExamPin()">Update Exam Provider</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUpdateModalLabel">Bulk Update Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateForm">
                    <div class="mb-3">
                        <label for="updateType" class="form-label">Update Type</label>
                        <select class="form-select" id="updateType" name="update_type" required>
                            <option value="percentage">Percentage Increase/Decrease</option>
                            <option value="amount">Fixed Amount Increase/Decrease</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustmentValue" class="form-label">Adjustment Value</label>
                        <input type="number" class="form-control" id="adjustmentValue" name="adjustment_value" step="0.01" required>
                        <div class="form-text" id="adjustmentHelp">
                            Enter percentage (e.g., 10 for 10% increase, -5 for 5% decrease)
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small>Select exam providers from the table before applying bulk updates.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="applyBulkUpdate()">Apply Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Calculator Modal -->
<div class="modal fade" id="pricingCalculatorModal" tabindex="-1" aria-labelledby="pricingCalculatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pricingCalculatorModalLabel">Exam Pin Pricing Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pricingCalculatorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="calcExamPin" class="form-label">Exam Provider</label>
                                <select class="form-select" id="calcExamPin" name="exam_pin_id" required>
                                    <option value="">Select Exam Provider</option>
                                    @foreach($examPins as $examPin)
                                    <option value="{{ $examPin->eId }}">{{ strtoupper($examPin->ePlan) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="calcQuantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="calcQuantity" name="quantity" min="1" max="50" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="calcAccountType" class="form-label">Account Type</label>
                                <select class="form-select" id="calcAccountType" name="account_type" required>
                                    <option value="user">Regular User</option>
                                    <option value="agent">Agent</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="calculatePricing()">Calculate</button>
                        </div>
                    </div>
                </form>
                <div id="pricingResults" class="mt-4" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Pricing Breakdown</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Unit Price:</td>
                                            <td id="resultUnitPrice">-</td>
                                        </tr>
                                        <tr>
                                            <td>Quantity:</td>
                                            <td id="resultQuantity">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td><strong id="resultTotalAmount">-</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Profit Analysis</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Buying Price:</td>
                                            <td id="resultBuyingPrice">-</td>
                                        </tr>
                                        <tr>
                                            <td>Profit per Unit:</td>
                                            <td id="resultProfitPerUnit">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Profit:</strong></td>
                                            <td><strong id="resultTotalProfit">-</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#examPinsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] }
        ]
    });

    // Update help text based on update type
    $('#updateType').change(function() {
        if ($(this).val() === 'percentage') {
            $('#adjustmentHelp').text('Enter percentage (e.g., 10 for 10% increase, -5 for 5% decrease)');
        } else {
            $('#adjustmentHelp').text('Enter amount in Naira (e.g., 100 for ₦100 increase, -50 for ₦50 decrease)');
        }
    });
});

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.exam-pin-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function getSelectedExamPins() {
    const checkboxes = document.querySelectorAll('.exam-pin-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function saveExamPin() {
    const formData = new FormData(document.getElementById('addExamPinForm'));

    $.ajax({
        url: '{{ route("admin.exam-pins.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#addExamPinModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred while saving');
        }
    });
}

function editExamPin(examPinId) {
    // Find the exam pin data from the table
    const row = $(`input[value="${examPinId}"]`).closest('tr');
    const plan = row.find('td:nth-child(3) strong').text();
    const price = row.find('td:nth-child(4)').text().replace('₦', '').replace(',', '');
    const buyingPrice = row.find('td:nth-child(5)').text().replace('₦', '').replace(',', '');
    const status = row.find('td:nth-child(7) span').text().trim() === 'Active' ? 1 : 0;

    $('#editExamPinId').val(examPinId);
    $('#editExamPlan').val(plan);
    $('#editExamPrice').val(price);
    $('#editExamBuyingPrice').val(buyingPrice);
    $('#editExamStatus').val(status);

    $('#editExamPinModal').modal('show');
}

function updateExamPin() {
    const examPinId = $('#editExamPinId').val();
    const formData = new FormData(document.getElementById('editExamPinForm'));

    $.ajax({
        url: `{{ route("admin.exam-pins.index") }}/${examPinId}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-HTTP-Method-Override': 'PUT'
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#editExamPinModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred while updating');
        }
    });
}

function toggleExamPinStatus(examPinId) {
    if (confirm('Are you sure you want to change the status of this exam provider?')) {
        $.ajax({
            url: `{{ route("admin.exam-pins.index") }}/${examPinId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'An error occurred');
            }
        });
    }
}

function deleteExamPin(examPinId) {
    if (confirm('Are you sure you want to delete this exam provider? This action cannot be undone.')) {
        $.ajax({
            url: `{{ route("admin.exam-pins.index") }}/${examPinId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'An error occurred while deleting');
            }
        });
    }
}

function applyBulkUpdate() {
    const selectedIds = getSelectedExamPins();

    if (selectedIds.length === 0) {
        alert('Please select at least one exam provider to update');
        return;
    }

    const formData = new FormData(document.getElementById('bulkUpdateForm'));
    formData.append('exam_pin_ids', JSON.stringify(selectedIds));

    $.ajax({
        url: '{{ route("admin.exam-pins.bulk-update-prices") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#bulkUpdateModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred during bulk update');
        }
    });
}

function exportExamPins() {
    window.location.href = '{{ route("admin.exam-pins.export") }}';
}

function showPricingCalculator() {
    $('#pricingResults').hide();
    $('#pricingCalculatorModal').modal('show');
}

function calculatePricing() {
    const formData = new FormData(document.getElementById('pricingCalculatorForm'));

    $.ajax({
        url: '{{ route("admin.exam-pins.calculate-pricing") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;

                $('#resultUnitPrice').text('₦' + parseFloat(data.unit_price).toLocaleString());
                $('#resultQuantity').text(data.quantity);
                $('#resultTotalAmount').text('₦' + parseFloat(data.total_amount).toLocaleString());
                $('#resultBuyingPrice').text('₦' + parseFloat(data.buying_price).toLocaleString());
                $('#resultProfitPerUnit').text('₦' + parseFloat(data.profit_per_unit).toLocaleString());
                $('#resultTotalProfit').text('₦' + parseFloat(data.total_profit).toLocaleString());

                $('#pricingResults').show();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred during calculation');
        }
    });
}
</script>
@endsection
