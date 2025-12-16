
<div class="page-content header-clear" style="background-color: #ffffff" >

<div class="card notch-clear rounded-3 mb-n5 mt-0" data-card-height="200" style="background-image: url('../../assets/img/dashboard1.png'); background-size: cover; background-position: center; border-radius: 15px;">

    <div class="card-body pt-4 mt-2 mb-n2">
        <h1 class="font-20 float-start color-white"><?php echo "Hi, ".$data->sFname; ?>!</h1>
        <h3 class="font-17 float-end color-white">(<?php echo $controller->formatUserType($data->sType); ?>)</h3>
        <div class="clearfix"></div>
    </div>
    <div class="card card-style mt-0 mb-5" style="height: 45px;">
        <div class="card-center ">
            <h3 class="float-start font-16 ps-3 pt-2">
                <span style="margin-right:10px;">Wallet</span> 
                <span id="hideEyeDiv" style="display:none;">&#8358;<?php echo number_format($data->sWallet); ?></span>
                <span id="openEyeDiv" >&#8358; *********</span>
            
                <span id="hideEye"><i class="fa fa-eye-slash" style="margin-left:20px;" aria-hidden="true"></i></span>
                <span id="openEye" style="display:none; margin-left:20px;"><i class="fa fa-eye" aria-hidden="true"></i></span>
                
            </h3>
            <a href="fund-wallet" class="btn float-end" style="background-color:<?php echo $sitecolor; ?>; border-radius:5rem; margin-right:7px"><b>Add Fund</b></a>
        
        </div>
    </div>

</div>


<?php $color = "CDC5E3"; ?>
<?php $coloor = "ffffff"; ?>

<div class="card card-style mt-3">
    <div class="content mb-2 mt-3">
        <div class="row text-center mb-0">
            <a href="contact-us" class="col-3">
                <span class="icon icon-l rounded-sm" style="background: #<?php echo $coloor; ?>; color:<?php echo $sitecolor; ?>;">
                    <i class="fas fa-phone" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13">Support</p>
            </a>
            <a href="buy-airtime" class="col-3">
                <span class="icon icon-l rounded-sm" style="background: #<?php echo $coloor; ?>; color:<?php echo $sitecolor; ?>;">
                    <i class="fas fa-mobile-alt" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13">Airtime</p>
            </a>
            <a href="buy-data" class="col-3">
                <span class="icon icon-l rounded-sm" style="background: #<?php echo $coloor; ?>; color:<?php echo $sitecolor; ?>;">
                    <i class="fas fa-wifi" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13">Data</p>
            </a>
            <a href="transactions" class="col-3">
                <span class="icon icon-l rounded-sm" style="background: #<?php echo $coloor; ?>; color :<?php echo $sitecolor; ?>;">
                    <i class="fas fa-history" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13">History</p>
            </a>
        </div>
    </div>
</div>



<div class="card card-style mt-n3">
    <div class="content mb-3 mt-3">
        <div>
            <h5>Enjoy Our Services:</h5>
            <hr/>
        </div>

        <div class="row text-center mb-0">
            
        <a href="buy-airtime" class="col-4 mt-2">
                <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">

                    <i class="fa fa-phone font-18"></i>
                </span>
                <p class="mb-0 pt-1 font-11">Airtime</p>
            </a>

            <a href="buy-data" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
                
                    <i class="fa fa-wifi font-18 "></i>
                </span>
                <p class="mb-0 pt-1 font-11">Data</p>
            </a>

            <a href="cable-tv" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
             
                    <i class="fa fa-tv font-18 "></i>
                </span>
                <p class="mb-0 pt-1 font-11">Cable TV</p>
            </a>

            <a href="electricity" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
         
                    <i class="fa fa-bolt font-18 "></i>
                </span>
                <p class="mb-0 pt-1 font-11">Electricity</p>
            </a>

            <a href="exam-pins" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
         
                    <i class="fa fa-graduation-cap font-18 "></i>
                </span>
                <p class="mb-0 pt-1 font-11">Exam Pins</p>
            </a>
            
            <a href="transactions" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
              
                    <i class="fa fa-calendar font-18"></i>
                </span>
                <p class="mb-0 pt-1 font-11">Transactions</p>
            </a>
             
<a href="recharge-pin" class="col-4 mt-2">
<span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
                    
                            <i class="fa fa-print font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Recharge Pin</p>
                    </a>
            
            <a href="pricing" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
              
                    <i class="fa fa-tags font-18"></i>
                </span>
                <p class="mb-0 pt-1 font-11">Pricing</p>
            </a>

            <a href="fund-wallet" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
              
                            <i class="fa fa-plus font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Add Fund</p>
                    </a>
                   <!-- Airtime 2 Cash - Temporarily Hidden
                   <a href="airtime2cash" class="col-4 mt-2">
                   <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
                      
                            <i class="fa fa-undo font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Airtime 2 Cash</p>
                    </a>
                    -->
                    <a href="buy-data-pin" class="col-4 mt-2">
                    <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
                   
                            <i class="fa fa-barcode font-18"></i>
                        </span>
                        <p class="mb-0 pt-1 font-11">Data Pin</p>
                    </a>
                    

            <a href="profile" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
                
                    <i class="fa fa-address-card  font-18"></i>
                </span>
                <p class="mb-0 pt-1 font-11">Profile</p>
            </a>
            
            <a href="contact-us" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>">
           
                    <i class="fa fa-envelope font-18"></i>
                </span>
                <p class="mb-0 pt-1 font-11">Contact</p>
            </a>

            


        <a href="#agent-upgrade-modal" id="upgrade-agent-btn" data-menu="agent-upgrade-modal" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>;">
                <i class="fa fa-address-book font-18"></i>
            </span>
            <p class="mb-0 pt-1 font-11">Vendor</p>
        </a>

        <a href="logout" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>;">
                <i class="fa fa-lock font-18" aria-hidden="true"></i>
            </span>
            <p class="mb-0 pt-1 font-11">Logout</p>
        </a>

        <a href="2bank" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>;">
                <i class="fa fa-arrow-down font-18"></i>
            </span>
            <p class="mb-0 pt-1 font-11">Withdraw</p>
        </a>

        <a href="https://wa.me/message/ODTG4C4AA5R6E1" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>;">
                <i class="fa fa-user-secret font-18"></i>
            </span>
            <p class="mb-0 pt-1 font-11">Upgrade To API User</p>
        </a>

        <a href="apidocumentation" class="col-4 mt-2">
            <span class="icon icon-l shadow-l rounded-sm" style="color:<?php echo $sitecolor; ?>; background: #<?php echo $color; ?>;">
                <i class="fa fa-code font-18"></i>
            </span>
            <p class"mb-0 pt-1 font-11">Developer's API</p>
        </a>
    </div>
</div>
</div>
<div class="mt-3 splide single-slider slider-no-arrows slider-no-dots splide--loop splide--ltr splide--draggable is-active mb-1" id="single-slider-1" style="visibility: visible; margin-bottom: 25px;">
    <div class="splide__arrows">
        <!-- Your navigation arrows here -->
    </div>
    <div class="splide__track" id="single-slider-1-track">
        <div class="splide__list" id="single-slider-1-list" style="transform: translateX(-624px);">
            <div class="splide__slide splide__slide--clone" aria-hidden="true" tabindex="-1" style="width: 156px;">
                <div class="card card-style bg-20" data-card-height="90" style="height: 90px;">
                    <img class="img-fluid" style="height: 90px;" src="../../assets/img/ads/ads6b.png" />
                </div>
            </div>
            <div class="splide__slide" id="single-slider-1-slide02" aria-hidden="true" tabindex="-1" style="width: 156px;">
                <div class="card card-style bg-20" data-card-height="90" style="height: 90px;">
                    <img class="img-fluid" style="height: 90px;" src="../../assets/img/ads/ads6b.png" />
                </div>
            </div>
            <div class="splide__slide" id="single-slider-1-slide03" aria-hidden="true" tabindex="-1" style="width: 156px;">
                <div class="card card-style bg-20" data-card-height="90" style="height: 90px;">
                    <img class="img-fluid" style="height: 90px;" src="../../assets/img/ads/ads6b.png" />
                </div>
            </div>
        </div>
    </div>
</div>
</div>