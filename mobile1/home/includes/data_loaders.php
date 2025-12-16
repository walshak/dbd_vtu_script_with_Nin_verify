<?php
// Data loading functions for different pages
// Include common_init.php before using these functions

function loadAirtimeData($controller)
{
    $data = array();
    $data[0] = $controller->getNetworks();
    $data[1] = json_encode($controller->getAirtimeDiscount());
    $controller->recordLastActivity();
    return $data;
}

function loadDataPurchaseData($controller)
{
    $data = array();
    $data[0] = $controller->getNetworks();
    $data[1] = json_encode($controller->getDataPlans());
    $controller->recordLastActivity();
    return $data;
}

function loadDataPinData($controller)
{
    $data = array();
    $data[0] = $controller->getNetworks();
    $data[1] = json_encode($controller->getDataPins());
    $controller->recordLastActivity();
    return $data;
}

function loadRechargePinData($controller)
{
    $data = array();
    $data[0] = $controller->getNetworks();
    $data[1] = json_encode($controller->getRechargePinDiscount());
    $controller->recordLastActivity();
    return $data;
}

function loadCableTvData($controller)
{
    $data = array();
    $data[0] = $controller->getCableProvider();
    $data[1] = json_encode($controller->getCablePlans());
    $controller->recordLastActivity();
    return $data;
}

function loadElectricityData($controller)
{
    $data = array();
    $data[0] = $controller->getElectricityProvider();
    $data[1] = $controller->getSiteSettings();
    $controller->recordLastActivity();
    return $data;
}

function loadNetworksData($controller)
{
    $data = array();
    if (method_exists($controller, 'getNetworks')) {
        $data = $controller->getNetworks();
    }
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return $data;
}

function loadPricingData($controller)
{
    $data = array();
    $data['networks'] = method_exists($controller, 'getNetworks') ? $controller->getNetworks() : array();
    $data['airtime'] = method_exists($controller, 'getAirtimeDiscount') ? $controller->getAirtimeDiscount() : array();
    $data['data_plans'] = method_exists($controller, 'getDataPlans') ? $controller->getDataPlans() : array();
    $data['cable_providers'] = method_exists($controller, 'getCableProvider') ? $controller->getCableProvider() : array();
    $data['cable_plans'] = method_exists($controller, 'getCablePlans') ? $controller->getCablePlans() : array();
    $data['electricity_providers'] = method_exists($controller, 'getElectricityProvider') ? $controller->getElectricityProvider() : array();
    $data['exam_pins'] = method_exists($controller, 'getExamPins') ? $controller->getExamPins() : array();

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return $data;
}

function loadExamPinData($controller)
{
    $data = array();
    $data[0] = $controller->getExamProvider();
    $controller->recordLastActivity();
    return $data;
}

function loadHomepageData($controller)
{
    $data = array();
    $data[0] = $controller->getProfileInfo($_SESSION['sId']);
    $data[1] = $controller->getApiConfiguration();
    $data[2] = $controller->getSiteSettings();
    $controller->recordLastActivity();
    return $data;
}

function loadViewPinsData($controller)
{
    $data = array();
    $data[0] = $controller->getDataPinTokens();
    $controller->recordLastActivity();
    return $data;
}

function loadViewRechargePinsData($controller)
{
    $data = array();
    $data[0] = $controller->getRechargePinTokens();
    $controller->recordLastActivity();
    return $data;
}

function loadTransactionsData($controller)
{
    global $limit;
    $data = array();
    $data[0] = $controller->getTransactionRecord($_SESSION['sId'], $limit);
    $controller->recordLastActivity();
    return $data;
}

function loadProfileData($controller)
{
    $data = array();
    $data[0] = $controller->getProfileInfo($_SESSION['sId']);
    $data[1] = $controller->getApiConfiguration();
    $data[2] = $controller->getSiteSettings();
    $controller->recordLastActivity();
    return $data;
}

function loadGeneralPageData()
{
    // For general pages that don't need specific data loading
    // Just record activity and load basic site settings
    global $controller, $transRef, $sitename, $sitecolor, $limit;

    // Only call recordLastActivity if the method exists
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return true;
}

function loadAlphaTopupData($controller)
{
    $data = array();
    $data[0] = $controller->getAlphaTopupPlans();
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return $data;
}

function loadContactUsData($controller)
{
    $data = array();
    $data[0] = $controller->getSiteSettings();
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return $data;
}

function loadNotificationsData($controller)
{
    $data = array();
    $data[0] = $controller->getAllNotification();
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return $data;
}

function loadUserProfileData($controller)
{
    global $data, $data2;

    // Load user profile data (this is already loaded in common_init.php as $data)
    // Load additional API documentation data if available
    if (method_exists($controller, 'getApiDocumentation')) {
        $data2 = $controller->getApiDocumentation();
    } elseif (method_exists($controller, 'getSiteSettings')) {
        $data2 = $controller->getSiteSettings();
    } else {
        $data2 = null;
    }

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }

    return array($data, $data2);
}

function loadFundWalletData($controller)
{
    global $data, $data2, $data3;

    // Load user data (this is already loaded in common_init.php as $data)
    // Load site settings for payment configurations
    if (method_exists($controller, 'getSiteSettings')) {
        $data2 = $controller->getSiteSettings();
    } else {
        $data2 = null;
    }

    // Load manual payment details
    if (method_exists($controller, 'getManualPaymentDetails')) {
        $data3 = $controller->getManualPaymentDetails();
    } elseif (method_exists($controller, 'getAdminBankDetails')) {
        $data3 = $controller->getAdminBankDetails();
    } else {
        // Create default manual payment data
        $data3 = (object) [
            'bankname' => 'Contact Admin',
            'accountname' => 'Contact Admin',
            'accountno' => '0000000000',
            'whatsapp' => '00000000000'
        ];
    }

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }

    return array($data, $data2, $data3);
}

function loadTransferData($controller)
{
    global $data;

    // Load user data with transfer settings
    // Transfer data includes wallet transfer charges and settings

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }

    return $data;
}

function loadReferralsData($controller)
{
    global $data, $data2;

    // Load user data with referral info (this is already loaded in common_init.php as $data)
    // Load referral bonus settings
    if (method_exists($controller, 'getReferralSettings')) {
        $data2 = $controller->getReferralSettings();
    } elseif (method_exists($controller, 'getSiteSettings')) {
        $data2 = $controller->getSiteSettings();
    } else {
        // Create default referral bonus data
        $data2 = (object) [
            'referalupgradebonus' => '0',
            'referalairtimebonus' => '0',
            'referaldatabonus' => '0',
            'referalcablebonus' => '0',
            'referalmeterbonus' => '0',
            'referalexambonus' => '0'
        ];
    }

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }

    return array($data, $data2);
}

function loadConfirmationData($controller)
{
    global $data, $data2;

    // Load confirmation data from session or GET parameters
    // This would typically contain the transaction details to confirm

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }

    return array($data, $data2);
}

function loadTransactionDetailsData($controller, $transRef)
{
    global $data;

    // Load specific transaction details
    if (method_exists($controller, 'getTransactionByRef')) {
        $data = $controller->getTransactionByRef($transRef);
    } elseif (method_exists($controller, 'getTransactionDetails')) {
        $data = $controller->getTransactionDetails($transRef);
    } else {
        $data = null;
    }

    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }

    return $data;
}

function loadBulkSmsData($controller)
{
    $data = array();
    if (method_exists($controller, 'getSmsSettings')) {
        $data['sms_settings'] = $controller->getSmsSettings();
    }
    if (method_exists($controller, 'getSmsRates')) {
        $data['sms_rates'] = $controller->getSmsRates();
    }
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    return $data;
}

function loadDashboardData($controller) {
    $data = array();
    
    // Load recent transactions
    if (method_exists($controller, 'getRecentTransactions')) {
        $data['recent_transactions'] = $controller->getRecentTransactions(5); // Get last 5 transactions
    } else {
        $data['recent_transactions'] = array();
    }
    
    // Load wallet balance (already available in $data global from common_init.php)
    // Load user statistics if available
    if (method_exists($controller, 'getUserStats')) {
        $data['user_stats'] = $controller->getUserStats();
    }
    
    if (method_exists($controller, 'recordLastActivity')) {
        $controller->recordLastActivity();
    }
    
    return $data;
}
?>
