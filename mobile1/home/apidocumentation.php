<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page (using general data loader since this is an informational page)
require_once __DIR__ . '/includes/data_loaders.php';
loadGeneralPageData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $sitename; ?> - API Documentation</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">
    <style>
        .endpoint {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 15px;
            background-color: white;
        }
        .endpoint h5 {
            margin-bottom: 10px;
            color: <?php echo $sitecolor; ?>;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            overflow-x: auto;
        }
    </style>
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
                <p class="mb-0 font-600 color-highlight">API Documentation</p>
                <h1>Developer API</h1>
                
                <div class="endpoint">
                    <h5>Airtime Purchase</h5>
                    <p><strong>Endpoint:</strong> <code>https://jossyfeydataservice.com.ng/api/airtime/</code></p>
                    <p><strong>Method:</strong> GET</p>
                    <pre><code>curl --location 'https://jossyfeydataservice.com.ng/api/airtime/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'</code></pre>
                </div>

                <div class="endpoint">
                    <h5>Data Purchase</h5>
                    <p><strong>Endpoint:</strong> <code>https://jossyfeydataservice.com.ng/api/data/</code></p>
                    <p><strong>Method:</strong> GET</p>
                    <pre><code>curl --location 'https://jossyfeydataservice.com.ng/api/data/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'</code></pre>
                </div>

                <div class="endpoint">
                    <h5>Cable TV</h5>
                    <p><strong>Endpoint:</strong> <code>https://jossyfeydataservice.com.ng/api/cabletv/</code></p>
                    <p><strong>Method:</strong> GET</p>
                    <pre><code>curl --location 'https://jossyfeydataservice.com.ng/api/cabletv/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'</code></pre>
                </div>

                <div class="endpoint">
                    <h5>Electricity</h5>
                    <p><strong>Endpoint:</strong> <code>https://jossyfeydataservice.com.ng/api/electricity/</code></p>
                    <p><strong>Method:</strong> GET</p>
                    <pre><code>curl --location 'https://jossyfeydataservice.com.ng/api/electricity/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'</code></pre>
                </div>

                <div class="endpoint">
                    <h5>Data Pin</h5>
                    <p><strong>Endpoint:</strong> <code>https://jossyfeydataservice.com.ng/api/datapin/</code></p>
                    <p><strong>Method:</strong> GET</p>
                    <pre><code>curl --location 'https://jossyfeydataservice.com.ng/api/datapin/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'</code></pre>
                </div>

                <div class="alert alert-info">
                    <h6>Authentication</h6>
                    <p>All API requests require a valid API token in the Authorization header.</p>
                    <p>Contact support to get your API access credentials.</p>
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