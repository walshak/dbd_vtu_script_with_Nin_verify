<?php<?php<?php<?php

// Include common initialization

require_once __DIR__ . '/includes/common_init.php';// Include common initialization



// Load data for this pagerequire_once __DIR__ . '/includes/common_init.php';// Include common initialization// Include common initialization

require_once __DIR__ . '/includes/data_loaders.php';

$homepageData = loadHomepageData($controller);



// Set up data variables for the homepage design// Load data for this pagerequire_once __DIR__ . '/includes/common_init.php';require_once("includes/common_init.php");

$data = $homepageData[0]; // User data

$data2 = isset($homepageData[1]) ? $homepageData[1] : null; // API configrequire_once __DIR__ . '/includes/data_loaders.php';

$data3 = isset($homepageData[2]) ? $homepageData[2] : null; // Site settings

$homepageData = loadHomepageData($controller);require_once("includes/data_loaders.php");

// Get homepage design from site settings

$design = isset($data3->homedesign) ? $data3->homedesign : '1';

?>

// Get homepage design from site settings// Load data for this page

<!DOCTYPE html>

<html lang="en">$design = isset($homepageData[2]->homedesign) ? $homepageData[2]->homedesign : '1';



<head>$color = $sitecolor;require_once __DIR__ . '/includes/data_loaders.php';// Load page-specific data

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="apple-mobile-web-app-capable" content="yes">$name = $sitename;

    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />$data = loadHomepageData($controller);$pageData = loadHomepageData($controller);

    <title><?php echo $sitename; ?> - Dashboard</title>

    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">// Include the appropriate homepage design

    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">$homepageFile = __DIR__ . '/homepages/homepage' . $design . '.php';$profileData = $pageData[0]; // User profile

    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">

    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">if (!file_exists($homepageFile)) {

</head>

    $homepageFile = __DIR__ . '/homepages/homepage1.php'; // Default fallback// Get homepage design from site settings$apiConfig = $pageData[1]; // API configuration

<body class="theme-light">

}

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

?>$design = isset($data[2]->homedesign) ? $data[2]->homedesign : '1';$siteSettings = $pageData[2]; // Site settings (already loaded in common_init)

<div id="page">

    <div class="header header-fixed header-auto-show header-logo-app">

        <a href="homepage.php" class="header-title header-subtitle"><?php echo $sitename; ?></a>

        <a href="#" data-menu="menu-main" class="header-icon header-icon-1"><i class="fas fa-bars"></i></a><!DOCTYPE html>$color = $sitecolor;?>

        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-dark"><i class="fas fa-sun"></i></a>

        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-light"><i class="fas fa-moon"></i></a><html lang="en">

        <a href="#" data-menu="menu-highlights" class="header-icon header-icon-3"><i class="fas fa-brush"></i></a>

    </div>$name = $sitename;<!DOCTYPE html>



    <?php <head>

    // Include the appropriate homepage design

    $homepageFile = __DIR__ . '/homepages/homepage' . $design . '.php';    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><html lang="en">

    if (file_exists($homepageFile)) {

        include($homepageFile);    <meta name="apple-mobile-web-app-capable" content="yes">

    } else {

        // Fallback content if homepage design file doesn't exist    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">// Include the appropriate homepage design

        include(__DIR__ . '/homepages/homepage1.php');

    }    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />

    ?>

    <title><?php echo $sitename; ?> - Dashboard</title>$homepageFile = __DIR__ . '/homepages/homepage' . $design . '.php';<head>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>

    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">

</div>

    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">if (!file_exists($homepageFile)) {    <meta charset="utf-8">

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>

<script type="text/javascript" src="../assets/js/custom.js"></script>    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

</body>

</html>    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">    $homepageFile = __DIR__ . '/homepages/homepage1.php'; // Default fallback    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />

    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">

</head>}    <title><?php echo $data3->sitename; ?> - Homepage</title>



<body class="theme-light">?>    <meta name="description" content="<?php echo $data3->sitename; ?> - VTU Services">



<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>



<div id="page"><!DOCTYPE html>    <!-- CSS -->

    <div class="header header-fixed header-auto-show header-logo-app">

        <a href="homepage.php" class="header-title header-subtitle"><?php echo $sitename; ?></a><html lang="en">    <?php include_once("includes/cssFiles.php"); ?>

        <a href="#" data-menu="menu-main" class="header-icon header-icon-1"><i class="fas fa-bars"></i></a>

        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-dark"><i class="fas fa-sun"></i></a>    <meta id="theme-check" name="theme-color" content="<?php echo $sitecolor; ?>">

        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-light"><i class="fas fa-moon"></i></a>

        <a href="#" data-menu="menu-highlights" class="header-icon header-icon-3"><i class="fas fa-brush"></i></a><head>    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">

    </div>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>

    <?php 

    // Set up data variables for the homepage design    <meta name="apple-mobile-web-app-capable" content="yes">

    $data = $homepageData[0]; // User data

    $data2 = isset($homepageData[1]) ? $homepageData[1] : null; // API config    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"><body class="theme-light">

    $data3 = isset($homepageData[2]) ? $homepageData[2] : null; // Site settings

        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />

    // Include the homepage design

    include($homepageFile);     <title><?php echo $sitename; ?> - Dashboard</title>    <div id="preloader">

    ?>

    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">        <div class="spinner-border color-highlight" role="status"></div>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>

    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">    </div>

</div>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>

<script type="text/javascript" src="../assets/js/custom.js"></script>    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">    <div id="page">

</body>

</html>    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">

</head>        <!-- Header -->

        <div class="header header-fixed header-auto-show header-logo-center">

<body class="theme-light">            <a href="#" class="header-title"><?php echo $data3->sitename; ?></a>

            <a href="#" class="header-icon header-icon-1"><i class="fas fa-bars"></i></a>

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>            <a href="profile.php" class="header-icon header-icon-4"><i class="fas fa-user"></i></a>

        </div>

<div id="page">

    <div class="header header-fixed header-auto-show header-logo-app">        <!-- Page Content -->

        <a href="homepage.php" class="header-title header-subtitle"><?php echo $sitename; ?></a>        <div class="page-content header-clear-medium">

        <a href="#" data-menu="menu-main" class="header-icon header-icon-1"><i class="fas fa-bars"></i></a>

        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-dark"><i class="fas fa-sun"></i></a>            <?php echo $msg; ?>

        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-light"><i class="fas fa-moon"></i></a>

        <a href="#" data-menu="menu-highlights" class="header-icon header-icon-3"><i class="fas fa-brush"></i></a>            <!-- Welcome Card -->

    </div>            <div class="card card-style">

                <div class="content">

    <?php                     <h2>Welcome, <?php echo $data2->sFname . ' ' . $data2->sLname; ?>!</h2>

    // Update the data variable to match what homepage designs expect                    <p class="color-theme opacity-80">Wallet Balance: ₦<?php echo number_format($data2->sWallet, 2); ?></p>

    $data = $data[0]; // User data                    <p class="color-theme opacity-60">Account Type: <?php echo ucfirst($data2->sType); ?></p>

    $data2 = isset($data[1]) ? $data[1] : null; // API config                </div>

    $data3 = isset($data[2]) ? $data[2] : null; // Site settings            </div>

    

    // Include the homepage design            <!-- Services Grid -->

    include($homepageFile);             <div class="card card-style">

    ?>                <div class="content">

                    <h3 class="mb-3">Our Services</h3>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>                    <div class="row">



</div>                        <!-- Buy Airtime -->

                        <div class="col-6 mb-3">

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>                            <a href="buyairtime.php" class="card card-style mx-0" style="text-decoration: none;">

<script type="text/javascript" src="../assets/js/custom.js"></script>                                <div class="card-center text-center">

</body>                                    <i class="fas fa-phone fa-3x color-blue-dark mb-3"></i>

</html>                                    <h5 class="color-theme">Buy Airtime</h5>
                                    <p class="color-theme opacity-50 font-12">All Networks</p>
                                </div>
                            </a>
                        </div>

                        <!-- Buy Data -->
                        <div class="col-6 mb-3">
                            <a href="buydata.php" class="card card-style mx-0" style="text-decoration: none;">
                                <div class="card-center text-center">
                                    <i class="fas fa-wifi fa-3x color-green-dark mb-3"></i>
                                    <h5 class="color-theme">Buy Data</h5>
                                    <p class="color-theme opacity-50 font-12">Internet Data</p>
                                </div>
                            </a>
                        </div>

                        <!-- Cable TV -->
                        <div class="col-6 mb-3">
                            <a href="cabletv.php" class="card card-style mx-0" style="text-decoration: none;">
                                <div class="card-center text-center">
                                    <i class="fas fa-tv fa-3x color-orange-dark mb-3"></i>
                                    <h5 class="color-theme">Cable TV</h5>
                                    <p class="color-theme opacity-50 font-12">TV Subscription</p>
                                </div>
                            </a>
                        </div>

                        <!-- Electricity -->
                        <div class="col-6 mb-3">
                            <a href="electricity.php" class="card card-style mx-0" style="text-decoration: none;">
                                <div class="card-center text-center">
                                    <i class="fas fa-bolt fa-3x color-yellow-dark mb-3"></i>
                                    <h5 class="color-theme">Electricity</h5>
                                    <p class="color-theme opacity-50 font-12">Pay Bills</p>
                                </div>
                            </a>
                        </div>

                        <!-- Fund Wallet -->
                        <div class="col-6 mb-3">
                            <a href="fundwallet.php" class="card card-style mx-0" style="text-decoration: none;">
                                <div class="card-center text-center">
                                    <i class="fas fa-wallet fa-3x color-purple-dark mb-3"></i>
                                    <h5 class="color-theme">Fund Wallet</h5>
                                    <p class="color-theme opacity-50 font-12">Add Money</p>
                                </div>
                            </a>
                        </div>

                        <!-- Transactions -->
                        <div class="col-6 mb-3">
                            <a href="transactions.php" class="card card-style mx-0" style="text-decoration: none;">
                                <div class="card-center text-center">
                                    <i class="fas fa-history fa-3x color-red-dark mb-3"></i>
                                    <h5 class="color-theme">Transactions</h5>
                                    <p class="color-theme opacity-50 font-12">History</p>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card card-style">
                <div class="content">
                    <h3 class="mb-3">Quick Actions</h3>
                    <div class="list-group list-custom-small">
                        <a href="profile.php" class="list-group-item">
                            <i class="fas fa-user-edit color-blue-dark"></i>
                            <span>Update Profile</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="referrals.php" class="list-group-item">
                            <i class="fas fa-users color-green-dark"></i>
                            <span>Referrals</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="notifications.php" class="list-group-item">
                            <i class="fas fa-bell color-orange-dark"></i>
                            <span>Notifications</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer card-style">
            <a href="#" class="footer-title"><span class="color-highlight"><?php echo $data3->sitename; ?></span></a>
            <p class="footer-text">Your reliable VTU service provider</p>
            <div class="footer-socials text-center">
                <a href="../logout.php" class="btn btn-s bg-red-dark rounded-sm text-uppercase font-900 mt-2">Logout</a>
            </div>
        </div>

    </div>

    <!-- JavaScript -->
    <?php include_once("includes/jsFiles.php"); ?>
    <?php include_once("includes/topupmatescript.php"); ?>

</body>

</html>
