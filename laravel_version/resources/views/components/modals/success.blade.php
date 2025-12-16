@props([
    'id' => 'successModal',
    'title' => 'Success!',
    'message' => 'Your transaction was completed successfully.',
    'showReceipt' => true,
    'showClose' => true
])

<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $title }}</h3>
            <div id="{{ $id }}Message" class="text-gray-600 mb-6">{{ $message }}</div>

            <div class="flex space-x-3">
                @if($showClose)
                <button onclick="hideModal('{{ $id }}')"
                        class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Close
                </button>
                @endif

                @if($showReceipt)
                <button onclick="downloadReceipt()"
                        class="flex-1 bg-green-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Receipt
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
