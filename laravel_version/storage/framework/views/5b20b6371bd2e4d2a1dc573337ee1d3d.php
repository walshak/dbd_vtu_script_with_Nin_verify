<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'VTU Admin Dashboard'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,300,400,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'nunito': ['Nunito', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                    }
                }
            }
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .box-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            border-radius: 8px 8px 0 0;
        }

        .box-body {
            padding: 20px;
        }

        .box-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-left: 4px solid #007bff;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="font-nunito antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php echo $__env->make('components.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Top Navigation Bar -->
            <?php echo $__env->make('components.admin-topbar', ['title' => $title ?? 'Dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <!-- Flash Messages -->
                    <?php if(session('success')): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc pl-5">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php $__env->startPush('scripts'); ?>
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

    <!-- Additional Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/layouts/admin.blade.php ENDPATH**/ ?>