<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadNotificationsData($controller);
$data = $data[0]; // Get the notifications directly
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Notifications</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">
</head>

<body class="theme-light">

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

<div id="page">
    <div class="header header-fixed header-auto-show header-logo-app">
        <a href="homepage.php" class="header-title header-subtitle"><?php echo $sitename; ?></a>
        <a href="#" data-menu="menu-main" class="header-icon header-icon-1"><i class="fas fa-bars"></i></a>
        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-dark"><i class="fas fa-sun"></i></a>
        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-light"><i class="fas fa-moon"></i></a>
        <a href="#" data-menu="menu-highlights" class="header-icon header-icon-3"><i class="fas fa-brush"></i></a>
    </div>

    <div class="page-content header-clear-medium">
        
        <div class="card card-style">
            <div class="content">
            <p class="mb-0 text-center font-600 color-highlight">All Notifications</p>
                <h1 class="text-center">Notifications</h1>
            </div>
        

        <div class="timeline-body mt-0">
            <div class="timeline-deco"></div>
            <?php if(!empty($data)): foreach($data as $list): ?>
            <div class="timeline-item mt-2">
                <i class="fa fa-envelope bg-blue-dark color-white shadow-l timeline-icon"></i>
                <div class="timeline-item-content rounded-s">
                    <h5 class="font-400 pt-1 pb-1"><b><?php echo $list->subject; ?></b></h5>
                    <h5 class="font-400 pt-1 pb-1">Message: <?php echo $list->message; ?></h5>
                    <h5 class="font-400 pt-1 pb-1"><span class="opacity-30"><?php echo $controller->formatDate($list->dPosted); ?></span></h5>
                </div>
            </div>	
            <?php endforeach; else : ?>
                <div class="timeline-item">
                <i class="fa fa-envelope bg-blue-dark color-white shadow-l timeline-icon"></i>
                <div class="timeline-item-content rounded-s">
                    <h5 class="font-400 pt-1 pb-1 text-danger">
                        <b>No Message Available</b>
                    </h5>
                </div>
            </div>
            <?php endif; ?>	

        </div>
    </div>

    </div>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>

</div>

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>
</body>
</html>