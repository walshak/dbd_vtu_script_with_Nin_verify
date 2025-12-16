
<div class="d-flex justify-content-between">
    <a class="btn btn-dark btn-block mr-2" href="site-setting">General Setting</a> 
    <a class="btn btn-primary btn-block ml-2 mt-0" href="contact-setting">Contact Setting</a>
    <a class="btn btn-info btn-block ml-4 mt-0" href="network-setting">Network Setting</a>
</div>
<hr/>
<div class="row">
<div class="col-12">
    
    <div class="box">
    <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h4 class="box-title">General Settings</h4>
			  <a class="btn btn-primary btn-rounded text-white" href="website-style">
				  <i class="fa fa-eye" aria-hidden="true"></i> Website Style
			  </a>
            </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
                    
               <div class="form-group">
                    <label for="success" class="control-label">Website Name</label>
                    <div class="">
                    <input type="text" name="sitename" value="<?php echo $data->sitename; ?>" class="form-control" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Website Url</label>
                    <div class="">
                    <input type="text" name="siteurl" value="<?php echo $data->siteurl; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Api Documentation Link</label>
                    <div class="">
                    <input type="text" name="apidocumentation" value="<?php echo $data->apidocumentation; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Bank Name (For Manual Funding)</label>
                    <div class="">
                    <input type="text" name="bankname" value="<?php echo $data->bankname; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Account Name (For Manual Funding)</label>
                    <div class="">
                    <input type="text" name="accountname" value="<?php echo $data->accountname; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Account Number (For Manual Funding)</label>
                    <div class="">
                    <input type="number" name="accountno" value="<?php echo $data->accountno; ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Electricity Charges</label>
                    <div class="">
                    <input type="text" name="electricitycharges" value="<?php echo $data->electricitycharges; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Airtime Purchase (Minimum)</label>
                    <div class="">
                    <input type="text" name="airtimemin" value="<?php echo $data->airtimemin; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Airtime Purchase (Maximum)</label>
                    <div class="">
                    <input type="text" name="airtimemax" value="<?php echo $data->airtimemax; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Agent Upgrade Fee</label>
                    <div class="">
                    <input type="text" name="agentupgrade" value="<?php echo $data->agentupgrade; ?>" class="form-control" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Vendor Upgrade Fee</label>
                    <div class="">
                    <input type="text" name="vendorupgrade" value="<?php echo $data->vendorupgrade; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Wallet to Wallet Transfer Charges</label>
                    <div class="">
                    <input type="text" name="wallettowalletcharges" value="<?php echo $data->wallettowalletcharges; ?>" class="form-control" required="required">
                    </div>
                </div> 

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Account Upgrade)</label>
                    <div class="">
                    <input type="text" name="referalupgradebonus" value="<?php echo $data->referalupgradebonus; ?>" class="form-control" required="required">
                    </div>
                </div> 

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Airtime Purchase)</label>
                    <div class="">
                    <input type="text" name="referalairtimebonus" value="<?php echo $data->referalairtimebonus; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Data Purchase)</label>
                    <div class="">
                    <input type="text" name="referaldatabonus" value="<?php echo $data->referaldatabonus; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Cable Tv)</label>
                    <div class="">
                    <input type="text" name="referalcablebonus" value="<?php echo $data->referalcablebonus; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Exam Pin)</label>
                    <div class="">
                    <input type="text" name="referalexambonus" value="<?php echo $data->referalexambonus; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Electricity Bill)</label>
                    <div class="">
                    <input type="text" name="referalmeterbonus" value="<?php echo $data->referalmeterbonus; ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Wallet Funding)</label>
                    <div class="">
                    <input type="text" name="referalwalletbonus" value="<?php echo $data->referalwalletbonus; ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="">
                       <button type="submit" name="update-site-setting" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>



