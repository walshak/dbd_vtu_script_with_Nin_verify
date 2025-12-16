<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadElectricityData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Electricity</title>
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
                <p class="mb-0 text-center font-600 color-highlight">Electricity Payment</p>
                <h1 class="text-center">Electricity Bill</h1>

                <div class="row text-center mb-2">
                    
                    <a href="javascript:selectExamByIcon('Abuja  Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/aedc.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Eko Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/ekedc.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Kaduna Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/kaduna.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Kano Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/kedco.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Jos Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/jos.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Ikeja Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/ikeja.png?v=1" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Ibadan Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/ibedc.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectExamByIcon('Enugu Electric');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/eedc.png" width="45" height="45" />
                        </span>
                    </a>
                    
                </div>
                
                <hr/>

                <form method="post" class="verifyelectricityForm" id="verifyelectricityForm" action="confirm-electricity">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="electricityid" class="color-theme opacity-80 font-700 font-12">Provider</label>
                                <select id="electricityid" name="provider" required>
                                    <option value="" disabled="" selected="">Select Provider</option>
                                    <?php foreach($data[0] AS $provider): if($provider->providerStatus == "On"): ?>
                                        <option value="<?php echo $provider->eId; ?>" providername="<?php echo $provider->provider; ?>"><?php echo $provider->provider; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="metertype" class="color-theme opacity-80 font-700 font-12">Meter Type</label>
                                <select id="metertype" name="metertype" required>
                                    <option value="" disabled="" selected="">Select Type</option>
                                    <option value="prepaid">Prepaid</option>
                                    <option value="postpaid">Postpaid</option>
                                 </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="meternumber" class="color-theme opacity-80 font-700 font-12">Meter Number</label>
                                <input type="number" name="meternumber" placeholder="Meter Number" value="" class="round-small" id="meternumber" required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="meteramount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <input type="text" name="amount" placeholder="Amount" value="" class="round-small" id="meteramount"  required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" readonly required  />
                            </div>

                            <p class="text-danger">
                                <b>Note: </b> Transaction attracts a service charges of N<span id="electricitycharges"><?php echo $data[1]->electricitycharges; ?></span> only.
                                <br/>
                                <b>Note: </b> Minimum Unit Purchase Is N1000.
                            </p>

                            <div class="form-button">
                            <button type="submit" id="electricity-btn" name="verify-meter-no" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Continue
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
function selectExamByIcon(provider) {
    var providerSelect = document.getElementById('electricityid');
    var options = providerSelect.options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].getAttribute('providername') === provider) {
            providerSelect.selectedIndex = i;
            break;
        }
    }
}
</script>
</body>
</html>