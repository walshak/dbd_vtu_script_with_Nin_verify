<?php
// Include common initialization
require_once __DIR__ . '/includes/common_init.php';

// Load data for this page (using general data loader since this is a utility page)
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
    <title><?php echo $sitename; ?> - Calculator</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/app/icons/icon-192x192.png">
    <style>
        .calculator {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .calc-screen {
            width: 100%;
            height: 60px;
            font-size: 24px;
            text-align: right;
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .calc-btn {
            width: 22%;
            height: 60px;
            margin: 1%;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .calc-btn:hover {
            transform: scale(1.05);
        }
        .calc-btn.number {
            background: <?php echo $sitecolor; ?>;
            color: white;
        }
        .calc-btn.operator {
            background: #6c757d;
            color: white;
        }
        .calc-btn.equal {
            background: #28a745;
            color: white;
            width: 46%;
        }
        .calc-btn.clear {
            background: #dc3545;
            color: white;
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
                <p class="mb-0 font-600 color-highlight text-center">Utility Tool</p>
                <h1 class="text-center">Calculator</h1>
                
                <div class="calculator">
                    <input type="text" placeholder="0" id="output-screen" class="calc-screen" readonly>
                    
                    <div class="d-flex flex-wrap">
                        <button onclick="clr()" class="calc-btn clear">CL</button>
                        <button onclick="del()" class="calc-btn clear">DEL</button>
                        <button onclick="display('%')" class="calc-btn operator">%</button>
                        <button onclick="display('/')" class="calc-btn operator">/</button>
                        
                        <button onclick="display('7')" class="calc-btn number">7</button>
                        <button onclick="display('8')" class="calc-btn number">8</button>
                        <button onclick="display('9')" class="calc-btn number">9</button>
                        <button onclick="display('*')" class="calc-btn operator">*</button>
                        
                        <button onclick="display('4')" class="calc-btn number">4</button>
                        <button onclick="display('5')" class="calc-btn number">5</button>
                        <button onclick="display('6')" class="calc-btn number">6</button>
                        <button onclick="display('-')" class="calc-btn operator">-</button>
                        
                        <button onclick="display('1')" class="calc-btn number">1</button>
                        <button onclick="display('2')" class="calc-btn number">2</button>
                        <button onclick="display('3')" class="calc-btn number">3</button>
                        <button onclick="display('+')" class="calc-btn operator">+</button>
                        
                        <button onclick="display('.')" class="calc-btn operator">.</button>
                        <button onclick="display('0')" class="calc-btn number">0</button>
                        <button onclick="calculate()" class="calc-btn equal">=</button>
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
let outputScreen = document.getElementById("output-screen");

function display(num) {
    if (outputScreen.value == "0") {
        outputScreen.value = num;
    } else {
        outputScreen.value += num;
    }
}

function calculate() {
    try {
        outputScreen.value = eval(outputScreen.value);
    } catch (err) {
        alert("Invalid calculation");
    }
}

function clr() {
    outputScreen.value = "0";
}

function del() {
    outputScreen.value = outputScreen.value.slice(0, -1);
    if (outputScreen.value == "") {
        outputScreen.value = "0";
    }
}
</script>
</body>
</html>