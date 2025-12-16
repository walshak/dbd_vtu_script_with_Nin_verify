<?php
require_once 'includes/common_init.php';
require_once 'includes/data_loaders.php';

// Get transaction reference from URL
$ref = isset($_GET['ref']) ? $_GET['ref'] : '';

// Load transaction details
$data = loadTransactionDetailsData($controller, $ref);
$sitename = isset($siteSettings['siteName']) ? $siteSettings['siteName'] : 'VTU Platform';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recharge Pin Details - <?php echo $siteName; ?></title>
    
    <!-- Mobile App Styling -->
    <link rel="stylesheet" type="text/css" href="../assets/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="../assets/app.json">
    <meta name="theme-color" content="<?php echo $sitecolor; ?>">
    <link rel="apple-touch-icon" href="../assets/images/logo.png">
    
    <style>
        .pin-card {
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .pin-header {
            padding: 10px;
            font-size: 12px;
        }
        .pin-content {
            padding: 10px;
            background: white;
            text-align: center;
        }
        .pin-button {
            background-color: #f2f2f2;
            border-radius: 3rem;
            padding: 8px;
            width: 100%;
            border: none;
            cursor: pointer;
        }
        .pin-button:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header header-fixed header-logo-center">
        <a href="dashboard" class="header-title"><?php echo $siteName; ?></a>
        <a href="dashboard" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
        <a href="account" class="header-icon header-icon-4"><i class="fas fa-user"></i></a>
    </div>

    <!-- Main Content -->
    <div class="page-content header-clear-medium">
        <div class="card card-style">
            <div class="content">
                <div class="d-flex justify-content-between mb-0">
                    <div>
                        <p class="mb-0 font-600 color-highlight">Transaction Details</p>
                        <h1>Recharge Pin</h1>
                    </div>
                    <div>
                        <a href="print-recharge-pin?ref=<?php echo htmlspecialchars($ref); ?>" class="btn btn-info">
                            <i class="fa fa-print"></i> Print
                        </a>
                    </div>
                </div>
                <p class="mb-0 font-600 text-danger">Click On The Pin To Copy</p>
                
                <div>
                    <?php
                    // Sample data for demonstration - replace with actual data loading
                    if (empty($data)) {
                        $data = (object) [
                            'tokens' => '1233455555555554,56722342234444443338,902233344444444433333333312',
                            'serial' => 'SN121233345555534,S4444444444444N5678,44433333444444SN9012',
                            'network' => 'MTN',
                            'datasize' => '100',
                            'quantity' => 3,
                        ];
                    }
                    ?>

                    <?php if (!empty($data) && isset($data->tokens)) : 
                        $pins = explode(",", $data->tokens);
                        $sn = explode(",", $data->serial);
                        $network = $data->network;
                        $datasize = $data->datasize;
                        $loadpin = "₦$data->datasize";
                        
                        if ($datasize == "1.5GB") {
                            $loadpin = "*460*6*1# Then PIN or Text PIN to 460";
                            $checkBal = "*131*4#";
                        }
                        
                        // Network-specific styling
                        if ($network == "AIRTEL") {
                            $cardColor = "#ff1a1a";
                            $cardLogo = "airtel.png";
                            $textColor = "#ffffff";
                            $checkBal = "*140#";
                        } elseif ($network == "GLO") {
                            $cardColor = "#ffffff";
                            $cardLogo = "glo.png";
                            $textColor = "#ffffff";
                            $checkBal = "*127*0#";
                        } elseif ($network == "9MOBILE") {
                            $cardColor = "#4caf50";
                            $cardLogo = "9mobile.png";
                            $textColor = "#ffffff";
                            $checkBal = "*232#";
                        } else {
                            $cardColor = "#ffcc00";
                            $cardLogo = "mtn.png";
                            $textColor = "#000000";
                            $checkBal = "*310#";
                        }
                    ?>

                    <?php for ($i = 0; $i < min($data->quantity, count($pins)); $i++) : ?>
                        <div class="pin-card">
                            <div class="row" style="margin: 0;">
                                <div class="col-4 pin-header" style="background-color:<?php echo $cardColor; ?>; color:<?php echo $textColor; ?>;">
                                    <p style="margin-bottom: 5px;">
                                        <img src="../../assets/images/icons/<?php echo $cardLogo; ?>" style="width: 30px; height: 30px;" alt="<?php echo $network; ?>" />
                                    </p>
                                    <h6 style="color:<?php echo $textColor; ?>; font-size: 14px;">RECHARGE PIN</h6>
                                    <h6 style="color:<?php echo $textColor; ?>; font-size: 14px;"><?php echo htmlspecialchars($datasize); ?></h6>
                                    <p style="margin-bottom: 0; color:<?php echo $textColor; ?>; font-size: 12px;">
                                        <?php echo htmlspecialchars($sn[$i] ?? 'SN' . ($i + 1)); ?>
                                    </p>
                                </div>
                                <div class="col-8 pin-content">
                                    <h6 style="font-size: 16px;"><?php echo strtoupper($network); ?></h6>
                                    <button class="pin-button" onclick="copyToClipboard('<?php echo trim($pins[$i]); ?>')">
                                        <h4 style="font-size: 16px; margin: 0;"><?php echo htmlspecialchars(trim($pins[$i])); ?></h4>
                                    </button>
                                    <p style="margin-bottom:0; font-size: 12px;">
                                        <b>Load <?php echo $loadpin; ?></b> <b>Bal: <?php echo $checkBal; ?></b>
                                    </p>
                                    <p style="font-size: 12px;">Powered By: <?php echo htmlspecialchars($sitename); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                    
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h4>No Pin Data Found</h4>
                            <p>The transaction reference provided does not contain valid pin data.</p>
                            <a href="dashboard" class="btn btn-primary">Back to Dashboard</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Menu -->
    <div class="footer-menu footer-menu-style-3">
        <a href="dashboard"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="fund-wallet"><i class="fas fa-wallet"></i><span>Wallet</span></a>
        <a href="transactions"><i class="fas fa-history"></i><span>History</span></a>
        <a href="account"><i class="fas fa-user"></i><span>Account</span></a>
    </div>

    <!-- Scripts -->
    <script src="../assets/scripts/jquery.js"></script>
    <script src="../assets/scripts/bootstrap.min.js"></script>
    <script src="../assets/scripts/custom.js"></script>
    
    <script>
    function copyToClipboard(text) {
        // Create a temporary textarea
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        
        // Select and copy the text
        textarea.select();
        textarea.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            
            // Show success message
            const Toast = {
                show: function(message) {
                    const toast = document.createElement('div');
                    toast.className = 'toast-notification';
                    toast.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #28a745;
                        color: white;
                        padding: 12px 20px;
                        border-radius: 4px;
                        z-index: 9999;
                        font-size: 14px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                    `;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 3000);
                }
            };
            
            Toast.show('PIN copied to clipboard!');
        } catch (err) {
            alert('PIN: ' + text);
        }
        
        // Remove the temporary textarea
        document.body.removeChild(textarea);
    }
    </script>
</body>
</html>