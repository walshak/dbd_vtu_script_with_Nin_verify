<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'steps' => [],
    'currentStep' => 1,
    'color' => 'blue',
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
    'steps' => [],
    'currentStep' => 1,
    'color' => 'blue',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colorClasses = [
        'blue' => [
            'active' => 'bg-blue-500 border-blue-500',
            'completed' => 'bg-green-500 border-green-500',
            'inactive' => 'bg-white border-gray-300',
            'line' => 'from-blue-500 to-purple-600',
        ],
        'green' => [
            'active' => 'bg-green-500 border-green-500',
            'completed' => 'bg-green-600 border-green-600',
            'inactive' => 'bg-white border-gray-300',
            'line' => 'from-green-500 to-blue-600',
        ],
        'purple' => [
            'active' => 'bg-purple-500 border-purple-500',
            'completed' => 'bg-green-500 border-green-500',
            'inactive' => 'bg-white border-gray-300',
            'line' => 'from-purple-500 to-blue-600',
        ],
        'orange' => [
            'active' => 'bg-orange-500 border-orange-500',
            'completed' => 'bg-green-500 border-green-500',
            'inactive' => 'bg-white border-gray-300',
            'line' => 'from-orange-500 to-red-600',
        ],
    ];

    $colors = $colorClasses[$color] ?? $colorClasses['blue'];
    $totalSteps = count($steps);
    $progressPercentage = $totalSteps > 1 ? (($currentStep - 1) / ($totalSteps - 1)) * 100 : 0;
?>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
    <div class="flex items-center justify-between relative">
        <!-- Progress Line Background -->
        <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 transform -translate-y-1/2 z-0"></div>

        <!-- Progress Line Active -->
        <div id="progressLine"
            class="absolute top-1/2 left-0 h-0.5 bg-gradient-to-r <?php echo e($colors['line']); ?> transform -translate-y-1/2 z-0 transition-all duration-500"
            style="width: <?php echo e($progressPercentage); ?>%"></div>

        <!-- Steps Container -->
        <div class="flex justify-between w-full relative z-10">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $stepNumber = $index + 1;
                    $isActive = $stepNumber === $currentStep;
                    $isCompleted = $stepNumber < $currentStep;
                    $isInactive = $stepNumber > $currentStep;

                    if ($isCompleted) {
                        $stepClass = $colors['completed'];
                        $textClass = 'text-gray-600';
                    } elseif ($isActive) {
                        $stepClass = $colors['active'];
                        $textClass = 'text-gray-600';
                    } else {
                        $stepClass = $colors['inactive'];
                        $textClass = 'text-gray-500';
                    }
                ?>

                <div class="progress-step flex flex-col items-center" data-step="<?php echo e($stepNumber); ?>">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 <?php echo e($stepClass); ?> flex items-center justify-center text-white font-semibold text-xs sm:text-sm transition-all duration-300">
                        <?php if($isCompleted): ?>
                            <i class="fas fa-check text-xs sm:text-sm"></i>
                        <?php else: ?>
                            <?php echo e($stepNumber); ?>

                        <?php endif; ?>
                    </div>
                    <span class="text-xs font-medium <?php echo e($textClass); ?> mt-2 text-center max-w-[80px] sm:max-w-none">
                        <?php echo e($step); ?>

                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Progress indicator update function
        window.updateProgressStep = function(step) {
            const totalSteps = <?php echo e($totalSteps); ?>;
            const progressPercentage = totalSteps > 1 ? ((step - 1) / (totalSteps - 1)) * 100 : 0;

            // Update progress line
            const progressLine = document.getElementById('progressLine');
            if (progressLine) {
                progressLine.style.width = progressPercentage + '%';
            }

            // Update step indicators
            document.querySelectorAll('.progress-step').forEach((stepEl, index) => {
                const stepNumber = index + 1;
                const circle = stepEl.querySelector('div');
                const label = stepEl.querySelector('span');

                // Remove all state classes
                circle.classList.remove(
                    'bg-blue-500', 'border-blue-500',
                    'bg-green-500', 'border-green-500', 'bg-green-600', 'border-green-600',
                    'bg-purple-500', 'border-purple-500',
                    'bg-orange-500', 'border-orange-500',
                    'bg-white', 'border-gray-300',
                    'text-white', 'text-gray-500'
                );
                label.classList.remove('text-gray-600', 'text-gray-500');

                if (stepNumber < step) {
                    // Completed state
                    const completedClasses = '<?php echo e($colors['completed']); ?>'.split(' ');
                    circle.classList.add(...completedClasses, 'text-white');
                    circle.innerHTML = '<i class="fas fa-check text-xs sm:text-sm"></i>';
                    label.classList.add('text-gray-600');
                } else if (stepNumber === step) {
                    // Active state
                    const activeClasses = '<?php echo e($colors['active']); ?>'.split(' ');
                    circle.classList.add(...activeClasses, 'text-white');
                    circle.textContent = stepNumber;
                    label.classList.add('text-gray-600');
                } else {
                    // Inactive state
                    const inactiveClasses = '<?php echo e($colors['inactive']); ?>'.split(' ');
                    circle.classList.add(...inactiveClasses, 'text-gray-500');
                    circle.textContent = stepNumber;
                    label.classList.add('text-gray-500');
                }
            });
        };
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/components/progress-indicator.blade.php ENDPATH**/ ?>