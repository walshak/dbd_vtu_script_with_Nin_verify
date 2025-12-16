<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadCableTvData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Cable TV</title>
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
                <p class="mb-0 text-center font-600 color-highlight">Cable TV Subscription</p>
                <h1 class="text-center">Cable TV</h1>
                <p class="text-danger text-center">You can contact DSTV/GOtv's customers care unit on 01-2703232/08039003788 or the toll free lines: 08149860333, 07080630333, and 09090630333 for assistance. <br/> STARTIMES's customers care unit on (094618888, 014618888)</p>

                <div class="row text-center mb-2">
                    
                    <div  class="col-4 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                        <a href="javascript:selectExamByIcon('DSTV');">
                            <img src="../assets/images/icons/dstv.png" width="60" height="50" />
                        </a>
                        </span>
                    </div>

                    <div  class="col-4 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                        <a href="javascript:selectExamByIcon('GOTV');">
                            <img src="../assets/images/icons/gotv.png" width="70" height="50" />
                        </a>
                        </span>
                    </div>

                    <div  class="col-4 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                        <a href="javascript:selectExamByIcon('STARTIMES');">
                            <img src="../assets/images/icons/startimes.png" width="60" height="50" />
                        </a>
                        </span>
                    </div>
                    
                    
                </div>
                
                <hr/>

                <form method="post" class="verifycableplanForm" id="verifycableplanForm" action="confirm-cable-tv">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="cableid" class="color-theme opacity-80 font-700 font-12">Provider</label>
                                <select id="cableid" name="provider" required>
                                    <option value="" disabled="" selected="">Select Provider</option>
                                    <?php foreach($data[0] AS $provider): if($provider->providerStatus == "On"): ?>
                                        <option value="<?php echo $provider->cId; ?>" providername="<?php echo $provider->provider; ?>"><?php echo $provider->provider; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <input type="hidden" name="cabledetails" id="cabledetails" />

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="cableplan" class="color-theme opacity-80 font-700 font-12">Plan</label>
                                <select id="cableplan" name="cableplan" required></select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" readonly required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="subtype" class="color-theme opacity-80 font-700 font-12">Subscription Type</label>
                                <select id="subtype" name="subtype" required>
                                    <option value="" disabled="" selected="">Select Type</option>
                                    <option value="change">Change</option>
                                    <option value="renew">Renew</option>
                                 </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
 
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="phone" class="color-theme opacity-80 font-700 font-12">Customer Phone Number</label>
                                <input type="number" name="phone" placeholder="Phone Number" value="" class="round-small" id="phone" required  />
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="iucnumber" class="color-theme opacity-80 font-700 font-12">IUC Number</label>
                                <input type="number" name="iucnumber" placeholder="IUC Number" value="" class="round-small" id="iucnumber" required  />
                            </div>

                            <p id="verifyer"></p>

                            <div class="form-button">
                            <button type="submit" id="cable-btn" name="verify-cable-sub" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
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
var cableplans = <?php echo $data[1]; ?>;

function selectExamByIcon(provider) {
    var providerSelect = document.getElementById('cableid');
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