<?php
// Common initialization for all mobile1 files
session_start();

// Check if user is logged in
if (!isset($_SESSION['sId'])) {
    header("Location: ../index.php");
    exit();
}

// Include necessary files
require_once("../core/includes/auto_loader2.php");

// Initialize variables
date_default_timezone_set('Africa/Lagos');
$protocol = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
$port = $_SERVER['SERVER_PORT'];
$defaultPort = ($protocol === 'https://') ? 443 : 80;
$portString = ($port != $defaultPort) ? ":" . $port : "";
$parentdirectory = "";
$assetsLoc = $protocol . $_SERVER['SERVER_NAME'] . $portString . "/" . $parentdirectory . "mobile1/home";
$siteurl = $protocol . $_SERVER['SERVER_NAME'] . $portString . "/" . $parentdirectory;

// Initialize controller
$controller = new Subscriber;

// Get common data that all pages need
$data2 = $controller->getProfileInfo($_SESSION['sId']); // User profile info
$data3 = $controller->getSiteSettings(); // Site settings

// Set common variables
$sitecolor = $data3->sitecolor;
$sitename = $data3->sitename;
$msg = "";
$homemsg = ""; // For topupmatescript.php

// Generate transaction reference for forms
$transRef = $controller->generateTransactionRef();

// Set message if passed via GET
if (isset($_GET["msg"])) {
    $msg = $controller->createPopMessage("Alert", $_GET["msg"], "blue");
}
