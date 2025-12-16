<div class="page-content header-clear-medium">
        
        
        <div class="card card-style bg-theme pb-0">
            <div class="content" id="tab-group-1">
                <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight">
                    <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#tab-1">Profile</a>
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-2">Password</a>
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-3">Pin</a>
                </div>
                <div class="clearfix mb-3"></div>
                <div data-bs-parent="#tab-group-1" class="collapse show" id="tab-1">
                    <p class="mb-n1 color-highlight font-600 font-12">Account Details</p>
                        <h4>Basic Information</h4>
                        
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <i class="fa font-14 fa-user rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Name: </b> <?php echo $data->sFname. " " . $data->sLname; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#">
                                <i class="fa font-14 fa-envelope rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Email: </b> <?php echo $data->sEmail; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#"> 
                                <i class="fa font-14 fa-phone rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Phone: </b> <?php echo $data->sPhone; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#">
                                <i class="fa font-14 fa-globe rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>State: </b> <?php echo $data->sState; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>   
                                        
                        </div>

                        <p class="mb-n1 mt-2 color-highlight font-600 font-12">Referral</p>
                        <h4>Referral Link</h4>
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <input type="text" class="form-control" readonly value="<?php echo $siteurl."mobile/register/?referral=".$data->sPhone; ?>" />
                            </a>
                            <a href="#">
                                <button class="btn btn-danger btn-sm" onclick="copyToClipboard('<?php echo $siteurl."mobile/register/?referral=".$data->sPhone; ?>')">Copy Link</button>
                                <button class="btn btn-success btn-sm" onclick="window.open('referrals')">View Commission</button>
                            </a>
                        </div>
                        
                        <?php if($data->sType == 3): ?>
                        <p class="mb-n1 mt-2 color-highlight font-600 font-12">Developer</p>
                        <h4>Api Documentation</h4>
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <input type="text" class="form-control" readonly value="<?php echo $data->sApiKey; ?>" />
                            </a>
                            <a href="#">
                                <button class="btn btn-danger btn-sm" onclick="copyToClipboard('<?php echo $data->sApiKey; ?>')">Copy Api Key</button>
                                <?php if(!empty($data2)): ?>
                                    <button class="pl-5 btn btn-success btn-sm" onclick="window.open('<?php echo $data2->apidocumentation; ?>')">View Documentation</button>
                                <?php endif; ?>
                            </a>
                         </div>
                         <?php endif; ?>
                        
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-2">
                    <p class="mb-n1 color-highlight font-600 font-12">Update Login Details</p>
                        <h4>Login Details</h4>
                        
                        <form id="passForm" method="post">
                        <div class="mt-5 mb-3">
                            
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="password" class="form-control" id="old-pass" name="oldpass" placeholder="Old Password" required>
                                <label for="old-pass" class="color-highlight">Old Password</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="password" class="form-control" id="new-pass" name="newpass" placeholder="New Password" required>
                                <label for="new-pass" class="color-highlight">New Password</label>
                                <em>(required)</em>
                            </div>

                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="password" class="form-control" id="retype-pass" placeholder="Retype Password" required>
                                <label for="retype-pass" class="color-highlight">Retype Password</label>
                                <em>(required)</em>
                            </div>
                        </div>
                        <button type="submit" id="update-pass-btn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Update Password
                        </button>
                        </form>
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-3">
                    <p class="mb-n1 color-highlight font-600 font-12">Update Transaction Pin</p>
                        <h4>Transaction Pin</h4>
                        
                        <form id="pinForm" method="post">
                        <div class="mt-3 mb-3">
                            <p class="text-danger"><b>Note: </b> The Default Transaction Pin Is '1234'. Your Transaction Pin should be a four digit number. </p>
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="number" class="form-control" id="old-pin" name="oldpin" placeholder="Old Pin" required>
                                <label for="old-pin" class="color-highlight">Old Pin</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="number"  class="form-control" id="new-pin" name="newpin" placeholder="New Pin" required>
                                <label for="new-pin" class="color-highlight">New Pin</label>
                                <em>(required)</em>
                            </div>

                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="number" class="form-control" id="retype-pin" placeholder="Retype Pin" required>
                                <label for="retype-pin" class="color-highlight">Retype Pin</label>
                                <em>(required)</em>
                            </div>
                        </div>
                        <button type="submit" id="update-pin-btn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Update Pin
                        </button>
                        </form>
                        
                        <hr/>

                        <p class="mb-n1 color-highlight font-600 font-12">Disable Transaction Pin</p>
                        <h4>Disable Pin</h4>
                        
                        <form class="the-submit-form" method="post">
                        <div class="mt-3 mb-3">
                            <p class="text-danger"><b>Note: </b> Only Disable Pin When You Are Sure About The Security Of Your Phone And Your Account Is Secured With A Strong Password. </p>
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="number" maxlength="4" class="form-control" id="old-pin" name="oldpin" placeholder="Old Pin" required>
                                <label for="old-pin" class="color-highlight">Old Pin</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <select name="pinstatus">
                                    <option value="">Change Status</option>
                                    <?php if ($data->sPinStatus == 0): ?>
                                    <option value="0" selected>Enable</option> <option value="1">Disable</option>
                                    <?php else : ?>
                                    <option value="0">Enable</option> <option value="1" selected>Disable</option>
                                    <?php endif; ?>
                                </select><label for="new-pin" class="color-highlight">Change Status</label>
                                <em>(required)</em>
                            </div>
</div>
                        <button type="submit" name="disable-user-pin" style="width: 100%;" class="the-form-btn btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Update Pin
                        </button>
                        </form>
                </div>
                
            </div>
        </div> 

</div>

