<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadAlphaTopupData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Alpha Topup</title>
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
				<div class="text-center mb-3"><img src="../assets/images/alpha.png" width="200" /></div>
                <form method="post" class="alphaplanForm" id="alphaplanForm" action="alpha-topup">
                        <fieldset>

                           <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="alphaplan" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <select id="alphaplan" name="alphaplan">
                                    <option value="">Select Amount</option>
                                    <?php if(!empty($data[0])): foreach($data[0] AS $plan): ?>
                                    <option value="<?php echo $plan->buyingPrice; ?>" plan="<?php echo $plan->buyingPrice; ?>" user="<?php echo $plan->sellingPrice; ?>" agent="<?php echo $plan->agent; ?>"  vendor="<?php echo $plan->vendor; ?>"><?php echo $plan->buyingPrice; ?></option>
                                    <?php endforeach; endif; ?>
                                    
                                 </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                                <input type="number" name="phone" placeholder="Phone Number" value="" class="round-small" id="phone" required  />
                            </div>

                            <p id="verifyer"></p>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" name="amounttopay" placeholder="Amount To Pay" 
									   value="" class="round-small" id="amounttopay" readonly required  />
                            </div>

                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />

                            <div class="form-button">
                            <button type="submit" id="alpha-plan-btn" name="purchase-alpha-topup" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Send Order
                            </button>
                            </div>
                        </fieldset>
                    </form>        
            </div>

        </div>

    </div>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>

</div>

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>
</body>
</html>