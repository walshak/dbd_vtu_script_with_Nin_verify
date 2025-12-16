@extends('layouts.admin')

@section('title', 'Feature Toggles')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg p-6 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Feature Toggles</h1>
                <p class="text-purple-100">Control system features and their rollout</p>
            </div>
            <button id="addFeatureBtn" class="bg-white text-purple-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition duration-200">
                Add Feature
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Features</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Enabled</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['enabled'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disabled</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['disabled'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Partial Rollout</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['partial'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Feature Management</h3>
            <p class="text-sm text-gray-600 mt-1">Manage and configure system features</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feature</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rollout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Modified</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($features as $feature)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $feature->feature_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $feature->feature_key }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $feature->is_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $feature->is_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $feature->rollout_percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $feature->rollout_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $feature->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editFeature({{ json_encode($feature) }})"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button onclick="toggleFeature({{ $feature->id }}, {{ $feature->is_enabled ? 'false' : 'true' }})"
                                    class="text-{{ $feature->is_enabled ? 'red' : 'green' }}-600 hover:text-{{ $feature->is_enabled ? 'red' : 'green' }}-900">
                                {{ $feature->is_enabled ? 'Disable' : 'Enable' }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No features configured yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Feature Modal -->
<div id="featureModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add Feature Toggle</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="featureForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name</label>
                        <input type="text" name="feature_name" id="feature_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Feature Key</label>
                        <input type="text" name="feature_key" id="feature_key" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_enabled" id="is_enabled" value="1"
                                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Enabled</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rollout %</label>
                            <input type="number" name="rollout_percentage" id="rollout_percentage" min="0" max="100" value="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                        Save Feature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openModal() {
    document.getElementById('featureModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('featureModal').classList.add('hidden');
    document.getElementById('featureForm').reset();
    document.getElementById('modalTitle').textContent = 'Add Feature Toggle';
    document.getElementById('featureForm').action = '{{ route("system-configuration.feature-toggles") }}';
    document.getElementById('methodField').innerHTML = '';
}

function editFeature(feature) {
    document.getElementById('modalTitle').textContent = 'Edit Feature Toggle';
    document.getElementById('featureForm').action = `/admin/system-configuration/feature-toggles/${feature.id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';

    document.getElementById('feature_name').value = feature.feature_name;
    document.getElementById('feature_key').value = feature.feature_key;
    document.getElementById('description').value = feature.description || '';
    document.getElementById('is_enabled').checked = feature.is_enabled;
    document.getElementById('rollout_percentage').value = feature.rollout_percentage;

    openModal();
}

function toggleFeature(id, enabled) {
    if (confirm(`Are you sure you want to ${enabled ? 'enable' : 'disable'} this feature?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/system-configuration/feature-toggles/${id}`;

        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="is_enabled" value="${enabled}">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}

document.getElementById('addFeatureBtn').addEventListener('click', openModal);

// Auto-generate feature key from name
document.getElementById('feature_name').addEventListener('input', function() {
    const name = this.value;
    const key = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
    document.getElementById('feature_key').value = key;
});
</script>
@endsection
