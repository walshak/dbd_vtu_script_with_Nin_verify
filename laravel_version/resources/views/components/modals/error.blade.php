@props([
    'id' => 'errorModal',
    'title' => 'Error',
    'message' => 'An error occurred. Please try again.',
    'buttonText' => 'Try Again'
])

<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $title }}</h3>
            <div id="{{ $id }}Message" class="text-gray-600 mb-6">{{ $message }}</div>
            <button onclick="hideModal('{{ $id }}')"
                    class="w-full bg-red-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                {{ $buttonText }}
            </button>
        </div>
    </div>
</div>
