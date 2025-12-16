<?php
require_once 'includes/common_init.php';
require_once 'includes/data_loaders.php';

// Get transaction reference from URL or POST data
$ref = isset($_GET['ref']) ? $_GET['ref'] : (isset($_POST['ref']) ? $_POST['ref'] : '');

// Load transaction details for receipt generation
$transactionData = loadTransactionDetailsData($controller, $ref);
$sitename = isset($siteSettings['siteName']) ? $siteSettings['siteName'] : 'VTU Platform';
$siteUrl = isset($siteSettings['siteUrl']) ? $siteSettings['siteUrl'] : '';

// Check if we need to generate PDF
$generatePDF = isset($_GET['pdf']) && $_GET['pdf'] == '1';

if ($generatePDF) {
    // PDF generation using basic HTML to PDF conversion
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="receipt_' . $ref . '.pdf"');
    
    // Simple PDF-like output using HTML
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Receipt - ' . htmlspecialchars($ref) . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .receipt { max-width: 600px; margin: 0 auto; }
            .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .details { margin: 20px 0; }
            .row { display: flex; justify-content: space-between; margin: 5px 0; }
            .label { font-weight: bold; }
            @media print { body { margin: 0; } }
        </style>
    </head>
    <body>
        <div class="receipt">
            <div class="header">
                <h2>' . htmlspecialchars($sitename) . '</h2>
                <p>Transaction Receipt</p>
            </div>
            <div class="details">
                <div class="row"><span class="label">Reference:</span> <span>' . htmlspecialchars($ref) . '</span></div>
                <div class="row"><span class="label">Date:</span> <span>' . date('Y-m-d H:i:s') . '</span></div>
                <div class="row"><span class="label">Status:</span> <span>Completed</span></div>
            </div>
        </div>
    </body>
    </html>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Receipt - <?php echo $siteName; ?></title>
    
    <!-- Mobile App Styling -->
    <link rel="stylesheet" type="text/css" href="../assets/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="../assets/app.json">
    <meta name="theme-color" content="<?php echo $sitecolor; ?>">
    <link rel="apple-touch-icon" href="../assets/images/logo.png">
    
    <style>
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .receipt-details {
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .detail-label {
            font-weight: bold;
            color: #333;
        }
        .detail-value {
            color: #666;
        }
        @media print {
            body { margin: 0; background: white; }
            .header, .footer-menu { display: none; }
            .page-content { margin: 0; }
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
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="mb-0 font-600 color-highlight">Receipt Generator</p>
                        <h1>Transaction Receipt</h1>
                    </div>
                    <div>
                        <button onclick="printReceipt()" class="btn btn-info me-2">
                            <i class="fa fa-print"></i> Print
                        </button>
                        <a href="generate-receipt?ref=<?php echo htmlspecialchars($ref); ?>&pdf=1" class="btn btn-success">
                            <i class="fa fa-download"></i> PDF
                        </a>
                    </div>
                </div>

                <div class="receipt-container" id="receiptContainer">
                    <div class="receipt-header">
                        <h2><?php echo htmlspecialchars($sitename); ?></h2>
                        <p style="margin: 0; color: #666;">Transaction Receipt</p>
                        <?php if (!empty($siteUrl)): ?>
                            <p style="margin: 5px 0; font-size: 12px;"><?php echo htmlspecialchars($siteUrl); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="receipt-details">
                        <?php if (!empty($ref)): ?>
                            <div class="detail-row">
                                <span class="detail-label">Reference Number:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($ref); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="detail-row">
                            <span class="detail-label">Date & Time:</span>
                            <span class="detail-value"><?php echo date('Y-m-d H:i:s'); ?></span>
                        </div>

                        <?php if (!empty($transactionData)): ?>
                            <?php if (isset($transactionData->type)): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Transaction Type:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($transactionData->type); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($transactionData->amount)): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Amount:</span>
                                    <span class="detail-value">₦<?php echo number_format($transactionData->amount, 2); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($transactionData->status)): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Status:</span>
                                    <span class="detail-value" style="color: <?php echo $transactionData->status == 'Successful' ? 'green' : 'red'; ?>;">
                                        <?php echo htmlspecialchars($transactionData->status); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($transactionData->recipient)): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Recipient:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($transactionData->recipient); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($transactionData->network)): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Network:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($transactionData->network); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="detail-row">
                                <span class="detail-label">Transaction Status:</span>
                                <span class="detail-value">Processing</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Note:</span>
                                <span class="detail-value">Transaction details will be updated once processing is complete.</span>
                            </div>
                        <?php endif; ?>

                        <div class="detail-row" style="border-top: 2px solid #333; margin-top: 20px; padding-top: 15px;">
                            <span class="detail-label">Customer Service:</span>
                            <span class="detail-value">support@<?php echo str_replace(['http://', 'https://'], '', $siteUrl); ?></span>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                        <p>Thank you for using our service!</p>
                        <p>Keep this receipt for your records.</p>
                    </div>
                </div>

                <?php if (empty($ref)): ?>
                    <div class="mt-4">
                        <h4>Generate Receipt</h4>
                        <form method="GET" action="generate-receipt">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="ref" class="color-theme opacity-80 font-700 font-12">Transaction Reference</label>
                                <input type="text" name="ref" placeholder="Enter transaction reference" class="round-small" id="ref" required />
                            </div>
                            <div class="form-button">
                                <button type="submit" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                    Generate Receipt
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
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
    function printReceipt() {
        // Hide elements that shouldn't be printed
        const header = document.querySelector('.header');
        const footer = document.querySelector('.footer-menu');
        const printButton = document.querySelector('.btn');
        
        if (header) header.style.display = 'none';
        if (footer) footer.style.display = 'none';
        
        // Print the page
        window.print();
        
        // Restore hidden elements after printing
        setTimeout(() => {
            if (header) header.style.display = '';
            if (footer) footer.style.display = '';
        }, 1000);
    }

    // Auto-print if print parameter is present
    <?php if (isset($_GET['print']) && $_GET['print'] == '1'): ?>
    window.onload = function() {
        printReceipt();
    };
    <?php endif; ?>
    </script>
</body>
</html>