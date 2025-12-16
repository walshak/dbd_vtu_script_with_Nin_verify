@extends('layouts.admin')

@section('title', 'User Management - Admin Panel')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">User Management</h1>
            <p class="text-blue-100">Manage subscribers, agents, and vendors</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User Type</label>
                <select id="user-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Users</option>
                    <option value="1">Users</option>
                    <option value="2">Agents</option>
                    <option value="3">Vendors</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="0">Active</option>
                    <option value="1">Pending</option>
                    <option value="2">Blocked</option>
                    <option value="3">Unverified</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="search-input" placeholder="Name, phone, email..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button id="filter-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-users">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Users</p>
                    <p class="text-2xl font-bold text-green-600" id="active-users">{{ $stats['active_users'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Agents</p>
                    <p class="text-2xl font-bold text-purple-600" id="agents">{{ $stats['agents'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-user-tie text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">New Today</p>
                    <p class="text-2xl font-bold text-orange-600" id="new-today">{{ $stats['new_today'] ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-user-plus text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Users List</h3>
            <button id="add-user-btn" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New User
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="users-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="users-tbody">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->phone }}</div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">₦{{ number_format($user->wallet_balance, 2) }}</div>
                            @if($user->referral_wallet > 0)
                            <div class="text-sm text-green-600">Ref: ₦{{ number_format($user->referral_wallet, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->user_type == 1) bg-blue-100 text-blue-800
                                @elseif($user->user_type == 2) bg-purple-100 text-purple-800
                                @elseif($user->user_type == 3) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $user->account_type_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->reg_status == 0) bg-green-100 text-green-800
                                @elseif($user->reg_status == 1) bg-yellow-100 text-yellow-800
                                @elseif($user->reg_status == 2) bg-red-100 text-red-800
                                @elseif($user->reg_status == 3) bg-gray-100 text-gray-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $user->registration_status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="viewUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editUser({{ $user->id }})" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($user->reg_status != 2)
                            <button onclick="blockUser({{ $user->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-ban"></i>
                            </button>
                            @else
                            <button onclick="unblockUser({{ $user->id }})" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                            <button onclick="creditUser({{ $user->id }})" class="text-purple-600 hover:text-purple-900">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($users) && method_exists($users, 'links'))
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- User Detail Modal -->
<div id="user-detail-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4 sticky top-0 bg-white z-10 pb-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">User Details</h3>
                <button onclick="closeModal('user-detail-modal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="user-detail-content" class="space-y-6">
                <!-- Loading spinner -->
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4 sticky top-0 bg-white z-10 pb-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                <button onclick="closeModal('edit-user-modal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-user-form" class="space-y-6">
                <input type="hidden" id="edit-user-id" name="user_id">

                <!-- Personal Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="edit-fname" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="edit-phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="edit-email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Account Settings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                            <select id="edit-account-type" name="account_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="1">User</option>
                                <option value="2">Agent</option>
                                <option value="3">Vendor</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="edit-status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="0">Active</option>
                                <option value="1">Pending</option>
                                <option value="2">Blocked</option>
                                <option value="3">Unverified</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verification Status</label>
                            <select id="edit-verified" name="verified" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="0">Not Verified</option>
                                <option value="1">Verified</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Wallet Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Wallet Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Wallet Balance</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₦</span>
                                <input type="number" id="edit-wallet" name="wallet" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Use credit/debit function to modify wallet</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Referral Wallet Balance</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₦</span>
                                <input type="number" id="edit-ref-wallet" name="ref_wallet" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Read-only field</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('edit-user-modal')" class="px-6 py-2 text-gray-500 hover:text-gray-700 font-medium">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Credit User Modal -->
<div id="credit-user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Credit User Account</h3>
                <button onclick="closeModal('credit-user-modal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="credit-user-form">
                <input type="hidden" id="credit-user-id" name="user_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" id="credit-amount" name="amount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="credit-description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Reason for credit"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('credit-user-modal')" class="px-4 py-2 text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">Credit Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    $('#filter-btn').click(function() {
        const userType = $('#user-type-filter').val();
        const status = $('#status-filter').val();
        const search = $('#search-input').val();

        // Reload page with filters
        const params = new URLSearchParams();
        if (userType) params.append('type', userType);
        if (status) params.append('status', status);
        if (search) params.append('search', search);

        window.location.search = params.toString();
    });

    // Credit user form
    $('#credit-user-form').submit(function(e) {
        e.preventDefault();

        const userId = $('#credit-user-id').val();
        const amount = $('#credit-amount').val();
        const description = $('#credit-description').val();

        $.post('{{ route("admin.transactions.credit-user") }}', {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            amount: amount,
            description: description
        })
        .done(function(response) {
            closeModal('credit-user-modal');
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'User account credited successfully',
                timer: 2000
            }).then(() => {
                location.reload();
            });
        })
        .fail(function(xhr) {
            let errorMsg = 'Failed to credit user account';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMsg
            });
        });
    });

    // Edit user form
    $('#edit-user-form').submit(function(e) {
        e.preventDefault();

        const userId = $('#edit-user-id').val();
        const formData = {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            name: $('#edit-fname').val(),
            phone: $('#edit-phone').val(),
            email: $('#edit-email').val(),
            user_type: $('#edit-account-type').val(),
            reg_status: $('#edit-status').val(),
            email_verified_at: $('#edit-verified').val() === '1' ? new Date().toISOString() : null
        };

        $.ajax({
            url: '{{ route("admin.system.subscribers.update") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                closeModal('edit-user-modal');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'User updated successfully',
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let errorMsg = 'Failed to update user';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMsg
                });
            }
        });
    });
});

function viewUser(userId) {
    // Show loading state
    $('#user-detail-content').html(`
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>
    `);
    $('#user-detail-modal').removeClass('hidden');

    // Fetch user details via AJAX
    $.ajax({
        url: `{{ url('/admin/users') }}/${userId}`,
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(user) {
            const statusColors = {
                0: 'bg-green-100 text-green-800',
                1: 'bg-yellow-100 text-yellow-800',
                2: 'bg-red-100 text-red-800',
                3: 'bg-gray-100 text-gray-800'
            };

            const typeColors = {
                1: 'bg-blue-100 text-blue-800',
                2: 'bg-purple-100 text-purple-800',
                3: 'bg-green-100 text-green-800'
            };

            const content = `
                <div class="space-y-6">
                    <!-- User Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-2xl font-bold">
                                ${user.name ? user.name.charAt(0) : ''}${user.name && user.name.split(' ')[1] ? user.name.split(' ')[1].charAt(0) : ''}
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">${user.name || 'N/A'}</h2>
                                <p class="text-blue-100">User ID: ${user.id}</p>
                                <p class="text-blue-100">Joined: ${user.created_at || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Info -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium">${user.name || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="font-medium">${user.phone || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">${user.email || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">State:</span>
                                    <span class="font-medium">${user.state || 'N/A'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Account Info -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Account Type:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeColors[user.user_type] || 'bg-gray-100 text-gray-800'}">
                                        ${user.account_type_name || 'Unknown'}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[user.reg_status] || 'bg-gray-100 text-gray-800'}">
                                        ${user.registration_status_name || 'Unknown'}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Verified:</span>
                                    <span class="font-medium ${user.email_verified_at ? 'text-green-600' : 'text-red-600'}">
                                        ${user.email_verified_at ? 'Yes' : 'No'}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">API Key:</span>
                                    <span class="font-mono text-sm">${user.api_key || 'Not Generated'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Information -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Wallet Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Main Wallet</div>
                                <div class="text-2xl font-bold text-blue-600">₦${parseFloat(user.wallet_balance || 0).toFixed(2)}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Referral Wallet</div>
                                <div class="text-2xl font-bold text-green-600">₦${parseFloat(user.referral_wallet || 0).toFixed(2)}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Total Balance</div>
                                <div class="text-2xl font-bold text-purple-600">₦${(parseFloat(user.wallet_balance || 0) + parseFloat(user.referral_wallet || 0)).toFixed(2)}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4 pt-4">
                        <button onclick="editUser(${userId})" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                            <i class="fas fa-edit mr-2"></i>Edit User
                        </button>
                        <button onclick="creditUser(${userId})" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium">
                            <i class="fas fa-plus-circle mr-2"></i>Credit Account
                        </button>
                        ${user.reg_status != 2 ?
                            `<button onclick="blockUser(${userId})" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium">
                                <i class="fas fa-ban mr-2"></i>Block User
                            </button>` :
                            `<button onclick="unblockUser(${userId})" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium">
                                <i class="fas fa-check mr-2"></i>Unblock User
                            </button>`
                        }
                    </div>
                </div>
            `;
            $('#user-detail-content').html(content);
        },
        error: function(xhr) {
            $('#user-detail-content').html(`
                <div class="text-center py-12">
                    <div class="text-red-500 text-4xl mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to Load User Details</h3>
                    <p class="text-gray-500">Please try again later.</p>
                    <button onclick="viewUser(${userId})" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                        <i class="fas fa-redo mr-2"></i>Retry
                    </button>
                </div>
            `);
        }
    });
}

function editUser(userId) {
    // Close user detail modal if open
    closeModal('user-detail-modal');

    // Fetch user data and populate edit form
    $.ajax({
        url: `{{ url('/admin/users') }}/${userId}`,
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(user) {
            // Populate form fields
            $('#edit-user-id').val(user.id);
            $('#edit-fname').val(user.name || '');
            $('#edit-lname').val(''); // Single name field now
            $('#edit-phone').val(user.phone || '');
            $('#edit-email').val(user.email || '');
            $('#edit-account-type').val(user.user_type || '1');
            $('#edit-status').val(user.reg_status || '0');
            $('#edit-verified').val(user.email_verified_at ? '1' : '0');
            $('#edit-wallet').val(parseFloat(user.wallet_balance || 0).toFixed(2));
            $('#edit-ref-wallet').val(parseFloat(user.referral_wallet || 0).toFixed(2));

            // Show modal
            $('#edit-user-modal').removeClass('hidden');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load user data for editing'
            });
        }
    });
}

function blockUser(userId) {
    // Close any open modals first
    closeModal('user-detail-modal');

    Swal.fire({
        title: 'Block User?',
        text: 'This will prevent the user from accessing their account',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, block user'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('/admin/users') }}/${userId}/suspend`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Blocked!',
                        text: 'User has been blocked successfully.',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to block user';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                }
            });
        }
    });
}

function unblockUser(userId) {
    // Close any open modals first
    closeModal('user-detail-modal');

    Swal.fire({
        title: 'Unblock User?',
        text: 'This will restore the user\'s access to their account',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, unblock user'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('/admin/users') }}/${userId}/activate`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Unblocked!',
                        text: 'User has been unblocked successfully.',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to unblock user';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                }
            });
        }
    });
}

function creditUser(userId) {
    // Close any open modals first
    closeModal('user-detail-modal');
    closeModal('edit-user-modal');

    // Clear form
    $('#credit-user-form')[0].reset();
    $('#credit-user-id').val(userId);
    $('#credit-user-modal').removeClass('hidden');

    // Focus on amount field
    setTimeout(() => {
        $('#credit-amount').focus();
    }, 100);
}

function closeModal(modalId) {
    $('#' + modalId).addClass('hidden');

    // Clear any form data when closing modals
    if (modalId === 'credit-user-modal') {
        $('#credit-user-form')[0].reset();
    } else if (modalId === 'edit-user-modal') {
        $('#edit-user-form')[0].reset();
    }
}

// Add keyboard support for closing modals
$(document).keydown(function(e) {
    if (e.key === 'Escape') {
        closeModal('user-detail-modal');
        closeModal('edit-user-modal');
        closeModal('credit-user-modal');
    }
});

// Close modals when clicking outside
$(document).click(function(e) {
    if ($(e.target).hasClass('fixed') && $(e.target).hasClass('inset-0')) {
        closeModal('user-detail-modal');
        closeModal('edit-user-modal');
        closeModal('credit-user-modal');
    }
});
</script>
@endpush
@endsection
