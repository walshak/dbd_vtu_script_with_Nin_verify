<?php
require_once 'includes/common_init.php';
require_once 'includes/data_loaders.php';

// Load data for crypto page
$data = loadNetworksData($controller);
$sitecolor = isset($siteSettings['sitecolor']) ? $siteSettings['sitecolor'] : '#007bff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Trade - <?php echo $siteName; ?></title>
    
    <!-- Mobile App Styling -->
    <link rel="stylesheet" type="text/css" href="../assets/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="../assets/app.json">
    <meta name="theme-color" content="<?php echo $sitecolor; ?>">
    <link rel="apple-touch-icon" href="../assets/images/logo.png">
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
                <p class="mb-0 text-center font-600 color-highlight">Cryptocurrencies Trade</p>
                <h1 class="text-center">Trade Crypto Currencies For Cash</h1>

                <div class="row text-center mb-2">
                    <a href="javascript:selectNetworkByIcon('MTN');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../../assets/images/icons/bitcoin.png" width="45" height="45" alt="Bitcoin" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectNetworkByIcon('AIRTEL');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../../assets/images/icons/etherum.png" width="45" height="45" alt="Ethereum" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectNetworkByIcon('GLO');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../../assets/images/icons/usdt.png" width="45" height="45" alt="USDT" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectNetworkByIcon('9MOBILE');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../../assets/images/icons/pi.png" width="45" height="45" alt="PI" />
                        </span>
                    </a>
                </div>
                
                <hr/>
                <div class="d-flex">
                    <h5 style="background:<?php echo $sitecolor; ?>; color:#ffffff; padding:9px; margin-right:5px;">Code: </h5>
                    <marquee direction="left" scrollamount="5" style="background:#f2f2f2; padding:3px; border-radius:5rem;">
                        <h5 class="py-2">
                        [Bitcoin Wallet] - xxxxxxxxxxxxxxxxxxxxxxx [Ethereum Wallet] - xxxxxxxxxxxxxxxxxxxxxx [USDT Wallet] -xxxxxxxxxxxxxxxxxxxx [PI Wallet] - xxxxxxxxxxxxxxxxxxxxxxx
                        </h5>
                    </marquee>
                </div>
                <hr/>
                
                <form method="post" class="cryptoForm" id="cryptoForm" action="buy-crypto">
                    <fieldset>
                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="networkid" class="color-theme opacity-80 font-700 font-12">Network</label>
                            <select id="cryptonetworkid" name="network">
                                <option value="" disabled="" selected="">Select Network</option>
                                <?php if(!empty($data)): foreach($data AS $network): if(isset($network->datapinStatus) && $network->datapinStatus == "On"): ?>
                                    <option value="<?php echo $network->nId; ?>" networkname="<?php echo $network->network; ?>" sme="<?php echo isset($network->smeStatus) ? $network->smeStatus : ''; ?>" gifting="<?php echo isset($network->giftingStatus) ? $network->giftingStatus : ''; ?>" corporate="<?php echo isset($network->corporateStatus) ? $network->corporateStatus : ''; ?>"><?php echo $network->network; ?></option>
                                <?php endif; endforeach; endif; ?>
                            </select>
                            <span><i class="fa fa-chevron-down"></i></span>
                            <i class="fa fa-check disabled valid color-green-dark"></i>
                            <i class="fa fa-check disabled invalid color-red-dark"></i>
                            <em></em>
                        </div>
 
                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="cryptogroup" class="color-theme opacity-80 font-700 font-12">Crypto Type</label>
                            <select id="cryptogroup" name="cryptogroup">
                                <option value="">Select Type</option>
                                <option value="Bitcoin">Bitcoin</option>
                                <option value="Ethereum">Ethereum</option>
                                <option value="USDT">USDT</option>
                                <option value="PI">PI</option>
                            </select>
                            <span><i class="fa fa-chevron-down"></i></span>
                            <i class="fa fa-check disabled valid color-green-dark"></i>
                            <i class="fa fa-check disabled invalid color-red-dark"></i>
                            <em></em>
                        </div>

                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="cryptoplan" class="color-theme opacity-80 font-700 font-12">Crypto Plan</label>
                            <select id="cryptoplan" name="cryptoplan" required>
                                <option value="">Select Crypto Plan</option>
                            </select>
                            <span><i class="fa fa-chevron-down"></i></span>
                            <i class="fa fa-check disabled valid color-green-dark"></i>
                            <i class="fa fa-check disabled invalid color-red-dark"></i>
                            <em></em>
                        </div> 

                        <p id="verifyer"></p>

                        <div class="input-style input-style-always-active has-borders validate-field mb-4">
                            <label for="quantity" class="color-theme opacity-80 font-700 font-12">Quantity</label>
                            <input type="number" name="quantity" placeholder="Quantity" class="round-small" id="cryptoquantity" required />
                        </div>

                        <div class="input-style input-style-always-active has-borders validate-field mb-4">
                            <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                            <input type="hidden" name="amount" id="amount" />
                            <input type="text" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" readonly required />
                        </div>

                        <div class="input-style input-style-always-active has-borders validate-field mb-4">
                            <label for="walletaddress" class="color-theme opacity-80 font-700 font-12">Wallet Address</label>
                            <input type="text" name="walletaddress" placeholder="Your Crypto Wallet Address" class="round-small" id="walletaddress" required />
                        </div>

                        <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                        <input name="transkey" id="transkey" type="hidden" />

                        <div class="form-button">
                            <button type="submit" id="crypto-btn" name="purchase-crypto" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Trade Crypto
                            </button>
                        </div>
                    </fieldset>
                </form>        
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
    $(document).ready(function() {
        // Crypto trade form handling
        $('#cryptoForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            
            $.ajax({
                url: '../api/crypto/index.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#crypto-btn').html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                    $('#crypto-btn').prop('disabled', true);
                },
                success: function(response) {
                    if(response.status === 'success') {
                        window.location.href = 'transaction-details?ref=' + response.reference;
                    } else {
                        alert(response.message || 'An error occurred');
                    }
                },
                error: function() {
                    alert('Network error. Please try again.');
                },
                complete: function() {
                    $('#crypto-btn').html('Trade Crypto');
                    $('#crypto-btn').prop('disabled', false);
                }
            });
        });

        // Auto-calculate amount
        $('#cryptoquantity, #cryptoplan').on('change', function() {
            calculateCryptoAmount();
        });

        function calculateCryptoAmount() {
            let quantity = $('#cryptoquantity').val();
            let plan = $('#cryptoplan').val();
            
            if(quantity && plan) {
                // This would typically fetch from API based on selected plan
                let amount = quantity * 100; // Placeholder calculation
                $('#amount').val(amount);
                $('#amounttopay').val('₦' + amount.toLocaleString());
            }
        }
    });
    </script>
</body>
</html>