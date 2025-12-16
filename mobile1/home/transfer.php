<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadTransferData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Transfer</title>
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
                <p class="mb-0 text-center font-600 color-highlight">Transfer Funds</p>
                <h1 class="text-center">Transfer</h1>
                
                <form method="post" class="transferForm" id="transferForm">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="transfertype" class="color-theme opacity-80 font-700 font-12">Transfer Type</label>
                                <select id="transfertype" name="transfertype" required>
                                    <option value="referral-wallet" selected>Referral To Wallet</option>
                                    <option value="wallet-wallet">Wallet To Wallet</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                                
                            <input name="transkey" id="transkey" type="hidden" value="<?php echo $transRef; ?>" />
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4" id="walletreceiver">
                                <label for="email" class="color-theme opacity-80 font-700 font-12">Receiver Email</label>
                                <input type="email" name="email" placeholder="Email"  id="walletreceiverinput" class="round-small" required />
                            </div>
                            
                           
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <input type="number" name="amount" placeholder="Amount" value="" class="round-small" id="wallettransferamount" required onkeyup="calculateTransferCharges()" />
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" required readonly />
                            </div>

                            <div class="d-none" id="wallettowalletcharges"><?php echo isset($data->wallettowalletcharges) ? $data->wallettowalletcharges : '50'; ?></div>
                            <p class="text-danger"><b>Note: </b> Wallet To Wallet Fund Transfer Attracts A Charges Of ₦<?php echo isset($data->wallettowalletcharges) ? $data->wallettowalletcharges : '50'; ?> Only.</p>
                          
                            <div class="form-button">
                            <button type="submit" id="transfer-btn" name="perform-transfer" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
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
function calculateTransferCharges() {
    const amount = parseFloat(document.getElementById('wallettransferamount').value) || 0;
    const transferType = document.getElementById('transfertype').value;
    const charges = parseFloat(document.getElementById('wallettowalletcharges').textContent) || 50;
    
    let amountToPay = amount;
    
    if (transferType === 'wallet-wallet' && amount > 0) {
        amountToPay = amount + charges;
    }
    
    document.getElementById('amounttopay').value = amountToPay.toFixed(2);
}

// Handle transfer type change
document.getElementById('transfertype').addEventListener('change', function() {
    calculateTransferCharges();
});

// Handle form submission
document.getElementById('transferForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const transferType = document.getElementById('transfertype').value;
    const email = document.getElementById('walletreceiverinput').value;
    const amount = parseFloat(document.getElementById('wallettransferamount').value);
    const amountToPay = parseFloat(document.getElementById('amounttopay').value);
    
    if (!email || !amount || amount <= 0) {
        alert('Please fill all fields with valid values');
        return;
    }
    
    if (amount < 100) {
        alert('Minimum transfer amount is ₦100');
        return;
    }
    
    // Confirm transfer
    const confirmMsg = `Transfer ₦${amount.toFixed(2)} to ${email}?\nTotal amount to pay: ₦${amountToPay.toFixed(2)}`;
    if (!confirm(confirmMsg)) {
        return;
    }
    
    const btn = document.getElementById('transfer-btn');
    btn.innerHTML = 'Processing...';
    btn.disabled = true;
    
    const formData = new FormData();
    formData.append('transfertype', transferType);
    formData.append('email', email);
    formData.append('amount', amount);
    formData.append('amounttopay', amountToPay);
    formData.append('transkey', document.getElementById('transkey').value);
    
    fetch('../api/user/transfer.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Transfer successful!');
            document.getElementById('transferForm').reset();
            calculateTransferCharges();
        } else {
            alert(data.message || 'Transfer failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during transfer');
    })
    .finally(() => {
        btn.innerHTML = 'Continue';
        btn.disabled = false;
    });
});

// Initialize charges calculation
calculateTransferCharges();
</script>
</body>
</html>