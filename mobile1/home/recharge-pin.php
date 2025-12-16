<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadRechargePinData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Recharge Pin</title>
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
                <p class="mb-0 text-center font-600 color-highlight">Recharge Card Pin</p>
                <h1 class="text-center">Recharge Pin</h1>
                
                <form method="post" class="rechargepinForm" id="rechargepinForm" action="recharge-pin">
                        <fieldset>
                                
                            <input name="transkey" id="transkey" type="hidden" />
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="businessname" class="color-theme opacity-80 font-700 font-12">Business Name</label>
                                <input type="text" name="businessname" placeholder="Business Name" value="" class="round-small" id="businessname"  required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networkid" class="color-theme opacity-80 font-700 font-12">Network</label>
                                <select id="networkid" name="network">
                                    <option value="" disabled="" selected="">Select Network</option>
                                    <?php foreach($data[0] AS $network): if($network->networkStatus == "On" && $network->airtimepinStatus == "On"): ?>
                                        <option value="<?php echo $network->networkid; ?>" networkname="<?php echo $network->network; ?>"><?php echo $network->network; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <input type="number" name="amount" placeholder="Amount" value="" class="round-small" id="rechargepinamount"  required  />
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="quantity" class="color-theme opacity-80 font-700 font-12">Quantity</label>
                                <input type="number" id="norechargepin" name="quantity" placeholder="Quantity" value="" class="round-small" required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" placeholder="Amount" value="" class="round-small" id="amounttopay"  required readonly  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="discount" class="color-theme opacity-80 font-700 font-12">Discount</label>
                                <input type="text" name="discount" placeholder="Discount" value="" class="round-small" id="discount" readonly required  />
                            </div>

                            <div class="form-button">
                            <button type="submit" id="rechargepin-btn" name="purchase-recharge-pin" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Purchase Pin
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
<script>
var rechargepindiscounts = <?php echo $data[1]; ?>;
</script>
</body>
</html>