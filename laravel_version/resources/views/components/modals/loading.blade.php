@props([
    'id' => 'loadingModal',
    'message' => 'Processing your request...',
    'title' => 'Processing'
])

<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
            <p id="{{ $id }}Text" class="text-gray-600 text-sm">{{ $message }}</p>
        </div>
    </div>
</div>
