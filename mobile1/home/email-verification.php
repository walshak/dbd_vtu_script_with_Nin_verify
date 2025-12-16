<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Check if user email is already verified
if (isset($data->sEmailVerified) && $data->sEmailVerified == 1) {
    header('Location: homepage.php');
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
    <title><?php echo $sitename; ?> - Email Verification</title>
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

            <div class="content text-center">
                <img src="../assets/images/icons/email-verification.png" style="width:250px; height:200px;" />
           
                <p class="mb-0 font-600 color-highlight">Email Verification</p>
                <h1>Verification</h1>
                <p class="mb-1 font-600 text-danger">A Verification Code Has Been Sent To Your Email. Please Provide The Code Below To Verify Your Account.</p>
                <p class="mb-3 font-600 text-danger">If You Can't Find The Verification Code, Please Do Check Your Spam Folder.</p>
                
                <form method="post" id="verificationForm">
                        <fieldset>
                            <input type="hidden" name="email" value="<?php echo $data->sEmail; ?>" />
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="code" class="color-theme opacity-80 font-700 font-12">Verification Code</label>
                                <input type="number" name="code" placeholder="Enter 6-digit code" value="" class="round-small" id="code" required maxlength="6" />
                            </div>
                            
                            <div class="form-button">
                            <button type="submit" id="verify-btn" name="email-verification" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                    Verify Email
                            </button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="button" id="resend-btn" class="btn btn-link text-decoration-none">
                                    Resend Verification Code
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
// Auto-format verification code input
document.getElementById('code').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
    if (value.length > 6) {
        value = value.substring(0, 6);
    }
    e.target.value = value;
});

// Form submission
document.getElementById('verificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const code = document.getElementById('code').value;
    
    if (code.length !== 6) {
        alert('Please enter a 6-digit verification code');
        return;
    }
    
    const btn = document.getElementById('verify-btn');
    btn.innerHTML = 'Verifying...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('../api/user/verify-email.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Email verified successfully!');
            window.location.href = 'homepage.php';
        } else {
            alert(data.message || 'Verification failed. Please check your code.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during verification');
    })
    .finally(() => {
        btn.innerHTML = 'Verify Email';
        btn.disabled = false;
    });
});

// Resend verification code
document.getElementById('resend-btn').addEventListener('click', function() {
    const btn = this;
    btn.innerHTML = 'Sending...';
    btn.disabled = true;
    
    fetch('../api/user/resend-verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=<?php echo urlencode($data->sEmail); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Verification code sent! Please check your email.');
        } else {
            alert(data.message || 'Failed to send verification code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending verification code');
    })
    .finally(() => {
        btn.innerHTML = 'Resend Verification Code';
        btn.disabled = false;
    });
});
</script>
</body>
</html>