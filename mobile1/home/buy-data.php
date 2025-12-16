<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadDataPurchaseData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Buy Data</title>
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
                <p class="mb-0 text-center font-600 color-highlight">Data For All Network</p>
                <h1 class="text-center">Buy Data</h1>

                <div class="row text-center mb-2">
                    
                    <a href="javascript:selectNetworkByIcon('MTN');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/mtn.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectNetworkByIcon('AIRTEL');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/airtel.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectNetworkByIcon('GLO');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/glo.png" width="45" height="45" />
                        </span>
                    </a>
                    
                    <a href="javascript:selectNetworkByIcon('9MOBILE');" class="col-3 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="../assets/images/icons/9mobile.png" width="45" height="45" />
                        </span>
                    </a>
                    
                </div>
                <hr/>
                <div class="d-flex">
                    <h5 style="background:<?php echo $sitecolor; ?>; color:#ffffff; padding:9px;  margin-right:5px;">Code: </h5>
                    <marquee direction="left" scrollamount="5" style="background:#f2f2f2; padding:3px; border-radius:5rem;">
                        <h5 class="py-2">
                        [MTN SME] - *461*4# - [MTN Gifting] - *131*4# - [9Mobile] - *228# - [Airtel] - *140# - [Glo] - *127*0#
                        </h5>
                    </marquee>
                </div>
                <hr/>
                
                <form method="post" class="dataplanForm" id="dataplanForm">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networkid" class="color-theme opacity-80 font-700 font-12">Network</label>
                                <select id="networkid" name="network" required>
                                    <option value="" disabled="" selected="">Select Network</option>
                                    <?php foreach($data[0] AS $network): if($network->networkStatus == "On"): ?>
                                        <option value="<?php echo $network->nId; ?>" networkname="<?php echo $network->network; ?>"><?php echo $network->network; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="plantype" class="color-theme opacity-80 font-700 font-12">Plan Type</label>
                                <select id="plantype" name="plantype" required>
                                    <option value="" disabled="" selected="">Select Plan Type</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="dataplan" class="color-theme opacity-80 font-700 font-12">Data Plan</label>
                                <select id="dataplan" name="dataplan" required>
                                    <option value="" disabled="" selected="">Select Data Plan</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
 
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                                <input type="number" onkeyup="verifyNetwork()" name="phone" placeholder="Phone Number" value="" class="round-small" id="phone" required />
                            </div>

                            <p id="verifyer"></p>

                            <div class="form-check icon-check">
                                <input class="form-check-input" type="checkbox" name="ported_number" id="ported_number">
                                <label class="form-check-label" for="ported_number">Disable Number Validator</label>
                                <i class="icon-check-1 fa fa-square color-gray-dark font-16"></i>
                                <i class="icon-check-2 fa fa-check-square font-16 color-highlight"></i>
                            </div>

                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />

                            <div class="form-button">
                            <button type="submit" id="data-btn" name="purchase-data" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Buy Data
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
// Data plans data
const dataPlans = <?php echo $data[1]; ?>;

function selectNetworkByIcon(networkName) {
    const networkSelect = document.getElementById('networkid');
    const options = networkSelect.options;
    
    for (let i = 0; i < options.length; i++) {
        if (options[i].getAttribute('networkname') === networkName) {
            networkSelect.selectedIndex = i;
            networkSelect.dispatchEvent(new Event('change'));
            break;
        }
    }
}

function verifyNetwork() {
    const phone = document.getElementById('phone').value;
    const networkSelect = document.getElementById('networkid');
    const verifyer = document.getElementById('verifyer');
    const portedCheckbox = document.getElementById('ported_number');
    
    if (phone.length >= 11 && !portedCheckbox.checked) {
        const prefix = phone.substring(0, 4);
        let detectedNetwork = '';
        
        // MTN prefixes
        if (['0803', '0806', '0703', '0706', '0813', '0810', '0814', '0816', '0903', '0906', '0913', '0916'].includes(prefix)) {
            detectedNetwork = 'MTN';
        }
        // Airtel prefixes  
        else if (['0802', '0808', '0708', '0812', '0701', '0902', '0901', '0904', '0907', '0912'].includes(prefix)) {
            detectedNetwork = 'AIRTEL';
        }
        // Glo prefixes
        else if (['0805', '0807', '0705', '0815', '0811', '0905', '0915'].includes(prefix)) {
            detectedNetwork = 'GLO';
        }
        // 9Mobile prefixes
        else if (['0809', '0818', '0817', '0909', '0908'].includes(prefix)) {
            detectedNetwork = '9MOBILE';
        }
        
        if (detectedNetwork) {
            // Auto-select the detected network
            const options = networkSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].getAttribute('networkname') === detectedNetwork) {
                    networkSelect.selectedIndex = i;
                    networkSelect.dispatchEvent(new Event('change'));
                    verifyer.innerHTML = `<span class="text-success">✓ ${detectedNetwork} network detected</span>`;
                    break;
                }
            }
        } else {
            verifyer.innerHTML = '<span class="text-warning">⚠ Network not detected. Please select manually.</span>';
        }
    } else {
        verifyer.innerHTML = '';
    }
}

function loadPlanTypes() {
    const networkSelect = document.getElementById('networkid');
    const planTypeSelect = document.getElementById('plantype');
    const dataplanSelect = document.getElementById('dataplan');
    
    // Clear existing options
    planTypeSelect.innerHTML = '<option value="" disabled selected>Select Plan Type</option>';
    dataplanSelect.innerHTML = '<option value="" disabled selected>Select Data Plan</option>';
    
    if (!networkSelect.value) return;
    
    const selectedNetwork = networkSelect.options[networkSelect.selectedIndex].getAttribute('networkname');
    const planTypes = new Set();
    
    // Get unique plan types for selected network
    dataPlans.forEach(plan => {
        if (plan.network === selectedNetwork) {
            planTypes.add(plan.plantype);
        }
    });
    
    // Add plan types to select
    planTypes.forEach(planType => {
        const option = document.createElement('option');
        option.value = planType;
        option.textContent = planType;
        planTypeSelect.appendChild(option);
    });
}

function loadDataPlans() {
    const networkSelect = document.getElementById('networkid');
    const planTypeSelect = document.getElementById('plantype');
    const dataplanSelect = document.getElementById('dataplan');
    
    // Clear existing options
    dataplanSelect.innerHTML = '<option value="" disabled selected>Select Data Plan</option>';
    
    if (!networkSelect.value || !planTypeSelect.value) return;
    
    const selectedNetwork = networkSelect.options[networkSelect.selectedIndex].getAttribute('networkname');
    const selectedPlanType = planTypeSelect.value;
    
    // Filter data plans
    const filteredPlans = dataPlans.filter(plan => 
        plan.network === selectedNetwork && plan.plantype === selectedPlanType
    );
    
    // Add data plans to select
    filteredPlans.forEach(plan => {
        const option = document.createElement('option');
        option.value = plan.planid;
        option.setAttribute('price', plan.price);
        option.textContent = `${plan.name} - ₦${plan.price}`;
        dataplanSelect.appendChild(option);
    });
}

// Event listeners
document.getElementById('networkid').addEventListener('change', loadPlanTypes);
document.getElementById('plantype').addEventListener('change', loadDataPlans);

// Form submission
document.getElementById('dataplanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const networkSelect = document.getElementById('networkid');
    const planTypeSelect = document.getElementById('plantype');
    const dataplanSelect = document.getElementById('dataplan');
    const phone = document.getElementById('phone').value;
    
    if (!networkSelect.value || !planTypeSelect.value || !dataplanSelect.value || !phone) {
        alert('Please fill all required fields');
        return;
    }
    
    if (phone.length < 11) {
        alert('Please enter a valid phone number');
        return;
    }
    
    const selectedPlan = dataplanSelect.options[dataplanSelect.selectedIndex];
    const price = selectedPlan.getAttribute('price');
    
    const confirmMsg = `Purchase ${selectedPlan.textContent} for ${phone}?`;
    if (!confirm(confirmMsg)) {
        return;
    }
    
    const btn = document.getElementById('data-btn');
    btn.innerHTML = 'Processing...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('../api/data/purchase.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Data purchase successful!');
            this.reset();
            document.getElementById('verifyer').innerHTML = '';
            document.getElementById('plantype').innerHTML = '<option value="" disabled selected>Select Plan Type</option>';
            document.getElementById('dataplan').innerHTML = '<option value="" disabled selected>Select Data Plan</option>';
        } else {
            alert(data.message || 'Purchase failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during purchase');
    })
    .finally(() => {
        btn.innerHTML = 'Buy Data';
        btn.disabled = false;
    });
});
</script>
</body>
</html>