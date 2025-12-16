<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadViewPinsData($controller);

// Handle the ref parameter for specific pin viewing
$data = isset($_GET["ref"]) ? $data[0] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - View Data Pins</title>
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
                <div class="d-flex justify-content-between mb-0">
                    <div>
                        <p class="mb-0 font-600 color-highlight">Transaction Details</p>
                        <h1>Data Pin</h1>
                    </div>
                    <div>
                        <?php if(isset($_GET["ref"])): ?>
                        <a href="print-data-pin.php?ref=<?php echo $_GET["ref"]; ?>" class="btn btn-info"><i class="fa fa-print"></i> Print</a>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="mb-0 font-600 text-danger">Click On The Pin To Copy</p>
                
                <div>
                    <?php if(!empty($data)) : $pins=explode(",",$data->tokens); $sn=explode(",",$data->serial); ?>
                    <?php $network=$data->network; $datasize=$data->datasize; $loadpin="*347*383*3*3*PIN#"; if($datasize=="1.5GB"){$loadpin="*460*6*1# Then PIN or Text PIN to 460"; $checkBal="*131*4#";} ?>
                    <?php if($network == "AIRTEL"){$cardColor="#ff1a1a"; $cardLogo="airtel.png"; $textColor="#ffffff"; $checkBal="*140#";} 
                    else {$cardColor="#ffcc00"; $cardLogo="mtn.png"; $textColor="#000000"; $checkBal="*461*4#";} ?>
                    <?php for($i=0; $i<$data->quantity; $i++): ?>
                          
                                <div class="row border" style="margin:3px;">
                                        <div class="col-4" style="margin:0; padding:0; background-color:<?php echo $cardColor; ?>;">
                                            <div class="text-dark" style="padding:10px;">
                                               
                                                <p style="margin-bottom:5px;"><img src="../assets/images/icons/<?php echo $cardLogo; ?>" style="width:50px; height:50px;" /></p>
                                                <h6 style="color:<?php echo $textColor; ?>">DATA PIN</h6>
                                                <h6 style="color:<?php echo $textColor; ?>"><?php echo $datasize; ?></h6>
                                                <p style="margin-bottom:0; color:<?php echo $textColor; ?>"><?php echo $sn[$i]; ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-8 bg-white" style="margin:0; padding:0; ">   
                                            <div class="text-center" style="padding:10px;">
                                                
                                                <h6><?php echo strtoupper($data->business); ?></h6>
                                                <button style="background-color:#f2f2f2; border-radius:3rem; padding:7px; width:100%;" onclick="copyToClipboard('<?php echo trim($pins[$i]); ?>')"><h4><?php echo trim($pins[$i]); ?></h4></button>
                                                <p style="margin-bottom:0;"><b>Load <?php echo $loadpin; ?></b> <b>Bal:   <?php echo $checkBal; ?></b></p>
                                                <p>Powered By: <?php echo $sitename; ?></p>
                                            </div>
                                        </div>
                                </div>
                        
                    <?php endfor; else: ?>
                        <p class="text-center">No data pins found.</p>
                    <?php endif; ?>
                   
                </div>

            </div>

        </div>

    </div>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>

</div>

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Pin copied to clipboard: ' + text);
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
</body>
</html>