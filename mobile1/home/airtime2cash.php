<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page (using general data loader since this is an informational/contact page)
require_once __DIR__ . '/includes/data_loaders.php';
loadGeneralPageData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Airtime To Cash</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">
</head>

<body class="theme-light">

    <div id="preloader">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>

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

                    <p class="mb-0 text-center font-600 color-highlight">Airtime For All Network</p>
                    <h1 class="text-center">Convert Airtime 2 Cash</h1>

                    <div class="row text-center mb-2">

                        <a href="javascript:selectNetworkByIcon('MTN');" class="col-3 mt-2">
                            <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                                <img src="../assets/images/icons/mtn.png" width="45" height="45" />
                            </span>
                        </a>

                        <a href="javascript:selectNetworkByIcon('AIRTEL');" class="col-3 mt-2">
                            <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                                <img src="../assets/images/icons/airtel.png" width="45" height="45" />
                            </span>
                        </a>

                        <a href="javascript:selectNetworkByIcon('GLO');" class="col-3 mt-2">
                            <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                                <img src="../assets/images/icons/glo.png" width="45" height="45" />
                            </span>
                        </a>

                        <a href="javascript:selectNetworkByIcon('9MOBILE');" class="col-3 mt-2">
                            <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                                <img src="../assets/images/icons/9mobile.png" width="45" height="45" />
                            </span>
                        </a>


                    </div>
                    <hr />
                    <div class="d-flex">
                        <h5 style="background:<?php echo $sitecolor; ?>; color:#ffffff; padding:9px;  margin-right:5px;">Info: </h5>
                        <marquee direction="left" scrollamount="5" style="background:#f2f2f2; padding:3px; border-radius:5rem;">
                            <h5 class="py-2">
                                Contact Admin 2 Convert Airtime 2 Cash. Click on the WhatsApp Icon, contact Admin and Convert Airtime 2 Cash
                            </h5>
                        </marquee>
                    </div>
                    <hr />

                    <form method="post" class="airtimeForm" id="airtimeForm" action="buy-airtime">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networktype" class="color-theme opacity-80 font-700 font-12">Network</label>
                                <select id="networktype" name="networktype">
                                    <option value="MTN">MTN</option>
                                    <option value="GLO">GLO</option>
                                    <option value="AIRTEL">AIRTEL</option>
                                    <option value="9MOBILE">9MOBILE</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="paymentmethod" class="color-theme opacity-80 font-700 font-12">Choose Payment Method</label>
                                <select id="paymentmethod" name="paymentmethod">
                                    <option value="Pay To Bank Account">Pay To Bank Account</option>
                                    <option value="Fund User Wallet">Fund User Wallet</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                                <input type="number" name="phone" placeholder="Phone Number" value="" class="round-small" id="phone" required />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <input type="number" name="amount" placeholder="Enter Amount" value="" class="round-small" id="amount" required />
                            </div>

                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />

                            <div class="row pt-3 mb-3">
                                <div class="col-12 text-center font-15 mt-2">
                                    <a class="text-dark" href="https://wa.me/message/ODTG4C4AA5R6E1"><b>CONVERT AIRTIME 2 CASH</b></a>
                                </div>
                            </div>
                    </form>
                </div>

            </div>

        </div>

        <?php include_once(__DIR__ . '/includes/menu.php'); ?>

    </div>

    <script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/custom.js"></script>
    <script>
        function selectNetworkByIcon(network) {
            document.getElementById('networktype').value = network;
        }
    </script>
</body>

</html>