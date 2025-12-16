<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'loadingModal',
    'message' => 'Processing your request...',
    'title' => 'Processing'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'id' => 'loadingModal',
    'message' => 'Processing your request...',
    'title' => 'Processing'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="<?php echo e($id); ?>" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo e($title); ?></h3>
            <p id="<?php echo e($id); ?>Text" class="text-gray-600 text-sm"><?php echo e($message); ?></p>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\HARDMOTIONS\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/components/modals/loading.blade.php ENDPATH**/ ?>