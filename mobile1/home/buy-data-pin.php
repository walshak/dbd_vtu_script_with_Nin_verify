<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadDataPinData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Buy Data Pin</title>
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
                <p class="mb-0 text-center font-600 color-highlight">Data For All Network</p>
                <h1 class="text-center">Buy Data Pin</h1>

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
                <hr/>
                <div class="d-flex">
                    <h5 style="background:<?php echo $sitecolor; ?>; color:#ffffff; padding:9px;  margin-right:5px;">Code: </h5>
                    <marquee direction="left" scrollamount="5" style="background:#f2f2f2; padding:3px; border-radius:5rem;">
                        <h5 class="py-2">
                        [MTN] - *460*6*1# [Balance] *131*4# [MTN SME] - *347*383*3*3*PIN# [Balance] 461*4# - [AIRTEL] - *347*383*3*3*PIN# [Balance] *140#
                        </h5>
                    </marquee>
                </div>
                <hr/>
                
                <form method="post" class="datapinForm" id="datapinForm" action="buy-data-pin">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networkid" class="color-theme opacity-80 font-700 font-12">Network</label>
                                <select id="datanetworkid" name="network">
                                    <option value="" disabled="" selected="">Select Network</option>
                                    <?php foreach($data[0] AS $network): if($network->datapinStatus == "On"): ?>
                                        <option value="<?php echo $network->nId; ?>" networkname="<?php echo $network->network; ?>" sme="<?php echo $network->smeStatus; ?>" gifting="<?php echo $network->giftingStatus; ?>" corporate="<?php echo $network->corporateStatus; ?>"><?php echo $network->network; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
 
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="datapingroup" class="color-theme opacity-80 font-700 font-12">Data Type</label>
                                <select id="datapingroup" name="datagroup">
                                    <option value="">Select Type</option>
                                    <option value="SME">SME</option>
                                    <option value="Gifting">Gifting</option>
                                    <option value="Corporate">Corporate</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="datapinplan" class="color-theme opacity-80 font-700 font-12">Data Plan</label>
                                <select id="datapinplan" name="datapinplan" required></select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div> 

                            <p id="verifyer"></p>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="quantity" class="color-theme opacity-80 font-700 font-12">Quantity</label>
                                <input type="number" name="quantity" placeholder="Quantity" class="round-small" id="datapinquantity" required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="hidden" name="amount" id="amount"  />
                                <input type="text" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" readonly required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="businessname" class="color-theme opacity-80 font-700 font-12">Business Name</label>
                                <input type="text" name="businessname" placeholder="Business Name"  class="round-small" id="businessname" required  />
                            </div>

                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />

                            <div class="form-button">
                            <button type="submit" id="datapin-btn" name="purchase-datapin" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Generate Pin
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
var datapinplans = <?php echo $data[1]; ?>;

function selectNetworkByIcon(network) {
    var networkSelect = document.getElementById('datanetworkid');
    var options = networkSelect.options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].getAttribute('networkname') === network) {
            networkSelect.selectedIndex = i;
            break;
        }
    }
}
</script>
</body>
</html>