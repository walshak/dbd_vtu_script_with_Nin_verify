<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
list($data, $data2, $data3) = loadFundWalletData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Fund Wallet</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">
    <script src="https://js.paystack.co/v1/inline.js"></script>
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
        
        <div class="card card-style bg-theme pb-0">
            <div class="content" id="tab-group-1">
                <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight">
                    <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#tab-1">Bank</a>
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-2">Card</a>
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-3">Manual</a>
                </div>
                <div class="clearfix mb-3"></div>
                <div data-bs-parent="#tab-group-1" class="collapse show" id="tab-1">
                <div class="text-center">
                    <p class="text-center">
                        <span class="icon icon-l gradient-blue shadow-l rounded-sm">
                            <i class="fa fa-arrow-up font-30 color-white"></i>
                        </span>
                    </p>
                    <h4 class="text-primary">FUND WALLET</h4>
                    <?php if($controller->getConfigValue($data2,"monifyFeStatus") == "On"): ?>
                    <?php $chargesText = $controller->getConfigValue($data2,"monifyCharges"); ?>
                    <?php if($chargesText == 50 || $chargesText == "50"){$chargesText = "N".$chargesText;} else {$chargesText = $chargesText."%";} ?>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Fidelity Bank</p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sFidelityBank; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                    <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sFidelityBank; ?>')">Copy Account No</button>
                    <hr/>
                    <?php endif; if($controller->getConfigValue($data2,"monifyMoStatus") == "On"): ?>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Moniepoint Bank</p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sRolexBank; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                    <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sRolexBank; ?>')">Copy Account No</button>
                    <hr/>
                    <?php endif; if($controller->getConfigValue($data2,"monifyWeStatus") == "On"): ?>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Wema Bank</p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sBankNo; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                    <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sBankNo; ?>')">Copy Account No</button>
                    <hr/>
                    <?php endif; if($controller->getConfigValue($data2,"monifySaStatus") == "On"): ?>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Sterling Bank</p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sSterlingBank; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                    <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sSterlingBank; ?>')">Copy Account No</button>
                    <?php endif; ?>
                </div>
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-2">
                        <div class="text-center">
                            <p class="text-center">
                                <span class="icon icon-l gradient-blue shadow-l rounded-sm">
                                    <i class="fa fa-arrow-up font-30 color-white"></i>
                                </span>
                            </p>
                            <h4 class="text-primary">FUND WALLET</h4>
                            <p class="mb-2 text-dark font-600 font-16">
                                Pay with card, bank transfer, ussd, or bank deposit. Secured by Paystack
                            </p>
                    
                        </div>
                        
                        <?php if($controller->getConfigValue($data2,"paystackStatus") == "On"): ?>
                        <form id="paystackForm" method="post">
                        <div class="mt-5 mb-3">
                            
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="hidden" value="<?php echo $controller->getConfigValue($data2,"paystackCharges"); ?>" id="paystackcharges" name="paystackcharges" />
                                <input type="number" onkeyup="calculatePaystackCharges()" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                                <label for="amount" class="color-highlight">Amount</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="text" class="form-control" id="charges" placeholder="Charges" readonly>
                                <label for="charges" class="color-highlight">Charges</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="text" class="form-control" id="amounttopay" placeholder="You Would Get" readonly>
                                <label for="amounttopay" class="color-highlight">You Would Get</label>
                                <em>(required)</em>
                            </div>

                            <input type="hidden" name="email" value="<?php echo $data->sEmail; ?>" />
                        </div>

                        <div class="text-center"><img src="../assets/img/paystack.png" /></div>
                        <button type="submit" id="fund-with-paystack" name="fund-with-paystack" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Pay Now
                        </button>
                        </form>
                        <?php else : ?>
                            <h3 class="text-center text-danger">Opps!! Paystack Payment Is Disabled, Please Contact Admin</h3>
                        <?php endif; ?>
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-3">
                <div class="text-center">
                    <p class="text-center">
                        <span class="icon icon-l gradient-blue shadow-l rounded-sm">
                            <i class="fa fa-arrow-up font-30 color-white"></i>
                        </span>
                    </p>
                    <h4 class="text-primary">FUND WALLET</h4>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b><?php echo $data3->bankname; ?></p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account Name: </b><?php echo $data3->accountname; ?></p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data3->accountno; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Please contact admin before making any transfer.</p>
                    <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data3->accountno; ?>')">Copy Account No</button>
                    <a class="btn btn-success font-700 rounded-xl mt-3" href="https://wa.me/234<?php echo $data3->whatsapp; ?>">Contact Admin</a>
                    
                </div>
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
        alert('Copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

function calculatePaystackCharges() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const chargesPercent = parseFloat(document.getElementById('paystackcharges').value) || 0;
    
    let charges = 0;
    if (chargesPercent <= 100) {
        // Percentage charges
        charges = (amount * chargesPercent) / 100;
    } else {
        // Fixed charges
        charges = chargesPercent;
    }
    
    const youWouldGet = amount - charges;
    
    document.getElementById('charges').value = charges.toFixed(2);
    document.getElementById('amounttopay').value = youWouldGet.toFixed(2);
}

// Paystack payment form
document.getElementById('paystackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('amount').value);
    const email = document.querySelector('input[name="email"]').value;
    
    if (amount < 100) {
        alert('Minimum amount is ₦100');
        return;
    }
    
    const btn = document.getElementById('fund-with-paystack');
    btn.innerHTML = 'Processing...';
    btn.disabled = true;
    
    // Initialize Paystack payment
    const handler = PaystackPop.setup({
        key: '<?php echo $controller->getConfigValue($data2,"paystackKey"); ?>', // Your public key
        email: email,
        amount: amount * 100, // Paystack amount is in kobo
        currency: 'NGN',
        ref: 'FW_' + Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference
        metadata: {
            custom_fields: [
                {
                    display_name: "Mobile Number",
                    variable_name: "mobile_number",
                    value: "<?php echo $data->sPhone; ?>"
                }
            ]
        },
        callback: function(response) {
            // Payment successful
            alert('Payment successful! Reference: ' + response.reference);
            
            // Send verification to server
            fetch('../api/user/verify-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'reference=' + response.reference + '&amount=' + amount
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Payment verified! Your wallet has been credited.');
                    location.reload();
                } else {
                    alert('Payment verification failed. Please contact support.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Payment verification error. Please contact support.');
            });
        },
        onClose: function() {
            btn.innerHTML = 'Pay Now';
            btn.disabled = false;
            alert('Payment window closed.');
        }
    });
    
    handler.openIframe();
});
</script>
</body>
</html>