<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content">
            <div class="d-flex justify-content-between mb-0">
                <div>
                    <p class="mb-0 font-600 color-highlight">Transaction Details</p>
                    <h1>Recharge Pin</h1>
                </div>
                <div>
                    <a href="print-recharge-pin?ref=<?php echo $_GET['ref']; ?>" class="btn btn-info"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
            <p class="mb-0 font-600 text-danger">Click On The Pin To Copy</p>
            <div>
                <?php
                // Sample data object
                $data = (object) [
                    'tokens' => '1233455555555554,56722342234444443338,902233344444444433333333312', // Replace with actual recharge PIN tokens separated by commas
                    'serial' => 'SN121233345555534,S4444444444444N5678,44433333444444SN9012', // Replace with actual serial numbers separated by commas
                    'network' => 'MTN', // Replace with the desired network (MTN, AIRTEL, GLO, 9MOBILE)
                    'datasize' => '100', // Replace with the data size (e.g., 1.5GB, 2GB)
                    'quantity' => 3, // Replace with the number of recharge pins you want to display
                ];

                // Rest of the PHP code provided in your initial snippet
                ?>

                <?php if (!empty($data)) : $pins = explode(",", $data->tokens); $sn = explode(",", $data->serial); ?>
                    <?php $network = $data->network; $datasize = $data->datasize; $loadpin = "$data->datasize"; if ($datasize == "1.5GB") {
                        $loadpin = "*460*6*1# Then PIN or Text PIN to 460";
                        $checkBal = "*131*4#";
                    } ?>
                    <?php
                    if ($network == "AIRTEL") {
                        $cardColor = "#ff1a1a";
                        $cardLogo = "airtel.png";
                        $textColor = "#ffffff";
                        $checkBal = "*140#";
                    } elseif ($network == "GLO") {
                        $cardColor = "#ffffff";
                        $cardLogo = "glo.png";
                        $textColor = "#ffffff";
                        $checkBal = "*127*0#";
                    } elseif ($network == "9MOBILE" || $network == "9MOBILE") {
                        $cardColor = "#4caf50";
                        $cardLogo = "9mobile.png";
                        $textColor = "#ffffff";
                        $checkBal = "*232#";
                    } else {
                        $cardColor = "#ffcc00";
                        $cardLogo = "mtn.png";
                        $textColor = "#000000";
                        $checkBal = "*310#";
                    }
                    ?>

                    <?php for ($i = 0; $i < $data->quantity; $i++) : ?>

                        <div class="row border" style="margin: 10px;">
                            <div class="col-4" style="margin: 0; padding: 0; background-color:<?php echo $cardColor; ?>;">
                                <div class="text-dark" style="padding: 5px; font-size: 12px;">
                                    <p style="margin-bottom: 5px;"><img src="../../assets/images/icons/<?php echo $cardLogo; ?>" style="width: 30px; height: 30px;" /></p>
                                    <h6 style="color:<?php echo $textColor; ?>; font-size: 14px;">RECHARGE PIN</h6>
                                    <h6 style="color:<?php echo $textColor; ?>; font-size: 14px;"><?php echo $datasize; ?></h6>
                                    <p style="margin-bottom: 0; color:<?php echo $textColor; ?>; font-size: 12px;"><?php echo $sn[$i]; ?></p>
                                </div>
                            </div>
                            <div class="col-8 bg-white" style="margin: 0; padding: 0;">
                                <div class="text-center" style="padding: 5px; font-size: 14px;">
                                    <h6 style="font-size: 16px;"><?php echo strtoupper($data->network); ?></h6>
                                    <button style="background-color:#f2f2f2; border-radius:3rem; padding:5px; width:100%;" onclick="copyToClipboard('<?php echo trim($pins[$i]); ?>')"><h4 style="font-size: 16px;"><?php echo trim($pins[$i]); ?></h4></button>
                                    <p style="margin-bottom:0; font-size: 12px;"><b>Load <?php echo $loadpin; ?></b> <b>Bal:   <?php echo $checkBal; ?></b></p>
                                    <p style="font-size: 12px;">Powered By: <?php echo $sitename; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>





<!-- AirtimePIN -->
<div class="modal fade modalbox" id="RechargePIN" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="appHeader">
                    <div class="left">
                        <a href="#" data-bs-dismiss="modal">
                            <ion-icon name="return-up-back"></ion-icon>
                        </a>
                    </div>
                    <div class="pageTitle">
                        (1) Airtime PIN
                    </div>
                    <div class="right">
                        <a href="printToken?rt=CARD_PIN_64c556b9b22a9" class="headerButton">
                            <ion-icon name="print"></ion-icon>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="action-sheet-content">
                    <!-- Transactions -->
                    <div class="section mt-2">
                        <div class="transactions">
                            <!-- item -->
                            <div class="item" style="background-color:#ffffff; border-radius:10px; border: 1px black solid;">
                                <div class="detail">
                                    <img src="https://ncwallet.ng/dashboard/logo/mtn.jpg" alt="img" class="image-block imaged w48">
                                    <div>
                                        <strong>PIN: 68015891347017270</strong>
                                        <p>S/N: 00000024148212330</p>
                                        <p>How to: *555*PIN#</p>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="price text-danger">₦100</div>
                                    <h6>Okay</h6>
                                </div>
                            </div>
                            <!-- * item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- * AirtimePIN -->
