<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page
require_once __DIR__ . '/includes/data_loaders.php';
$data = loadTransactionsData($controller);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - Transactions</title>
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
                <h4>Transactions</h4>
                <p>
                    Your last 100 transactions. <br/>
                    <b class="text-danger">Click on the transaction to view details.</b>
                </p>
                <form method="GET" class="the-submit-form">
                     <div class="form-group">
                      <input type="text" class="form-control" placeholder="Keyword" name="search" aria-label="Phone Or Keyword">
                     </div>
                     <div class="form-group mt-2">
                      <select class="form-control" name="searchfor" required>
                          <option value="">Search For ..</option>
                          <option value="all">All Transaction</option>
                          <option value="reference">Transaction Reference</option>
                          <option value="wallet">Wallet Transaction</option>
                          <option value="monnify">Monnify Transaction</option>
                          <option value="paystack">Paystack Transaction</option>
                          <option value="airtime">Airtime Transaction</option>
                          <option value="data">Data Transaction</option>
                          <option value="cable">Cable Tv Transaction</option>
                          <option value="exam">Exam Pin Transaction</option>
                          <option value="electricity">Electricity Transaction</option>
                          <option value="recharge-pin">RechargePin Transaction</option>
                      </select>
                     </div>
                     <div class="form-group mt-2">
                      <button class="btn btn-primary the-form-btn" type="submit"><i class="fa fa-search"></i> Search</button>
                     </div>
                     
                </form>
                 <?php if(isset($_GET["search"])): echo "<b class='text-info'>Showing Result For Search Key: '".$_GET["search"]."' </b>"; endif; ?>
            </div>
        </div>

        <div class="card card-style p-3">
            <?php if(!empty($data[0])){ $i=1; foreach($data[0] as $list){   ?>
           
                <a href="transaction-details.php?ref=<?php echo $list->transref; ?>" class="d-flex">
                    <div class="align-self-center">
                        <?php if($list->servicename == "Airtime"): ?>
                        <span class="icon icon-s gradient-green color-white rounded-sm shadow-xxl"><i class="fa fa-phone font-15"></i></span>
                        <?php elseif($list->servicename == "Data"): ?>
                        <span class="icon icon-s gradient-blue color-white rounded-sm shadow-xxl"><i class="fa fa-wifi font-15"></i></span>
                        <?php elseif($list->servicename == "Cable TV"): ?>
                        <span class="icon icon-s gradient-brown color-white rounded-sm shadow-xxl"><i class="fa fa-tv font-15"></i></span>
                        <?php elseif($list->servicename == "Electricity Bill"): ?>
                        <span class="icon icon-s gradient-yellow color-white rounded-sm shadow-xxl"><i class="fa fa-bolt font-15"></i></span>
                        <?php elseif($list->servicename == "Exam Pin"): ?>
                        <span class="icon icon-s gradient-green color-white rounded-sm shadow-xxl"><i class="fa fa-graduation-cap font-15"></i></span>
                        <?php elseif($list->servicename == "Cable TV"): ?>
                        <span class="icon icon-s gradient-blue color-white rounded-sm shadow-xxl"><i class="fa fa-tv font-15"></i></span>
                        <?php elseif($list->servicename == "Wallet Transfer"): ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-arrow-up font-15"></i></span>
                        <?php elseif($list->servicename == "Referral Bonus"): ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-user font-15"></i></span>
                        
                        <?php elseif($list->servicename == "Data Pin"): ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-barcode font-15"></i></span>
                        <?php elseif($list->servicename == "Recharge Pin"): ?>
                        <span class="icon icon-s gradient-green color-white rounded-sm shadow-xxl"><i class="fa fa-print font-15"></i></span>
                        <?php elseif($list->servicename == "Referral Debit"): ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-user font-15"></i></span>
                        <?php else: ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-list font-15"></i></span>
                        <?php endif; ?>
                    </div>
                    <div class="align-self-center">
                        <h5 class="ps-3 mb-n1 font-15"><?php echo $list->servicename; ?></h5>
                        <h6 class="ps-3 font-12 mt-3 color-theme opacity-70"><?php echo $list->servicedesc; ?></h6>
                        <span class="ps-3 font-10 color-theme opacity-70"><?php echo "Ref: ".$list->transref; ?></span>
                    </div>
                    <div class="ms-auto text-end align-self-center">
                        <h5 class="color-theme font-15 font-700 d-block mb-n1">N<?php echo $list->amount; ?></h5>
                        <?php if($list->status == 0): ?>
                        <span class="color-green-dark font-10"><?php echo $controller->formatDate2($list->date); ?> <i class="fa fa-check-circle"></i></span>
                        <?php elseif($list->status == 5 || $list->status == 2): ?>
                        <span class="color-blue-dark font-10"><?php echo $controller->formatDate2($list->date); ?> <i class="fa fa-exclamation-circle"></i></span>
                        <?php else: ?>
                        <span class="color-red-dark font-10"><?php echo $controller->formatDate2($list->date); ?> <i class="fa fa-exclamation-circle"></i></span>
                        <?php endif; ?>
                     </div>
                </a>
                <div class="divider my-3"></div>
            
            <?php $i++; }}  else {echo "<h3 class='text-danger'>No Transaction To Display</h3>";} ?>
        </div>
        
        <?php 
        // Get pagination variables from common_init.php
        global $pageCount, $limit;
        ?>
        <div class="card card-style">
            <div class="content">
                <div class="d-flex justify-content-between">
                    <h5>Transactions</h5>
                    <a class="btn btn-primary btn-sm" href="transactions.php?page=<?php echo $pageCount + 1; if(isset($_GET["search"])): echo "&search=".$_GET["search"]."&searchfor=".$_GET["searchfor"]; endif; ?>"><b>Next 100</b></a>
                </div>
             </div>
        </div>

    </div>

    <?php include_once(__DIR__ . '/includes/menu.php'); ?>

</div>

<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>
</body>
</html>