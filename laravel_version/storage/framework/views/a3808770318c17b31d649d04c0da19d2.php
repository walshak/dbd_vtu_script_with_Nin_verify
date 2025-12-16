<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'successModal',
    'title' => 'Success!',
    'message' => 'Your transaction was completed successfully.',
    'showReceipt' => true,
    'showClose' => true
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
    'id' => 'successModal',
    'title' => 'Success!',
    'message' => 'Your transaction was completed successfully.',
    'showReceipt' => true,
    'showClose' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="<?php echo e($id); ?>" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4"><?php echo e($title); ?></h3>
            <div id="<?php echo e($id); ?>Message" class="text-gray-600 mb-6"><?php echo e($message); ?></div>

            <div class="flex space-x-3">
                <?php if($showClose): ?>
                <button onclick="hideModal('<?php echo e($id); ?>')"
                        class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Close
                </button>
                <?php endif; ?>

                <?php if($showReceipt): ?>
                <button onclick="downloadReceipt()"
                        class="flex-1 bg-green-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Receipt
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/components/modals/success.blade.php ENDPATH**/ ?>