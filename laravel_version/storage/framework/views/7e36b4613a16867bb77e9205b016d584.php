<?php $__env->startSection('title', ($title ?? 'Page') . ' - VASTLEAD '); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <?php echo $__env->make('components.user-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 min-w-0">
        <!-- Top Navigation Bar -->
        <?php echo $__env->make('components.user-topbar', ['title' => $title ?? 'Page'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <?php echo $__env->yieldContent('page-content'); ?>
        </main>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<!-- VTU Framework Core -->
<script src="<?php echo e(asset('js/vtu-framework.js')); ?>"></script>
<script src="<?php echo e(asset('js/vtu-services.js')); ?>"></script>

<script>
$(document).ready(function() {
    // Mobile sidebar toggle
    $('#mobile-menu-button').click(function() {
        $('#sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
        $('#sidebar-overlay').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    // Close sidebar when clicking overlay
    $('#sidebar-overlay').click(function() {
        $('#sidebar').removeClass('translate-x-0').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Profile menu toggle
    $('#profile-menu-button').click(function(e) {
        e.stopPropagation();
        $('#profile-menu').toggleClass('hidden');
    });

    // Close profile menu when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('#profile-menu-button, #profile-menu').length) {
            $('#profile-menu').addClass('hidden');
        }
    });

    // Initialize sidebar state on mobile
    if ($(window).width() < 1024) {
        $('#sidebar').addClass('-translate-x-full');
    }

    // Auto-close mobile sidebar on resize
    $(window).resize(function() {
        if ($(window).width() >= 1024) {
            $('#sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
            $('#sidebar-overlay').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        } else {
            $('#sidebar').removeClass('translate-x-0').addClass('-translate-x-full');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/layouts/user-layout.blade.php ENDPATH**/ ?>