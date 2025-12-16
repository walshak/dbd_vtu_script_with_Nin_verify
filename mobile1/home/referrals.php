<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
list($data, $data2) = loadReferralsData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Referrals</title>
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

        <div class="row mb-0">
            <div class="col-6">
                <div class="card card-style" data-card-height="90" 
                style="height: 110px;  margin-right:0;">
                        <div class="card-top ps-3 pt-3">
                            <h6 class="font-5" style="color:<?php echo $sitecolor; ?>" >Referrals</h6>
                            
                        </div>
                        <div class="card-center pe-3">
                        
                        </div>
                        <div class="card-bottom ps-3 pb-2">
                            <h4><?php echo isset($data->refCount) ? $data->refCount : '0'; ?></h4>
                        </div>
                        
                </div>
            </div>
            <div class="col-6">
                <div class="card card-style" data-card-height="90" 
                style="height: 110px;  margin-left:0;">
                        <div class="card-top ps-3 pt-3">
                            <h6 class="font-5" style="color:<?php echo $sitecolor; ?>">Commission</h6>
                            
                        </div>
                        <div class="card-center pe-3">
                        
                        </div>
                        <div class="card-bottom ps-3 pb-2">
                            <h4>₦<?php echo isset($data->sRefWallet) ? number_format($data->sRefWallet, 2) : '0.00'; ?></h4>
                        </div>
                        
                </div>
            </div>
        </div>

       <div class="card card-style">
            
            <div class="content">
            <div>
                <h5>Referrals Link</h5>
                <hr/>
            </div>
               
               <div>
                    <input type="text" class="form-control" readonly value="<?php echo $siteurl."mobile/register/?referral=".$data->sPhone; ?>" />
                    <button class="btn btn-danger btn-sm mt-2" style="border-radius:5rem;" onclick="copyToClipboard('<?php echo $siteurl."mobile/register/?referral=".$data->sPhone; ?>')">Copy Link</button>
                    <a href="transfer.php" class="btn btn-success btn-sm mt-2" style="border-radius:5rem; margin-left:5px;">Withdraw</a>
                </div>
            </div>
        </div>

       <div class="card card-style">
            
            <div class="content">
                <div>
                    <h5>Commission List</h5>
                    <hr/>
                </div>

                <table class="table table-bordered table-striped">
                    <tr class="bg-blue-dark">
                        <td class="text-white"><b>Service</b></td>
                        <td class="text-white"><b>Bonus</b></td>
                    </tr>
                    <tr>
                        <td><b>Account Upgrade </b></td>
                        <td><b>₦<?php echo isset($data2->referalupgradebonus) ? $data2->referalupgradebonus : '0'; ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Airtime Bonus</b></td>
                        <td><b>₦<?php echo isset($data2->referalairtimebonus) ? $data2->referalairtimebonus : '0'; ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Data Bonus</b></td>
                        <td><b>₦<?php echo isset($data2->referaldatabonus) ? $data2->referaldatabonus : '0'; ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Cable TV Bonus</b></td>
                        <td><b>₦<?php echo isset($data2->referalcablebonus) ? $data2->referalcablebonus : '0'; ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Electricity Bonus</b></td>
                        <td><b>₦<?php echo isset($data2->referalmeterbonus) ? $data2->referalmeterbonus : '0'; ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Exam Pin Bonus</b></td>
                        <td><b>₦<?php echo isset($data2->referalexambonus) ? $data2->referalexambonus : '0'; ?></b></td>
                    </tr>
                   
                </table> 

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
        alert('Referral link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Referral link copied to clipboard!');
        } catch (err) {
            alert('Please manually copy the referral link');
        }
        document.body.removeChild(textArea);
    });
}
</script>
</body>
</html>