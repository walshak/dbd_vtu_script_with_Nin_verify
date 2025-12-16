<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';

// Get pin reference from URL
$pinRef = $_GET['ref'] ?? '';

if (empty($pinRef)) {
    header('Location: view-recharge-pins.php');
    exit;
}

// Load recharge pin details
if (method_exists($controller, 'getRechargePinByRef')) {
    $data = $controller->getRechargePinByRef($pinRef);
} else {
    // Fallback - could be loaded from session or database
    $data = $_SESSION['recharge_pin_data_' . $pinRef] ?? null;
}

if (!$data) {
    header('Location: view-recharge-pins.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Print Recharge Pins</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">
    
    <style>
    @media print {
        .header, .menu, .btn, .no-print {
            display: none !important;
        }
        
        .page-content {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        .card {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
        }
        
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .pin-card {
            page-break-inside: avoid;
            margin-bottom: 20px !important;
        }
    }
    
    .pin-card {
        border: 2px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .pin-logo-section {
        padding: 15px;
        text-align: center;
    }
    
    .pin-details-section {
        padding: 15px;
        background: #f8f9fa;
    }
    
    .pin-code {
        background: #e9ecef;
        border-radius: 25px;
        padding: 10px;
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 16px;
        text-align: center;
        margin: 10px 0;
    }
    </style>
</head>

<body class="theme-light">

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

<div id="page">
    <div class="header header-fixed header-auto-show header-logo-app no-print">
        <a href="homepage.php" class="header-title header-subtitle"><?php echo $sitename; ?></a>
        <a href="#" data-menu="menu-main" class="header-icon header-icon-1"><i class="fas fa-bars"></i></a>
        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-dark"><i class="fas fa-sun"></i></a>
        <a href="#" data-toggle-theme class="header-icon header-icon-2 show-on-theme-light"><i class="fas fa-moon"></i></a>
        <a href="#" data-menu="menu-highlights" class="header-icon header-icon-3"><i class="fas fa-brush"></i></a>
    </div>

    <div class="page-content header-clear-medium">
        
        <div class="card card-style no-print">
            <div class="content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Recharge Pin Cards</h4>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <a href="view-recharge-pins.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="">
            <div class="content">
                <div class="row">
                    <?php if(!empty($data)) : 
                        $pins = is_string($data->tokens) ? explode(",", $data->tokens) : [$data->tokens];
                        $sn = is_string($data->serial) ? explode(",", $data->serial) : [$data->serial];
                        $network = $data->network ?? 'MTN';
                        $amount = $data->amount ?? '100';
                        $quantity = $data->quantity ?? count($pins);
                        $business = $data->business ?? $sitename;
                        
                        // Set network-specific settings
                        $loadpin = "*555*PIN#";
                        $checkBal = "*556#";
                        
                        if($network == "AIRTEL") {
                            $cardColor = "#ff1a1a";
                            $cardLogo = "airtel.png";
                            $textColor = "#ffffff";
                            $loadpin = "*126*PIN#";
                            $checkBal = "*123#";
                        } elseif($network == "GLO") {
                            $cardColor = "#00b04f";
                            $cardLogo = "glo.png";
                            $textColor = "#ffffff";
                            $loadpin = "*123*PIN#";
                            $checkBal = "*124#";
                        } elseif($network == "9MOBILE") {
                            $cardColor = "#00a651";
                            $cardLogo = "9mobile.png";
                            $textColor = "#ffffff";
                            $loadpin = "*222*PIN#";
                            $checkBal = "*232#";
                        } else {
                            $cardColor = "#ffcc00";
                            $cardLogo = "mtn.png";
                            $textColor = "#000000";
                            $loadpin = "*555*PIN#";
                            $checkBal = "*556#";
                        }
                        
                        for($i = 0; $i < $quantity; $i++): 
                    ?>
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="pin-card">
                            <div class="row g-0">
                                <div class="col-4 pin-logo-section" style="background-color:<?php echo $cardColor; ?>;">
                                    <div style="color:<?php echo $textColor; ?>;">
                                        <img src="../assets/images/icons/<?php echo $cardLogo; ?>" style="width:50px; height:50px;" class="mb-2" />
                                        <h6 style="color:<?php echo $textColor; ?>; margin-bottom: 5px;">RECHARGE PIN</h6>
                                        <h6 style="color:<?php echo $textColor; ?>; margin-bottom: 5px;">₦<?php echo $amount; ?></h6>
                                        <small style="color:<?php echo $textColor; ?>;"><?php echo isset($sn[$i]) ? $sn[$i] : 'SN: ' . ($i + 1); ?></small>
                                    </div>
                                </div>
                                
                                <div class="col-8 pin-details-section">   
                                    <div class="text-center">
                                        <h6 class="mb-2"><?php echo strtoupper($business); ?></h6>
                                        <div class="pin-code">
                                            <?php echo isset($pins[$i]) ? $pins[$i] : 'PIN-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT); ?>
                                        </div>
                                        <small class="text-muted">
                                            <b>Load: <?php echo $loadpin; ?></b><br>
                                            <b>Check Bal: <?php echo $checkBal; ?></b>
                                        </small>
                                        <p class="mb-0 mt-2"><small>Powered By: <?php echo $sitename; ?></small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                    <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <h5>No Pin Data Available</h5>
                            <p>Unable to load recharge pin information. Please try again.</p>
                            <a href="view-recharge-pins.php" class="btn btn-primary">Go Back</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <div class="no-print">
        <?php include_once(__DIR__ . '/includes/menu.php'); ?>
    </div>

</div>

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>

<script>
// Auto-focus print dialog
window.addEventListener('load', function() {
    // Small delay to ensure page is fully loaded
    setTimeout(function() {
        if (confirm('Print these recharge pin cards?')) {
            window.print();
        }
    }, 1000);
});
</script>

</body>
</html>