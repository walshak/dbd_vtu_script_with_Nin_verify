<?php
require_once 'includes/common_init.php';
require_once 'includes/data_loaders.php';

// Load pricing data
$pricingData = loadPricingData($controller);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Pricing - <?php echo $siteName; ?></title>
    
    <!-- Mobile App Styling -->
    <link rel="stylesheet" type="text/css" href="../assets/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="../assets/app.json">
    <meta name="theme-color" content="<?php echo $sitecolor; ?>">
    <link rel="apple-touch-icon" href="../assets/images/logo.png">
    
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table th, .table td {
            white-space: nowrap;
            padding: 8px;
            font-size: 12px;
        }
        .bg-blue-dark {
            background-color: #1e3a8a !important;
        }
        .section-header {
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .pricing-alert {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
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
                <p class="mb-0 font-600 color-highlight">Our Pricing</p>
                <h1 class="font-20">Service Rates & Plans</h1>
                
                <!-- Networks Section -->
                <h2 class="font-18 section-header">Networks</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Id</b></th>
                                <th class="text-white"><b>Network</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['networks'])): foreach ($pricingData['networks'] AS $network): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($network->nId ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($network->network ?? ''); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="2" class="text-center">No network data available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Airtime Section -->
                <h2 class="font-18 section-header">Airtime Discount Rates</h2>
                <div class="pricing-alert">
                    <i class="fas fa-info-circle"></i> Scroll horizontally to view the entire table
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Network</b></th>
                                <th class="text-white"><b>Subscriber</b></th>
                                <th class="text-white"><b>Agents</b></th>
                                <th class="text-white"><b>Vendors</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['airtime'])): foreach ($pricingData['airtime'] AS $airtime): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($airtime->network ?? ''); ?></td>
                                    <td><?php echo (100 - ($airtime->aUserDiscount ?? 0)); ?>%</td>
                                    <td><?php echo (100 - ($airtime->aAgentDiscount ?? 0)); ?>%</td>
                                    <td><?php echo (100 - ($airtime->aVendorDiscount ?? 0)); ?>%</td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center">No airtime pricing data available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Data Plans Section -->
                <h2 class="font-18 section-header">Data Plan Pricing</h2>
                <div class="pricing-alert">
                    <i class="fas fa-info-circle"></i> Scroll horizontally to view the entire table
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Network</b></th>
                                <th class="text-white"><b>Plan Id</b></th>
                                <th class="text-white"><b>Plan</b></th>
                                <th class="text-white"><b>Subscriber</b></th>
                                <th class="text-white"><b>Agents</b></th>
                                <th class="text-white"><b>Vendors</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['data_plans'])): foreach ($pricingData['data_plans'] AS $plans): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($plans->network ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($plans->pId ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($plans->name ?? '') . ' (' . ($plans->type ?? '') . ') (' . ($plans->day ?? '') . ' days)'); ?></td>
                                    <td>₦<?php echo number_format($plans->userprice ?? 0); ?></td>
                                    <td>₦<?php echo number_format($plans->agentprice ?? 0); ?></td>
                                    <td>₦<?php echo number_format($plans->vendorprice ?? 0); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center">No data plan pricing available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cable TV Provider Section -->
                <h2 class="font-18 section-header">Cable TV Providers</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Cable Id</b></th>
                                <th class="text-white"><b>Provider</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['cable_providers'])): foreach ($pricingData['cable_providers'] AS $cable): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cable->cId ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($cable->provider ?? ''); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="2" class="text-center">No cable providers available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cable Plans Section -->
                <h2 class="font-18 section-header">Cable TV Plans</h2>
                <div class="pricing-alert">
                    <i class="fas fa-info-circle"></i> Scroll horizontally to view the entire table
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Provider</b></th>
                                <th class="text-white"><b>Plan Id</b></th>
                                <th class="text-white"><b>Plan</b></th>
                                <th class="text-white"><b>Subscriber</b></th>
                                <th class="text-white"><b>Agents</b></th>
                                <th class="text-white"><b>Vendors</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['cable_plans'])): foreach ($pricingData['cable_plans'] AS $plans): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($plans->provider ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($plans->cpId ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($plans->name ?? '') . ' (' . ($plans->day ?? '') . ' days)'); ?></td>
                                    <td>₦<?php echo number_format($plans->userprice ?? 0); ?></td>
                                    <td>₦<?php echo number_format($plans->agentprice ?? 0); ?></td>
                                    <td>₦<?php echo number_format($plans->vendorprice ?? 0); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center">No cable plans available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Electricity Providers Section -->
                <h2 class="font-18 section-header">Electricity Providers</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Id</b></th>
                                <th class="text-white"><b>Provider</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['electricity_providers'])): foreach ($pricingData['electricity_providers'] AS $provider): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($provider->eId ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($provider->provider ?? ''); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="2" class="text-center">No electricity providers available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Exam Pins Section -->
                <h2 class="font-18 section-header">Exam Pin Pricing</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-blue-dark">
                                <th class="text-white"><b>Exam Id</b></th>
                                <th class="text-white"><b>Provider</b></th>
                                <th class="text-white"><b>Price</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pricingData['exam_pins'])): foreach ($pricingData['exam_pins'] AS $exam): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exam->eId ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($exam->provider ?? ''); ?></td>
                                    <td>₦<?php echo number_format($exam->price ?? 0); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="3" class="text-center">No exam pin pricing available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-3" style="background: #e7f3ff; border-radius: 8px;">
                    <h5><i class="fas fa-info-circle text-primary"></i> Pricing Information</h5>
                    <ul class="mb-0" style="font-size: 14px;">
                        <li>Prices shown are current rates and may be subject to change</li>
                        <li>Different user levels (Subscriber, Agent, Vendor) have different pricing</li>
                        <li>Discounts are automatically applied based on your account type</li>
                        <li>All prices are in Nigerian Naira (₦)</li>
                    </ul>
                </div>
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
</body>
</html>