<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';

// Get transaction reference from URL
$transRef = $_GET['ref'] ?? '';
$isReceipt = isset($_GET['receipt']);

if (empty($transRef)) {
    header('Location: transactions.php');
    exit;
}

// Load transaction details
$data = loadTransactionDetailsData($controller, $transRef);

if (!$data) {
    header('Location: transactions.php');
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
    <title><?php echo $sitename; ?> - Transaction Details</title>
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
                <div class="text-center"><img src="../assets/images/icons/success.png" style="width:100px; height:100px;" /></div>
                <p class="mb-0 font-600 color-highlight text-center">Transaction Details</p>
                <h1 class="text-center"><?php echo $isReceipt ? 'Receipt' : 'Transaction'; ?></h1>
                
                <table class="table table-bordered table-striped">
                    <tr>
                        <td><b>Transaction No:</b></td>
                        <td><?php echo $data->transref; ?></td>
                    </tr>
                    <tr>
                        <td><b>Service:</b></td>
                        <td><?php echo $data->servicename; ?></td>
                    </tr>
                    <tr>
                        <td><b>Description:</b></td>
                        <td><?php echo $data->servicedesc; ?></td>
                    </tr>
                    <?php if(!$isReceipt): ?>
                    <tr>
                        <td><b>Amount:</b></td>
                        <td>₦<?php echo number_format($data->amount, 2); ?></td>
                    </tr>
                    <tr>
                        <td><b>Old Balance:</b></td>
                        <td>₦<?php echo number_format($data->oldbal, 2); ?></td>
                    </tr>
                     <tr>
                        <td><b>New Balance:</b></td>
                        <td>₦<?php echo number_format($data->newbal, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><b>Status:</b></td>
                        <td>
                            <?php 
                            $status = $data->status;
                            $statusClass = '';
                            $statusText = '';
                            
                            switch($status) {
                                case 'success':
                                case 'completed':
                                case '1':
                                    $statusClass = 'text-success';
                                    $statusText = 'Successful';
                                    break;
                                case 'pending':
                                case '0':
                                    $statusClass = 'text-warning';
                                    $statusText = 'Pending';
                                    break;
                                case 'failed':
                                case 'error':
                                case '2':
                                    $statusClass = 'text-danger';
                                    $statusText = 'Failed';
                                    break;
                                default:
                                    $statusClass = 'text-muted';
                                    $statusText = ucfirst($status);
                            }
                            ?>
                            <span class="<?php echo $statusClass; ?>"><b><?php echo $statusText; ?></b></span>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Date:</b></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($data->date)); ?></td>
                    </tr>
                </table> 
                
                <?php if (!$isReceipt): ?>
                <div class="d-flex gap-2">
                    <a href="transaction-details.php?receipt&ref=<?php echo $_GET["ref"]; ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-receipt me-1"></i><b>View Receipt</b>
                    </a>
                    <a href="transactions.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-list me-1"></i><b>All Transactions</b>
                    </a>
                </div>
                <?php else: ?>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-info btn-sm">
                        <i class="fas fa-print me-1"></i><b>Print Receipt</b>
                    </button>
                    <a href="transaction-details.php?ref=<?php echo $_GET["ref"]; ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i><b>Back to Details</b>
                    </a>
                    <a href="transactions.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-list me-1"></i><b>All Transactions</b>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <?php if (!$isReceipt): ?>
    <?php include_once(__DIR__ . '/includes/menu.php'); ?>
    <?php endif; ?>

</div>

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>

<?php if ($isReceipt): ?>
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
    }
    
    body {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}

.receipt-header {
    text-align: center;
    margin-bottom: 20px;
}

.receipt-header h2 {
    color: <?php echo $sitecolor; ?>;
    margin-bottom: 5px;
}

.receipt-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    color: #666;
}
</style>

<script>
// Auto-focus print dialog for receipts
window.addEventListener('load', function() {
    if (window.location.href.includes('receipt')) {
        // Small delay to ensure page is fully loaded
        setTimeout(function() {
            if (confirm('Print this receipt?')) {
                window.print();
            }
        }, 1000);
    }
});
</script>
<?php endif; ?>

</body>
</html>