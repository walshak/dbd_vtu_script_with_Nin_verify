<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
list($data, $data2) = loadUserProfileData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Profile</title>
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
        
        <div class="card card-style bg-theme pb-0">
            <div class="content" id="tab-group-1">
                <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight">
                    <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#tab-1">Profile</a>
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-2">Password</a>
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-3">Pin</a>
                </div>
                <div class="clearfix mb-3"></div>
                <div data-bs-parent="#tab-group-1" class="collapse show" id="tab-1">
                    <p class="mb-n1 color-highlight font-600 font-12">Account Details</p>
                        <h4>Basic Information</h4>
                        
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <i class="fa font-14 fa-user rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Name: </b> <?php echo $data->sFname. " " . $data->sLname; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#">
                                <i class="fa font-14 fa-envelope rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Email: </b> <?php echo $data->sEmail; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#"> 
                                <i class="fa font-14 fa-phone rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Phone: </b> <?php echo $data->sPhone; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#">
                                <i class="fa font-14 fa-globe rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>State: </b> <?php echo $data->sState; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>   
                                        
                        </div>

                        <p class="mb-n1 mt-2 color-highlight font-600 font-12">Referral</p>
                        <h4>Referral Link</h4>
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <input type="text" class="form-control" readonly value="<?php echo $siteurl."mobile/register/?referral=".$data->sPhone; ?>" />
                            </a>
                            <a href="#">
                                <button class="btn btn-danger btn-sm" onclick="copyToClipboard('<?php echo $siteurl."mobile/register/?referral=".$data->sPhone; ?>')">Copy Link</button>
                                <button class="btn btn-success btn-sm" onclick="window.open('referrals.php')">View Commission</button>
                            </a>
                        </div>
                        
                        <?php if($data->sType == 3): ?>
                        <p class="mb-n1 mt-2 color-highlight font-600 font-12">Developer</p>
                        <h4>Api Documentation</h4>
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <input type="text" class="form-control" readonly value="<?php echo $data->sApiKey; ?>" />
                            </a>
                            <a href="#">
                                <button class="btn btn-danger btn-sm" onclick="copyToClipboard('<?php echo $data->sApiKey; ?>')">Copy Api Key</button>
                                <?php if(!empty($data2)): ?>
                                    <button class="pl-5 btn btn-success btn-sm" onclick="window.open('<?php echo $data2->apidocumentation; ?>')">View Documentation</button>
                                <?php endif; ?>
                            </a>
                         </div>
                         <?php endif; ?>
                        
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-2">
                    <p class="mb-n1 color-highlight font-600 font-12">Update Login Details</p>
                        <h4>Login Details</h4>
                        
                        <form id="passForm" method="post">
                        <div class="mt-5 mb-3">
                            
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="password" class="form-control" id="old-pass" name="oldpass" placeholder="Old Password" required>
                                <label for="old-pass" class="color-highlight">Old Password</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="password" class="form-control" id="new-pass" name="newpass" placeholder="New Password" required>
                                <label for="new-pass" class="color-highlight">New Password</label>
                                <em>(required)</em>
                            </div>

                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="password" class="form-control" id="retype-pass" placeholder="Retype Password" required>
                                <label for="retype-pass" class="color-highlight">Retype Password</label>
                                <em>(required)</em>
                            </div>
                        </div>
                        <button type="submit" id="update-pass-btn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Update Password
                        </button>
                        </form>
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-3">
                    <p class="mb-n1 color-highlight font-600 font-12">Update Transaction Pin</p>
                        <h4>Transaction Pin</h4>
                        
                        <form id="pinForm" method="post">
                        <div class="mt-3 mb-3">
                            <p class="text-danger"><b>Note: </b> The Default Transaction Pin Is '1234'. Your Transaction Pin should be a four digit number. </p>
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="number" class="form-control" id="old-pin" name="oldpin" placeholder="Old Pin" required>
                                <label for="old-pin" class="color-highlight">Old Pin</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="number"  class="form-control" id="new-pin" name="newpin" placeholder="New Pin" required>
                                <label for="new-pin" class="color-highlight">New Pin</label>
                                <em>(required)</em>
                            </div>

                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="number" class="form-control" id="retype-pin" placeholder="Retype Pin" required>
                                <label for="retype-pin" class="color-highlight">Retype Pin</label>
                                <em>(required)</em>
                            </div>
                        </div>
                        <button type="submit" id="update-pin-btn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Update Pin
                        </button>
                        </form>
                        
                        <hr/>

                        <p class="mb-n1 color-highlight font-600 font-12">Disable Transaction Pin</p>
                        <h4>Disable Pin</h4>
                        
                        <form id="pinStatusForm" method="post">
                        <div class="mt-3 mb-3">
                            <p class="text-danger"><b>Note: </b> Only Disable Pin When You Are Sure About The Security Of Your Phone And Your Account Is Secured With A Strong Password. </p>
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="number" maxlength="4" class="form-control" id="old-pin-disable" name="oldpin" placeholder="Old Pin" required>
                                <label for="old-pin-disable" class="color-highlight">Old Pin</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <select name="pinstatus" class="form-control" required>
                                    <option value="">Change Status</option>
                                    <?php if ($data->sPinStatus == 0): ?>
                                    <option value="0" selected>Enable</option> <option value="1">Disable</option>
                                    <?php else : ?>
                                    <option value="0">Enable</option> <option value="1" selected>Disable</option>
                                    <?php endif; ?>
                                </select>
                                <label for="new-pin" class="color-highlight">Change Status</label>
                                <em>(required)</em>
                            </div>
                        </div>
                        <button type="submit" name="disable-user-pin" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Update Pin Status
                        </button>
                        </form>
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

// Password form submission
document.getElementById('passForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const oldPass = document.getElementById('old-pass').value;
    const newPass = document.getElementById('new-pass').value;
    const retypePass = document.getElementById('retype-pass').value;
    
    if (newPass !== retypePass) {
        alert('New passwords do not match!');
        return;
    }
    
    if (newPass.length < 6) {
        alert('Password must be at least 6 characters long!');
        return;
    }
    
    const btn = document.getElementById('update-pass-btn');
    btn.innerHTML = 'Updating...';
    btn.disabled = true;
    
    fetch('../api/user/change-password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'oldpass=' + encodeURIComponent(oldPass) + '&newpass=' + encodeURIComponent(newPass)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Password updated successfully!');
            document.getElementById('passForm').reset();
        } else {
            alert(data.message || 'Failed to update password');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating password');
    })
    .finally(() => {
        btn.innerHTML = 'Update Password';
        btn.disabled = false;
    });
});

// Pin form submission
document.getElementById('pinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const oldPin = document.getElementById('old-pin').value;
    const newPin = document.getElementById('new-pin').value;
    const retypePin = document.getElementById('retype-pin').value;
    
    if (newPin !== retypePin) {
        alert('New pins do not match!');
        return;
    }
    
    if (newPin.length !== 4) {
        alert('Pin must be exactly 4 digits!');
        return;
    }
    
    const btn = document.getElementById('update-pin-btn');
    btn.innerHTML = 'Updating...';
    btn.disabled = true;
    
    fetch('../api/user/change-pin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'oldpin=' + encodeURIComponent(oldPin) + '&newpin=' + encodeURIComponent(newPin)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Pin updated successfully!');
            document.getElementById('pinForm').reset();
        } else {
            alert(data.message || 'Failed to update pin');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating pin');
    })
    .finally(() => {
        btn.innerHTML = 'Update Pin';
        btn.disabled = false;
    });
});

// Pin status form submission
document.getElementById('pinStatusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const oldPin = document.getElementById('old-pin-disable').value;
    const pinStatus = document.querySelector('select[name="pinstatus"]').value;
    
    if (oldPin.length !== 4) {
        alert('Pin must be exactly 4 digits!');
        return;
    }
    
    if (pinStatus === '') {
        alert('Please select pin status!');
        return;
    }
    
    const btn = document.querySelector('button[name="disable-user-pin"]');
    btn.innerHTML = 'Updating...';
    btn.disabled = true;
    
    fetch('../api/user/change-pin-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'oldpin=' + encodeURIComponent(oldPin) + '&pinstatus=' + encodeURIComponent(pinStatus)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Pin status updated successfully!');
            document.getElementById('pinStatusForm').reset();
            location.reload();
        } else {
            alert(data.message || 'Failed to update pin status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating pin status');
    })
    .finally(() => {
        btn.innerHTML = 'Update Pin Status';
        btn.disabled = false;
    });
});
</script>
</body>
</html>