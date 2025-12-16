
<div class="d-flex justify-content-between">
<a class="btn btn-success btn-block mr-2" href="api-setting">General Setting</a> 
<a class="btn btn-primary btn-block ml-2 mt-0" href="monnify-setting">Monnify Setting</a>
<a class="btn btn-info btn-block ml-4 mt-0" href="paystack-setting">Paystack Setting</a>
</div>
<hr/>

<div class="row">
<div class="col-12">
<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>Note: </strong> <br/> This is a restricted area. You need to obtain an access code to perform this operation. Before adding any link here please study the api documentation or contact Topupmate Technology Admin for assistance.
        </div>
    <div class="box">
        <div class="box-header with-border">
          <h4 class="box-title">Manage API Settings</h4>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        
        <form  method="post" class="form-submit">
                    
               
                <div class="form-group">
                    <label for="success" class="control-label">Access Code</label>
                    <div class="">
                    <input type="number" name="code" placeholder="Access Code" class="form-control" required />
                    </div>
                </div>
                <?php echo "AP" . date("Hymd") . date("d"); ?>

                <div class="form-group">
                    <label for="success" class="control-label">Api Provider Name</label>
                    <div class="">
                    <input type="text" name="providername" placeholder="Api Provider Name" class="form-control" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Api Url</label>
                    <div class="">
                    <input type="text" name="providerurl" placeholder="Api Url" class="form-control" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Service</label>
                    <div class="">
                        <select name="service" class="form-control" required>
                            <option value="">Select Service</option>
                            <option value="Wallet">Wallet</option>
                            <option value="Airtime">Airtime</option>
                            <option value="Data">Data</option>
                            <option value="CableVer">Cable Verification</option>
                            <option value="Cable">Cable</option>
                            <option value="ElectricityVer">Electricity Verification</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Exam">Exam</option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <div class="">
                       <button type="submit" name="add-new-api-details" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>