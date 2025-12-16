<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';

// Get confirmation data from session or parameters
$confirmData = $_SESSION['electricity_confirmation'] ?? null;
$customerName = $_SESSION['electricity_customer_name'] ?? 'Customer details not available';

if (!$confirmData) {
    // Redirect back to electricity page if no confirmation data
    header('Location: electricity.php');
    exit;
}

// Convert array to object for template compatibility
$data = (object) $confirmData;
$data2 = $customerName;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Confirm Electricity</title>
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
                <h1 class="text-center">Confirm Electricity Bill</h1>
                
                <form method="post" class="electricityForm" id="electricityForm">
                        <fieldset>

                            <input type="hidden" name="provider" value="<?php echo $data->provider; ?>" />
                            <input type="hidden" name="amount" value="<?php echo $data->amount; ?>" />
                            <input type="hidden" name="meternumber" value="<?php echo $data->meternumber; ?>" />
                            <input type="hidden" name="phone" value="<?php echo $data->phone; ?>" />
                            <input type="hidden" name="metertype" value="<?php echo $data->metertype; ?>" />
                        
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="electricitydetails" class="color-theme opacity-80 font-700 font-12">Payment For</label>
                                <input type="text" id="electricitydetails" value="<?php echo $data->electricitydetails; ?>" class="round-small" disabled />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="metertype" class="color-theme opacity-80 font-700 font-12">Meter Type</label>
                                <input type="text" placeholder="Type" value="<?php echo $data->metertype; ?>" class="round-small" id="metertype" disabled />
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                                <input type="text" placeholder="Phone Number" value="<?php echo $data->phone; ?>" class="round-small" id="phone" disabled />
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="meternumber" class="color-theme opacity-80 font-700 font-12">Meter Number</label>
                                <input type="text" placeholder="Meter Number" value="<?php echo $data->meternumber; ?>" class="round-small" id="meternumber" disabled />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Customer Name</label>
                                <input type="text" placeholder="Customer Name" value="<?php echo $data2; ?>" class="round-small" disabled />
                            </div>

                            <p id="verifyer" class="text-danger"><b><?php echo (strpos($data2,"Could Not") !== false) ? "Note: ".$data2 : ""; ?></b></p>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <input type="text" placeholder="Amount" value="₦<?php echo number_format($data->amount, 2); ?>" class="round-small" id="amount" readonly disabled />
                            </div>

                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />

                            <div class="form-button d-flex">
                                <a href="electricity.php" class="btn btn-secondary btn-l font-600 font-15 me-2 rounded-s" style="width: 48%;">
                                    Back
                                </a>
                                <button type="submit" id="electricity-btn" name="purchase-electricity" style="width: 48%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight rounded-s">
                                    Confirm Payment
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
// Form submission
document.getElementById('electricityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const confirmMsg = `Confirm Electricity bill payment?\nProvider: <?php echo $data->electricitydetails; ?>\nAmount: ₦<?php echo number_format($data->amount, 2); ?>\nMeter: <?php echo $data->meternumber; ?>`;
    
    if (!confirm(confirmMsg)) {
        return;
    }
    
    const btn = document.getElementById('electricity-btn');
    btn.innerHTML = 'Processing...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    formData.append('confirm_purchase', '1');
    
    fetch('../api/electricity/purchase.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Electricity bill payment successful!');
            // Clear confirmation data
            fetch('../api/user/clear-confirmation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'type=electricity'
            });
            window.location.href = 'transactions.php';
        } else {
            alert(data.message || 'Payment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during payment');
    })
    .finally(() => {
        btn.innerHTML = 'Confirm Payment';
        btn.disabled = false;
    });
});
</script>
</body>
</html>