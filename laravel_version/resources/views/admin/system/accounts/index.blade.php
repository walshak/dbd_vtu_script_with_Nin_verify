@extends('layouts.admin')

@section('title', 'System Users Management')

@section('content')
<div class="container mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-900">System Users Management</h4>
            <div class="space-x-2">
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm" onclick="openCreateSubscriberModal()">
                    <i class="fas fa-user-plus mr-1"></i> Create Subscriber
                </button>
                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm" onclick="openCreateAccountModal()">
                    <i class="fas fa-plus mr-1"></i> Create New Admin
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($accounts as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->sId }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->sFname }} {{ $account->sLname }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->sEmail }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($account->sType) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($account->sStatus === 'active')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($account->sStatus) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $account->created_at ? $account->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="viewAccount({{ $account->sId }})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editAccount({{ $account->sId }})" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($account->sStatus === 'active')
                                    <button onclick="toggleStatus({{ $account->sId }}, 'blocked')" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button onclick="toggleStatus({{ $account->sId }}, 'active')" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg">No admin accounts found</p>
                                    <p class="text-sm">Create your first admin account to get started</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-blue-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">{{ $accounts->where('sStatus', 'active')->count() }}</h3>
                    <p class="text-blue-100">Active Admins</p>
                </div>
                <i class="fas fa-user-check text-3xl text-blue-200"></i>
            </div>
        </div>
        <div class="bg-yellow-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">{{ $accounts->where('sStatus', 'blocked')->count() }}</h3>
                    <p class="text-yellow-100">Blocked Admins</p>
                </div>
                <i class="fas fa-user-times text-3xl text-yellow-200"></i>
            </div>
        </div>
        <div class="bg-indigo-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">{{ $accounts->count() }}</h3>
                    <p class="text-indigo-100">Total Admins</p>
                </div>
                <i class="fas fa-users text-3xl text-indigo-200"></i>
            </div>
        </div>
        <div class="bg-green-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">{{ $accounts->where('created_at', '>=', now()->subWeek())->count() }}</h3>
                    <p class="text-green-100">New This Week</p>
                </div>
                <i class="fas fa-user-plus text-3xl text-green-200"></i>
            </div>
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div id="createAccountModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Admin Account</h3>
            <form method="POST" action="{{ route('admin.system.accounts.create') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username (Email)</label>
                    <input type="email" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Role</option>
                        <option value="1">Super Admin</option>
                        <option value="2">Admin</option>
                        <option value="3">Support</option>
                    </select>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeCreateAccountModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">Cancel</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Subscriber Modal -->
<div id="createSubscriberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Subscriber Account</h3>
            <form method="POST" action="{{ route('admin.system.accounts.create-subscriber') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="fname" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="lname" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" name="state" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeCreateSubscriberModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Create Subscriber</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCreateAccountModal() {
    document.getElementById('createAccountModal').classList.remove('hidden');
}

function closeCreateAccountModal() {
    document.getElementById('createAccountModal').classList.add('hidden');
}

function openCreateSubscriberModal() {
    document.getElementById('createSubscriberModal').classList.remove('hidden');
}

function closeCreateSubscriberModal() {
    document.getElementById('createSubscriberModal').classList.add('hidden');
}

function viewAccount(id) {
    window.location.href = `/admin/system/accounts/${id}`;
}

function editAccount(id) {
    console.log('Edit account:', id);
    // Implementation for editing account
}

function toggleStatus(id, status) {
    if (confirm(`Are you sure you want to ${status === 'active' ? 'activate' : 'block'} this admin account?`)) {
        fetch('{{ route("admin.system.accounts.update-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id: id,
                status: status === 'active' ? '0' : '1'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Error updating account status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating account status');
        });
    }
}
</script>
@endpush