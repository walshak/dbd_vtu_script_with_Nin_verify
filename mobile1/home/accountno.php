<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
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
    <title><?php echo $sitename; ?> - Account Details</title>
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
                <p class="mb-0 font-600 color-highlight text-center">Banking Information</p>
                <h1 class="text-center">Account Details</h1>
                
                <div class="list-group list-custom-small">
                    <div class="list-item">
                        <i class="fa fa-university color-blue-dark rounded-xl shadow-xl"></i>
                        <div>
                            <strong>Virtual Bank Accounts</strong>
                            <p class="text-muted">Automated funding available</p>
                        </div>
                    </div>
                    
                    <?php if(isset($data->sFidelityBank) && !empty($data->sFidelityBank)): ?>
                    <div class="list-item">
                        <i class="fa fa-credit-card color-green-dark rounded-xl shadow-xl"></i>
                        <div>
                            <strong>Fidelity Bank</strong>
                            <p><?php echo $data->sFidelityBank; ?></p>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo $data->sFidelityBank; ?>')">
                                Copy Account
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($data->sRolexBank) && !empty($data->sRolexBank)): ?>
                    <div class="list-item">
                        <i class="fa fa-credit-card color-orange-dark rounded-xl shadow-xl"></i>
                        <div>
                            <strong>Moniepoint Bank</strong>
                            <p><?php echo $data->sRolexBank; ?></p>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo $data->sRolexBank; ?>')">
                                Copy Account
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($data->sBankNo) && !empty($data->sBankNo)): ?>
                    <div class="list-item">
                        <i class="fa fa-credit-card color-purple-dark rounded-xl shadow-xl"></i>
                        <div>
                            <strong>Wema Bank</strong>
                            <p><?php echo $data->sBankNo; ?></p>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo $data->sBankNo; ?>')">
                                Copy Account
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($data->sSterlingBank) && !empty($data->sSterlingBank)): ?>
                    <div class="list-item">
                        <i class="fa fa-credit-card color-red-dark rounded-xl shadow-xl"></i>
                        <div>
                            <strong>Sterling Bank</strong>
                            <p><?php echo $data->sSterlingBank; ?></p>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo $data->sSterlingBank; ?>')">
                                Copy Account
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6><i class="fas fa-info-circle me-2"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>All accounts are linked to your profile: <strong><?php echo $data->sFname . ' ' . $data->sLname; ?></strong></li>
                        <li>Transfers to these accounts are automatically credited</li>
                        <li>Processing time: Instant to 10 minutes</li>
                        <li>Additional charges may apply for bank transfers</li>
                    </ul>
                </div>
                
                <div class="text-center mt-4">
                    <a href="fund-wallet.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Fund Wallet
                    </a>
                    <a href="transactions.php" class="btn btn-secondary ms-2">
                        <i class="fas fa-history me-1"></i>Transaction History
                    </a>
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
        alert('Account number copied to clipboard!');
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
            alert('Account number copied to clipboard!');
        } catch (err) {
            alert('Please manually copy the account number');
        }
        document.body.removeChild(textArea);
    });
}
</script>
</body>
</html>