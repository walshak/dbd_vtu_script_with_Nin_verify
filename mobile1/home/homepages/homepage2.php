
<div class="page-content header-clear-medium">

        <div class="card card-style bg-20" data-card-height="200" 
           
            style="height: 130px; background-image: linear-gradient(to top, <?php echo $sitecolor; ?> 0%, <?php echo $sitecolor; ?> 100%);">
            <!-- style="height: 130px; background-image: url('../../assets/img/bg.jpg')"> -->
            <div class="card-top ps-3 pt-2">
                <h1 class="color-white font-19"  style="text-shadow: 2px 2px 2px #000000;"><?php echo "Hi, ".$data->sFname; ?></h1>
            </div>
            <div class="card-top pe-3 pt-2">
                <h5 class="color-white float-end"  style="text-shadow: 2px 2px 2px #000000;">(<?php echo $controller->formatUserType($data->sType); ?>)</h5>
            </div>
            <div class="card-center ps-3 pt-2">
                <h2 class="color-white font-20" style="text-shadow: 2px 2px 2px #000000;">
                N<?php echo number_format($data->sWallet); ?> 
                </h2>
                <h4 class="color-white font-16"  style="text-shadow: 2px 2px 2px #000000;">
                Wallet Balance
                </h4>
            </div>
            <div class="card-center pe-3 pt-2">
            <a href="fund-wallet" class="float-end text-center">
                    <span class="icon icon-l bg-light shadow-l rounded-sm">
                        <i class="fa fa-arrow-up font-18" style="color:<?php echo $sitecolor; ?>"></i>
                    </span>
                    <h5 class="mb-0 pt-1 font-14 text-white" style="text-shadow: 2px 2px 2px #000000;">Add Funds</h5>
                </a>
            </div>
            <div class="card-bottom ps-3 pb-2 bt-3">
                <h3 class="font-15"><a href="fund-wallet" style="text-shadow: 2px 2px 2px #000000;"><b class="text-white">Click Here To Fund Your Wallet</b></a></h3>
            </div>
            
            <div class="card-overlay bg-gradient"></div>

        </div>


        <div class="card card-style mt-3">
            
            <div class="content mb-2 mt-3">
            <div>
                <h5>Shortlinks</h5>
                <hr/>
               </div>
                <div class="row text-center mb-0">
                    <a href="buy-airtime" class="col-3">
                        <span class="icon icon-l rounded-sm" style="background:#fafafa; color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-phone font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Buy Airtime</p>
                    </a>
                    <a href="buy-data-pin" class="col-3">
                        <span class="icon icon-l rounded-sm" style="background:#fafafa; color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-mobile font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Data Card</p>
                    </a>
                    <a href="buy-data" class="col-3">
                        <span class="icon icon-l rounded-sm" style="background:#fafafa; color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-wifi font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Buy Data</p>
                    </a>
                    <a href="fund-wallet" class="col-3">
                        <span class="icon icon-l rounded-sm" style="background:#fafafa; color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-arrow-up font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Fund Wallet</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-style mt-n3">
            <div class="content mb-3 mt-3">
               <div>
                <h5>Services</h5>
                <hr/>
               </div>

                <div class="row text-center mb-0">
                    
                <a href="buy-airtime" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-phone font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Airtime</p>
                    </a>

                    <a href="buy-data" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-wifi font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Data</p>
                    </a>

                    <a href="cable-tv" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-tv font-18 "></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Cable TV</p>
                    </a>

                    <a href="electricity" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-bolt font-18 "></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Electricity</p>
                    </a>

                    <a href="exam-pins" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-graduation-cap font-18 "></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Exam Pins</p>
                    </a>
                    
                    <!-- Airtime 2 Cash - Temporarily Hidden
                    <a href="airtime2cash" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-phone font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Airtime 2 Cash</p>
                    </a>
                    -->
                    
                    <a href="recharge-pin" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-receipt font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Recharge Card Pin</p>
                    </a>
                    
                    <a href="buy-data-pin" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-wifi font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Data Card</p>
                    </a>
                    
                    <a href="2bank" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-arrow-up font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Wallet 2 Bank</p>
                    </a>
                    
                    <a href="transactions" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-receipt font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Transactions</p>
                    </a>

                    
                    <a href="pricing" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-list font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Pricing</p>
                    </a>

                    <a href="fund-wallet" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-arrow-up font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Add Fund</p>
                    </a>

                    <a href="profile" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-user  font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Profile</p>
                    </a>
                    
                    <a href="contact-us" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-envelope font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Contact</p>
                    </a>

                    

                    <a href="#agent-upgrade-modal" id="upgrade-agent-btn" data-menu="agent-upgrade-modal" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-user-secret font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Agent</p>
                    </a>

                    <a href="logout" class="col-4 mt-2">
                        <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>;">
                            <i class="fa fa-lock  font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-13">Logout</p>
                    </a>
                    
                </div>
            </div>
        </div>

</div>

