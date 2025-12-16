<?php
// Simple test without includes
session_start();

// Set some test variables
$sitename = "VTU Platform";
$sitecolor = "#007bff";
$data2 = (object) [
    'fname' => 'Test User',
    'wallet' => 1000.50
];
$dashboardData = [
    'recent_transactions' => []
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo $sitename; ?></title>
    
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
        <a href="#" class="header-title"><?php echo $sitename; ?></a>
        <a href="notifications" class="header-icon header-icon-1"><i class="fas fa-bell"></i></a>
        <a href="account" class="header-icon header-icon-4"><i class="fas fa-user"></i></a>
    </div>

    <!-- Main Content -->
    <div class="page-content header-clear-medium">
        <!-- Welcome Card -->
        <div class="card card-style">
            <div class="content">
                <h1>Welcome, <?php echo htmlspecialchars($data2->fname ?? 'User'); ?>!</h1>
                <p class="mb-0 font-600 color-highlight">Your VTU Dashboard</p>

                <!-- Wallet Balance -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card card-style bg-highlight">
                            <div class="content text-center">
                                <h3 class="text-white">Wallet Balance</h3>
                                <h1 class="text-white">₦<?php echo number_format($data2->wallet ?? 0, 2); ?></h1>
                                <a href="fund-wallet" class="btn btn-sm btn-border-white rounded-s">
                                    <i class="fas fa-plus"></i> Fund Wallet
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Services -->
        <div class="card card-style">
            <div class="content">
                <h4 class="mb-3">Quick Services</h4>
                
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="buy-airtime" class="d-block text-center p-3 bg-theme rounded-s text-white">
                            <i class="fas fa-phone fa-2x mb-2"></i>
                            <h6>Buy Airtime</h6>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="buy-data" class="d-block text-center p-3 bg-green-dark rounded-s text-white">
                            <i class="fas fa-wifi fa-2x mb-2"></i>
                            <h6>Buy Data</h6>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="cable-tv" class="d-block text-center p-3 bg-blue-dark rounded-s text-white">
                            <i class="fas fa-tv fa-2x mb-2"></i>
                            <h6>Cable TV</h6>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="electricity" class="d-block text-center p-3 bg-red-dark rounded-s text-white">
                            <i class="fas fa-bolt fa-2x mb-2"></i>
                            <h6>Electricity</h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- More Services -->
        <div class="card card-style">
            <div class="content">
                <h4 class="mb-3">More Services</h4>
                
                <div class="list-group list-custom-small">
                    <a href="recharge-pin" class="list-group-item">
                        <i class="fas fa-credit-card color-blue-dark"></i>
                        <span>Recharge Pins</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="buy-data-pin" class="list-group-item">
                        <i class="fas fa-sim-card color-green-dark"></i>
                        <span>Data Pins</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="exam-pins" class="list-group-item">
                        <i class="fas fa-graduation-cap color-red-dark"></i>
                        <span>Exam Pins</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="transfer" class="list-group-item">
                        <i class="fas fa-exchange-alt color-yellow-dark"></i>
                        <span>Transfer Money</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <!-- Airtime to Cash - Temporarily Hidden
                    <a href="airtime2cash" class="list-group-item">
                        <i class="fas fa-money-bill color-green-dark"></i>
                        <span>Airtime to Cash</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    -->
                    <a href="sendbulksms" class="list-group-item">
                        <i class="fas fa-sms color-blue-dark"></i>
                        <span>Bulk SMS</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card card-style">
            <div class="content">
                <div class="d-flex justify-content-between">
                    <h4>Recent Transactions</h4>
                    <a href="transactions" class="color-highlight">View All</a>
                </div>
                
                <?php if (!empty($dashboardData['recent_transactions'])): ?>
                    <div class="list-group list-custom-small">
                        <?php foreach (array_slice($dashboardData['recent_transactions'], 0, 5) as $transaction): ?>
                            <div class="list-group-item">
                                <i class="fas fa-circle color-green-dark" style="font-size: 8px;"></i>
                                <span>
                                    <?php echo htmlspecialchars($transaction->type ?? 'Transaction'); ?> -
                                    ₦<?php echo number_format($transaction->amount ?? 0, 2); ?>
                                </span>
                                <small class="text-muted">
                                    <?php echo date('M d', strtotime($transaction->date ?? 'now')); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">No recent transactions</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer Menu -->
    <div class="footer-menu footer-menu-style-3">
        <a href="index" class="active-nav"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="fund-wallet"><i class="fas fa-wallet"></i><span>Wallet</span></a>
        <a href="transactions"><i class="fas fa-history"></i><span>History</span></a>
        <a href="account"><i class="fas fa-user"></i><span>Account</span></a>
    </div>

    <!-- Scripts -->
    <script src="../assets/scripts/jquery.js"></script>
    <script src="../assets/scripts/bootstrap.min.js"></script>
    <script src="../assets/scripts/custom.js"></script>
</body>
</html>