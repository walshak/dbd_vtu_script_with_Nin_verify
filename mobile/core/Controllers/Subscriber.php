<?php

	class Subscriber extends Controller{
		
		public  $userId;
		public  $loginAccount;
		
		public $pageCount=1; 
		public $nextPage=2; 
		public $previousePage=0; 
		public $thePostCat=""; 
		public $limit=0;
		public $noFetch=1;
		public $siteurl;
		
		
		protected $model;
 
		//Default Constructor
		public function __construct(){
			
			global $siteurl;
			$this->siteurl = $siteurl;
			
			if(isset($_COOKIE['loginId']) && isset($_SESSION['loginId'])){
				if($_COOKIE['loginId']!=''){
					
					//Set User Data
					$this->userId=(float) base64_decode($_COOKIE["loginId"]);
					$this->loginAccount=(float) base64_decode($_COOKIE["loginAccount"]);
					
					//Pagination
					if(isset($_GET["category"])){$this->thePostCat=$_GET["category"]; }
					if(isset($_GET["page"])){
						$this->pageCount=(float) $_GET["page"];
						$this->nextPage=$this->pageCount + 1; $this->previousePage=$this->pageCount - 1; 
					}
					
					//Setting Fetch Limit
					$this->limit=$this->pageCount - 1; 
					$this->limit=$this->limit * $this->noFetch;
					
					//Initialize Model Class
					$this->model=new SubscriberModel;
					$this->subscribe();

				}
				else{ header("Location: ../"); exit();}
			}
			else{ 
				//Login Exception For Contact Message From
				if(isset($_GET["save-message"])){$this->model=new SubscriberModel;} 
				elseif(isset($_GET["settings"])){$this->model=new SubscriberModel;} 
				else {header("Location: ../"); exit();}
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Account & Profile Management
		//----------------------------------------------------------------------------------------------------------------

		//Logout Users From System
		public function logoutUser(){
			session_start();
			session_destroy();
			setcookie("loginId", "", time() - 3600, "/");
			setcookie("loginVisit", "", time() - 3600, "/");
			setcookie("loginAccount", "", time() - 3600, "/");
			setcookie("loginState", "", time() - 3600, "/");
			header("Location: ../");
			exit();
		}
 
		//Record Traffic
		public function recordTraffic(){
			$data= $this->model->recordTraffic();
			return $data;
		}

		//Record Last Activity
		public function recordLastActivity(){
			$data= $this->model->recordLastActivity($this->userId);
			if($data == 1){
				$this->logoutUser();
			}
			return $data;
		}


		//Get Profile Info
		public function getProfileInfo(){
			$data= $this->model->getProfileInfo($this->userId);
			return $data;
		}

		//Update Account Password
		public function updateProfileKey(){
			extract($_POST);
			$check=$this->model->updateProfileKey($this->userId,$oldpass,$newpass);
			return $check;
		}

		//Disable User Pin
		public function disableUserPin(){
			extract($_POST);
			$check=$this->model->disableUserPin($this->userId,$oldpin,$pinstatus);
			if($check == 0){return $this->createPopMessage("Success!!","Pin Disabled Successfully","green");}
			elseif($check == 1){return $this->createPopMessage("Error!!","Invalid Pin Provided.","red");}
			else{return $this->createPopMessage("Error!!","Unable To Disable Pin, Please Try Again Later.","red");}
			return $check;
		}

		//Update Account Pin
		public function updateTransactionPin(){
			extract($_POST);
			$check=$this->model->updateTransactionPin($this->userId,$oldpin,$newpin);
			return $check;
		}

		//----------------------------------------------------------------------------------------------------------------
		
//-----------------------------—---------------------
		// Buy Airtime
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Network
		public function getNetworks(){
			$check=$this->model->getNetworks();
			return $check;
		}

		//Get Airtime Discount
		public function getAirtimeDiscount(){
			$check=$this->model->getAirtimeDiscount();
			return $check;
		}

		//Purchase Airtime
		public function purchaseAirtime(){
			extract($_POST);
			$host = $this->siteurl."/api/airtime/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(isset($_POST["ported_number"])){
			    if($_POST["ported_number"] == "on"){$ported_number = "true";}
			}
			
			if(is_object($check)){
				
				//Purchase Airtime
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"network": "'.$network.'",
						"amount": "'.$amount.'",
						"phone": "'.$phone.'",
						"ported_number":"'.$ported_number.'",
						"ref" : "'.$transref.'",
						"airtime_type": "'.$networktype.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Buy Recharge Card
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Recharge Pin Discount
		public function getRechargePinDiscount(){
			$check=$this->model->getRechargePinDiscount();
			return $check;
		}

		//Purchase Recharge Card Pin
		public function purchaseRechargePin(){
			extract($_POST);
			$host = $this->siteurl."/api/rechargepin/";
			
       function generateTransref() {
    // Generate a random string for uniqueness (you can modify the length as needed)
    $randomString = bin2hex(random_bytes(8));

    // Get the current timestamp (Unix timestamp)
    $timestamp = time();

    // Concatenate the timestamp and random string to create the transaction reference
    $transref = "TR-" . $timestamp . "-" . $randomString;

    return $transref;
};
       $transre = generateTransref();



			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(isset($_POST["ported_number"])){
			    if($_POST["ported_number"] == "on"){$ported_number = "true";}
			}
			
			if(is_object($check)){
				
				//Purchase Airtime
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
					  "ref" : "'.$transre.'", 
						"network": "'.$network.'",
						"amount": "'.$amount.'",
						"quantity": "'.$quantity.'",
						
						"name_on_card":"'.$businessname.'"
						
						
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
					"Authorization: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transre");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"orange");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Alpha Topup Plan
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Alpha Topup
		public function getAlphaTopupPlans(){
			$data=$this->model->getAlphaTopupPlans();
			return $data;
		}

		// ------------------------------------------
		//Purchase Alpha Topup
		// ------------------------------------------
		
		
		//Purchase Alpha Topup API
		public function purchaseAlphaTopup(){
			extract($_POST);
			$host = $this->siteurl."/api/alphatopup/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(is_object($check)){
				
				//Purchase Airtime
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"amount": "'.$alphaplan.'",
						"phone": "'.$phone.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Buy Data Plan
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Data Plans
		public function getDataPlans(){
			$check=$this->model->getDataPlans();
			return $check;
		}

		//Get Data Pins
		public function getDataPins(){
			$check=$this->model->getDataPins();
			return $check;
		}

		//Get Data Pins
		public function getDataPinTokens(){
			if(!isset($_GET["ref"])){header("Location: ./"); exit(); }
			$check=$this->model->getDataPinTokens($this->userId,$_GET["ref"]);
			return $check;
		}
			//Get Recharge Pins
		public function getRechargePinTokens(){
			if(!isset($_GET["ref"])){header("Location: ./"); exit(); }
			$check=$this->model->getRechargePinTokens($this->userId,$_GET["ref"]);
			return $check;
		}

		//Purchase Data
		public function purchaseData(){
			extract($_POST);
			$host = $this->siteurl."/api/data/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(isset($_POST["ported_number"])){
			    if($_POST["ported_number"] == "on"){$ported_number = "true";}
			}
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"network": "'.$network.'",
						"phone": "'.$phone.'",
						"ported_number":"'.$ported_number.'",
						"ref" : "'.$transref.'",
						"data_plan": "'.$dataplan.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);

				//exit(); 
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//Purchase Data
		public function purchaseDataPin(){
			extract($_POST);
			$host = $this->siteurl."/api/datapin/" ;

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(isset($_POST["ported_number"])){
			    if($_POST["ported_number"] == "on"){$ported_number = "true";}
			}
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"network": "'.$network.'",
						"quantity": "'.$quantity.'",
						"businessname":"'.$businessname.'",
						"data_plan": "'.$datapinplan.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				print_r($result);
				var_dump($result);
			

				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Upgrade To Agent
		//----------------------------------------------------------------------------------------------------------------
		
		//Upgrade To Agent
		public function upgradeToAgent(){
			extract($_POST);
			$transref = $this->generateTransactionRef();
			$check=$this->model->upgradeToAgent($this->userId,$kpin,$transref);
			if($check == 0){return $this->createPopMessage("Success!!","Account Upgraded. You are now an Agent.","green");}
			elseif($check == 1){return $this->createPopMessage("Error!!","Invalid Transaction Pin.","red");}
			elseif($check == 2){return $this->createPopMessage("Error!!","You Are Already An Agent","red");}
			elseif($check == 3){return $this->createPopMessage("Error!!","Insufficent Balance","red");}
			else{return $this->createPopMessage("Error!!","Unexpected Error: Could Not Upgrade Account","red");}
		}

		//Upgrade To Vendor
		public function upgradeToVendor(){
			extract($_POST);
			$transref = $this->generateTransactionRef();
			$check=$this->model->upgradeToVendor($this->userId,$kpin,$transref);
			if($check == 0){return $this->createPopMessage("Success!!","Account Upgraded. You are now a Vendor.","green");}
			elseif($check == 1){return $this->createPopMessage("Error!!","Invalid Transaction Pin.","red");}
			elseif($check == 2){return $this->createPopMessage("Error!!","You Are Already An Agent","red");}
			elseif($check == 3){return $this->createPopMessage("Error!!","Insufficient Balance","red");}
			else{return $this->createPopMessage("Error!!","Unexpected Error: Could Not Upgrade Account","red");}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Site Settings
		//----------------------------------------------------------------------------------------------------------------
		
		public function getSiteSettings(){
			$data=$this->model->getSiteSettings();
			return $data;
		}

		public function getApiConfiguration(){
			$data=$this->model->getApiConfiguration();
			return $data;
		}


		//----------------------------------------------------------------------------------------------------------------
		// Exam Pins
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Exam Provider
		public function getExamProvider(){
			$data=$this->model->getExamProvider();
			return $data;
		}
		
		//Purchase Exam Pin Token
		public function purchaseExamPinToken(){
			extract($_POST);
			$host = $this->siteurl."/api/exam/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"provider": "'.$provider.'",
						"quantity": "'.$quantity.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!","Server Error: ".$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		//----------------------------------------------------------------------------------------------------------------
		// Electricity
		//----------------------------------------------------------------------------------------------------------------
		 
		//Get All Electricity Provider
		public function getElectricityProvider(){
			$data=$this->model->getElectricityProvider();
			return $data;
		}

		//Validate Meter Number
		public function validateMeterNumber(){
			extract($_POST);
			$host = $this->siteurl."/api/electricity/verify/";
			$data = $this->getProfileInfo();
			
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $host,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'{
					"provider": "'.$provider.'",
					"meternumber": "'.$meternumber.'",
					"metertype": "'.$metertype.'"
				}',
					
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"Token: Token $data->sApiKey"
				),
			));

			$exereq = curl_exec($curl);
			$result=json_decode($exereq);
			curl_close($curl);
				
			if($result->status == "success"){
				return $result->msg;
			}
			else{
				return $result->msg;
			}
			
		}

		//Purchase Electricity Token
		public function purchaseElectricityToken(){
			extract($_POST);
			$host = $this->siteurl."/api/electricity/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"provider": "'.$provider.'",
						"phone": "'.$phone.'",
						"metertype": "'.$metertype.'",
						"meternumber": "'.$meternumber.'",
						"ref" : "'.$transref.'",
						"amount": "'.$amount.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!","Server Error: ".$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Cable TV
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Cable Provider
		public function getCableProvider(){
			$data=$this->model->getCableProvider();
			return $data;
		}
		
		//Get Cable Plan
		public function getCablePlans(){
			$data=$this->model->getCablePlans();
			return $data;
		}

		//Purchase Cable Tv
		public function purchaseCableTv(){
			extract($_POST);
			$host = $this->siteurl."/api/cabletv/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $host,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"provider": "'.$provider.'",
						"phone": "'.$phone.'",
						"subtype": "'.$subtype.'",
						"iucnumber": "'.$iucnumber.'",
						"ref" : "'.$transref.'",
						"plan": "'.$cableplan.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!","Server Error, Please Contact Admin","red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//Validate ICU Number
		public function validateIUCNumber(){
			extract($_POST);
			$host = $this->siteurl."/api/cabletv/verify/";
			$data = $this->getProfileInfo();
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $host,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'{
					"provider": "'.$provider.'",
					"iucnumber": "'.$iucnumber.'"
				}',
					
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"Token: Token $data->sApiKey"
				),
			));

			$exereq = curl_exec($curl);
			$result=json_decode($exereq);
			curl_close($curl);
			
			if($result->status == "success"){
				return $result->msg;
			}
			else{
				return $result->msg;
			}
			
		}


		//----------------------------------------------------------------------------------------------------------------
		// Transaction Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Network
		public function getAllTransaction($limit){
			$check=$this->model->getAllTransaction($this->userId,$limit);
			return $check;
		}
		
		//Generate Transaction Reference
		public function generateTransactionRef(){
			$tranId=rand(1000,9999).time();
			return $tranId;
		}

		
		//Get Transaction Details
		public function getTransactionDetails(){
			if(!isset($_GET["ref"])){header("Location: ./"); exit(); }
			$data=$this->model->getTransactionDetails($_GET["ref"]);
			return $data;
		}

		

		//Initialize Paystack Payment
		public function fundWithPaystack(){
			extract($_POST);
			$data=$this->model->initializePayStack($this->siteurl,$email,$amount);
			if($data["status"] == 'success'){$link = $data["msg"]; header("Location: $link"); exit();}
			return $this->createPopMessage("Opps!!",$data["msg"],"red"); 
		}

		//----------------------------------------------------------------------------------------------------------------
		// Wallet Transfer Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Perform Funds Transfer
		public function performFundsTransfer(){
			extract($_POST);
			$amount = (float) $amount;
			$amounttopay = 0;
			$transref1 = $this->generateTransactionRef();
			$transref2 = $transref1 .":".$this->generateTransactionRef();

			if($amount < 0){
			    return $this->createPopMessage("Error!!","Not Enough Balance. Please Try Again.","red"); 
			}
			
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			if(is_object($check)){
				if($transfertype == "wallet-wallet"): 
					$amounttopay = $amount;
					$data=$this->model->performWalletTransfer($this->userId,$email,$amount,$amounttopay,$transref1,$transref2); 
					if($data == 0){ header("Location: transaction-details?ref=$transref1"); return 0;}
					elseif($data == 2){	return $this->createPopMessage("Error!!","Receiver Email {$email} Does Not Exist. Please Try Again.","red"); }
					elseif($data == 3){	return $this->createPopMessage("Error!!","Not Enough Balance. Please Try Again.","red"); }
					elseif($data == 4){	return $this->createPopMessage("Error!!","Could Not Update Wallet, Please Try Again.","red"); }
					elseif($data == 5){	return $this->createPopMessage("Opps!!","You Cannot Perform Wallet Transfer To The Same User Wallet.","red"); }
					else{return $this->createPopMessage("Error!!","Unexpected Error, Please Tray Again Later","red"); }
				endif;
				
				if($transfertype == "referral-wallet"): 
					$amounttopay = $amount;
					$data = $this->model->performReferralTransfer($this->userId,$amount,$amounttopay,$transref1,$transref2);
					if($data == 0){ header("Location: transaction-details?ref=$transref1"); return 0;}
					elseif($data == 3){	return $this->createPopMessage("Error!!","Not Enough Balance. Please Try Again.","red"); }
					elseif($data == 4){	return $this->createPopMessage("Error!!","Could Not Update Wallet, Please Try Again","red"); }
					else{return $this->createPopMessage("Error!!","Unexpected Error, Please Tray Again Later","red"); }
				endif;
				
				return $this->createPopMessage("Error!!","Could Not Perform Operation, Please Try Again Later.","red");
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		

		//----------------------------------------------------------------------------------------------------------------
		// Contact Message
		//----------------------------------------------------------------------------------------------------------------
		
		//Post Form Contact Message
		public function postContact(){
			extract($_POST);
			$check=$this->model->postContact($name,$email,$subject,$message);
			return $check;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Notification
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Notification
		public function getAllNotification(){
			$data=$this->model->getAllNotification($this->loginAccount);
			return $data;
		}

		//Get Home Notification
		public function displayHomeNotification(){
			$data=$this->model->getHomeNotification();
			$subject = addslashes(str_replace("\r","",$data->subject));
			$subject = str_replace("\n","",$subject);
			$message = addslashes(str_replace("\r","",$data->message));
			$message = str_replace("\n","",$message);
			return "swal('{$subject}','{$message}','info');";
			return json_encode(array("subject" => $subject, "message" => $message));
		}

		//----------------------------------------------------------------------------------------------------------------
		// Email Verification
		//----------------------------------------------------------------------------------------------------------------
		//Verify Email
		public function verifyUserMail(){
			extract($_POST);
			$verifyRecoveryCode = new AccountAccess;
            $status = $verifyRecoveryCode->verifyRecoveryCode();

			if($status == 0){
				$this->model->updateEmailVerificationStatus($this->userId);
				header("Location: homepage?msg=Email Verification Successful");
				exit();
			}
			elseif($status == 1){return $this->createPopMessage("Error!!","Wrong Verification Code","red");}
			else{return $this->createPopMessage("Error!!","Email Verification Failed","red");}
	
		}


		//Format Description
		public function formatDescription($data){
			$data=str_replace("\n\r", "<br/>", $data);
			return $data;
		}

		//Format Status
		public function formatStatus($status){
			if($status == 0){return "<b class='text-success'>Transaction Successful</b>";}
			elseif($status == 2){return "<b class='text-primary'>Transaction Processing</b>";}
			elseif($status == 5){return "<b class='text-primary'>Transaction Processing</b>";}
			else{return "<b class='text-danger'>Failed Transaction</b>";}
		}

		//Create Message Pop
		public function createPopMessage($heading,$message,$color){
			//Color is green or red for success and error respectively
			$msg='
			<div id="gen-message-box" class="menu menu-box-bottom bg-'.$color.'-dark rounded-m" data-menu-height="335" data-menu-effect="menu-over">
					<h1 class="text-center mt-4"><i class="fa fa-3x fa-times-circle scale-box color-white shadow-xl rounded-circle"></i></h1>
					<h1 class="text-center mt-3 text-uppercase color-white font-700">'.$heading.'</h1>
					<p class="boxed-text-l color-white opacity-70">
							'.$message.'
					</p>
					<a href="#" class="close-menu btn btn-m btn-center-l button-s shadow-l rounded-s text-uppercase font-600 bg-white color-black">Close</a>
			</div>
			';
        
			return $msg;
		}

		public function createPopMessage2($heading,$message){
			$msg='
			<div id="gen-message-box" class="menu menu-box-modal rounded-m" data-menu-height="400" style="display: block; width: 97%; height: 400px;">
				<h1 class="text-center mt-4"><i class="fa fa-3x fa-info-circle scale-box color-blue-dark shadow-xl rounded-circle"></i></h1>
				<h3 class="text-center mt-3 font-700">'.$heading.'</h3>
				<p class="boxed-text-xl text-dark">
					'.$message.'
				</p>
				<div class="row mb-0 me-3 ms-3">
					<div class="col-6">
						<a href="#" class="btn close-menu btn-full btn-m color-red-dark border-red-dark font-600 rounded-s">Okay</a>
					</div>
					<div class="col-6">
						<a href="notifications" class="btn btn-full btn-m color-green-dark border-green-dark font-600 rounded-s">View More</a>
					</div>
				</div>
			</div>
			';
        
			return $msg;
		}

		public function subscribe(){
			if(!file_exists('../../core/helpers/vendor/manifest.php')){
				$resp="PGgxIHN0eWxlPSdjb2xvcjpyZWQ7Jz5JbGxlZ2FsIFVzZSBPZiBTb2Z0d2FyZSBEZXRlY3RlZC4gPC9oMT4KICAgICAgICAgICAgPGgyPgogICAgICAgICAgICAgICAgWW91ciBJbmZvcm1hdGlvbiBIYXZlIEJlZW4gU3VibWl0dGVkIFRvIE91ciBTZXJ2ZXIuIAogICAgICAgICAgICAgICAgPGJyLz4KICAgICAgICAgICAgICAgIFlvdSBIYXZlIDQ4IEhvdXJzIFRvIFBheSBBIEZpbmUgT2YgTjUwLDAwMCBGb3IgVXNpbmcgT3VyIFNvZnR3YXJlIFdpdGhvdXQgQSBMaWNlbnNlLiAKICAgICAgICAgICAgICAgIDxici8+CiAgICAgICAgICAgICAgICBGYWlsdXJlIFRvIERvIFNvLCBMZWdhbCBNZWFzdXJlcyBXb3VsZCBCZSBUYWtlbiBPbiBZb3UuIAogICAgICAgICAgICA8L2gyPgogICAgICAgICAgICA8aDMgc3R5bGU9J2NvbG9yOnJlZDsnPgogICAgICAgICAgICA8YSBocmVmPSdodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHAnPgogICAgICAgICAgICBodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHA8L2E+IEZvciBNb3JlIERldGFpbHMuCiAgICAgICAgICAgIDwvaDM+"; echo base64_decode($resp); exit();
			}
			if(!file_exists('../../core/helpers/vendor/site.php')){
				$resp="PGgxIHN0eWxlPSdjb2xvcjpyZWQ7Jz5JbGxlZ2FsIFVzZSBPZiBTb2Z0d2FyZSBEZXRlY3RlZC4gPC9oMT4KICAgICAgICAgICAgPGgyPgogICAgICAgICAgICAgICAgWW91ciBJbmZvcm1hdGlvbiBIYXZlIEJlZW4gU3VibWl0dGVkIFRvIE91ciBTZXJ2ZXIuIAogICAgICAgICAgICAgICAgPGJyLz4KICAgICAgICAgICAgICAgIFlvdSBIYXZlIDQ4IEhvdXJzIFRvIFBheSBBIEZpbmUgT2YgTjUwLDAwMCBGb3IgVXNpbmcgT3VyIFNvZnR3YXJlIFdpdGhvdXQgQSBMaWNlbnNlLiAKICAgICAgICAgICAgICAgIDxici8+CiAgICAgICAgICAgICAgICBGYWlsdXJlIFRvIERvIFNvLCBMZWdhbCBNZWFzdXJlcyBXb3VsZCBCZSBUYWtlbiBPbiBZb3UuIAogICAgICAgICAgICA8L2gyPgogICAgICAgICAgICA8aDMgc3R5bGU9J2NvbG9yOnJlZDsnPgogICAgICAgICAgICA8YSBocmVmPSdodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHAnPgogICAgICAgICAgICBodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHA8L2E+IEZvciBNb3JlIERldGFpbHMuCiAgICAgICAgICAgIDwvaDM+"; echo base64_decode($resp); exit();
			}
		}
		
 

	}

?>
