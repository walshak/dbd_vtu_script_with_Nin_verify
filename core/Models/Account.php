<?php

class Account extends Model
{

	//Verify Admin Login Deatils
	public function verifyAdminAccount($uname, $pass)
	{
		$sql = "SELECT sysId,sysName,sysStatus,sysUsername,sysRole FROM sysusers WHERE sysUsername=:uname AND sysToken=:password";
		$query = self::$dbh->prepare($sql);
		$query->bindParam(':uname', $uname, PDO::PARAM_STR);
		$query->bindParam(':password', $pass, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);

		if ($query->rowCount() > 0) {
			if ($result["sysStatus"] <> 0) {
				return 2;
			}
			$_SESSION['sysUser'] = $result["sysUsername"];
			$_SESSION['sysRole'] = $result["sysRole"];
			$_SESSION['sysName'] = $result["sysName"];
			$_SESSION['sysId'] = $result["sysId"];
			return 0;
		} else {
			return 1;
		}
	}

	public function verifyAdminAccount2()
	{
		$sql = "SELECT sysId,sysName,sysStatus,sysUsername,sysRole FROM sysusers";
		$query = self::$dbh->prepare($sql);
		$query->execute();
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	//Register/Create New User Account
	public function registerUser($fname, $lname, $email, $phone, $password, $state, $account, $referal, $transpin)
	{

		//if registration is done by admin, dont save cookies data
		if ($referal == "admin") {
			$saveCookies = FALSE;
			$referal = "";
		} else {
			$saveCookies = TRUE;
		}

		//Verify Registration Details
		$dbh = self::connect();
		$c = "SELECT sEmail,sPhone,sType FROM subscribers WHERE ";
		$c .= ($email <> "") ? " sEmail=:e OR sPhone=:p" : " sPhone=:p";
		$queryC = $dbh->prepare($c);
		if ($email <> "") {
			$queryC->bindParam(':e', $email, PDO::PARAM_STR);
		}
		$queryC->bindParam(':p', $phone, PDO::PARAM_STR);
		$queryC->execute();
		$result = $queryC->fetch(PDO::FETCH_ASSOC);
		$data = 4;

		//Output Error Message If Data Already Exist
		if ($queryC->rowCount() > 0) {

			if ($result["sPhone"] == $phone) {
				$data = 3;
			}
			if ($email <> "") {
				if ($result["sEmail"] == $email) {
					$data = 2;
				}
			}
			if ($result["sEmail"] == $email && $result["sPhone"] == $phone) {
				$data = 1;
			}

			//Code 1 Phone And Email Exist, Code 2 Email Exist, Code 3 Phone Exist

			return $data;
		}

		//Insert And Register Member
		else {

			$hash = substr(sha1(md5($password)), 3, 10);
			$apiKey = substr(str_shuffle("0123456789ABCDEFGHIJklmnopqrstvwxyzAbAcAdAeAfAgAhBaBbBcBdC1C23C3C4C5C6C7C8C9xix2x3"), 0, 60) . time();
			$varCode = mt_rand(2000, 9000);


			$sql = "INSERT INTO subscribers (sFname,sLname,sEmail,sPhone,sPass,sState,sType,sApiKey,sReferal,sPin,sVerCode,sRegStatus)VALUES(:fname,:lname,:email,:phone,:pass,:s,:a,:k,:ref,:pin,:code,0)";

			$query = $dbh->prepare($sql);

			$query->bindParam(':fname', $fname, PDO::PARAM_STR);
			$query->bindParam(':lname', $lname, PDO::PARAM_STR);
			$query->bindParam(':email', $email, PDO::PARAM_STR);
			$query->bindParam(':phone', $phone, PDO::PARAM_STR);
			$query->bindParam(':pass', $hash, PDO::PARAM_STR);
			$query->bindParam(':s', $state, PDO::PARAM_STR);
			$query->bindParam(':a', $account, PDO::PARAM_STR);
			$query->bindParam(':k', $apiKey, PDO::PARAM_STR);
			$query->bindParam(':ref', $referal, PDO::PARAM_STR);
			$query->bindParam(':pin', $transpin, PDO::PARAM_INT);
			$query->bindParam(':code', $varCode, PDO::PARAM_STR);
			$query->execute();

			$lastInsertId = $dbh->lastInsertId();
			if ($lastInsertId) {

				$data = 0;

				if ($saveCookies) {
					$_SESSION["loginId"] = $lastInsertId;
					$_SESSION["loginName"] = $fname . " " . $lname;
					$_SESSION["loginEmail"] = $email;
					$_SESSION["loginPhone"] = $phone;

					$loginId = base64_encode($lastInsertId);
					$loginState = base64_encode($state);
					$loginPhone = base64_encode($phone);
					$loginAccount = base64_encode("1");
					$loginName = base64_encode($fname);


					setcookie("loginId", $loginId, time() + (2592000 * 30), "/");
					setcookie("loginState", $loginState, time() + (2592000 * 30), "/");
					setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
					setcookie("loginPhone", $loginPhone, time() + (31540000 * 30), "/");
					setcookie("loginName", $loginName, time() + (31540000 * 30), "/");


					//Generate User Login Token
					$randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
					$userLoginToken = time() . $randomToken . mt_rand(100, 1000);

					//Set User Login Token
					$_SESSION["loginAccToken"] = $userLoginToken;

					//Save New User Login Token For One Device Login Check

					$sqlAc = "INSERT INTO userlogin (user,token) VALUES (:user,:token)";
					$queryAc = $dbh->prepare($sqlAc);
					$queryAc->bindParam(':user', $lastInsertId, PDO::PARAM_STR);
					$queryAc->bindParam(':token', $userLoginToken, PDO::PARAM_STR);
					$queryAc->execute();
				}

				//Get API Details
				$d = $this->getApiConfiguration();
				$a = $this->getSiteConfiguration();
				$monifyStatus = $this->getConfigValue($d, "monifyStatus");
				$monifyApi = $this->getConfigValue($d, "monifyApi");
				$monifySecrete = $this->getConfigValue($d, "monifySecrete");
				$monifyContract = $this->getConfigValue($d, "monifyContract");
				$adminEmail = $a->email;

				//If Monnify Is Active, Create Virtual Account For User
				if ($monifyStatus == "On") {
					$this->createVirtualBankAccount($lastInsertId, $fname, $lname, $phone, $email, $monifyApi, $monifySecrete, $monifyContract);
				}

				//Send Email To User
				$subject = "Welcome (" . $this->sitename . ")";
				$message = "Hi " . $fname . ", " . "Welcome to {$this->sitename}. At {$this->sitename}, you can access instant recharge of Airtime, Data Bundle, CableTv, Electricity Bill Payment and Airtime to Cash. More features such as buying and selling gift cards, wallet to wallet transfer, and wallet to bank transfer would be made available soon. Our customer support line is available to you 24/7. Stay connected.";
				$message .= "<h3>Use The Verification Code \"" . $varCode . "\" To Recover Your Account.</h3>";
				$check = self::sendMail($email, $subject, $message);

				//Send Email To Admin
				$subject2 = "New User Registration (" . $this->sitename . ")";
				$message2 = "Hi " . $this->sitename . ", " . "This is to notify you that a new user just registered on your platform. Please find the below details for your usage: ";
				$message2 .= "<h3>Name: $fname $lname <br/> Phone Number: $phone <br/> Email: $email <br> State: $state</h3>";
				$message2 .= "<br/><br/><br/> <i>Notification Powered By Topupmate Technology</i>";
				$check = self::sendMail($adminEmail, $subject2, $message2);
			} else {
				$data = 4;
			}

			return $data;
		}
	}

	//Login User Account
	public function loginUser($phone, $key)
	{

		//Verify Registration Details
		$dbh = self::connect();
		$hash = substr(sha1(md5($key)), 3, 10);
		$c = "SELECT sId,sFname,sLname,sEmail,sPass,sPhone,sState,sType,sRegStatus FROM subscribers WHERE sPhone=:ph AND sPass=:p";
		$queryC = $dbh->prepare($c);
		$queryC->bindParam(':ph', $phone, PDO::PARAM_STR);
		$queryC->bindParam(':p', $hash, PDO::PARAM_STR);
		$queryC->execute();
		$result = $queryC->fetch(PDO::FETCH_OBJ);
		if ($queryC->rowCount() > 0) {

			if ($result->sRegStatus == 1) {
				return 2;
			}

			$_SESSION["loginId"] = $result->sId;
			$_SESSION["loginName"] = $result->sFname . " " . $result->sLname;
			$_SESSION["loginEmail"] = $result->sEmail;
			$_SESSION["loginPhone"] = $result->sPhone;


			$loginId = base64_encode($result->sId);
			$loginState = base64_encode($result->sState);
			$loginAccount = base64_encode($result->sType);
			$loginPhone = base64_encode($result->sPhone);
			$loginName = base64_encode($result->sFname);

			setcookie("loginId", $loginId, time() + (2592000 * 30), "/");
			setcookie("loginState", $loginState, time() + (2592000 * 30), "/");
			setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
			setcookie("loginPhone", $loginPhone, time() + (31540000 * 30), "/");
			setcookie("loginName", $loginName, time() + (31540000 * 30), "/");

			//Generate User Login Token
			$randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
			$userLoginToken = time() . $randomToken . mt_rand(100, 1000);

			//Set User Login Token
			$_SESSION["loginAccToken"] = $userLoginToken;

			//Save New User Login Token For One Device Login Check

			$sqlAc = "INSERT INTO userlogin (user,token) VALUES (:user,:token)";
			$queryAc = $dbh->prepare($sqlAc);
			$queryAc->bindParam(':user', $result->sId, PDO::PARAM_STR);
			$queryAc->bindParam(':token', $userLoginToken, PDO::PARAM_STR);
			$queryAc->execute();

			return 0;
		} else {
			return 1;
		}
	}


	//Recover User Account
	public function recoverUserLogin($email)
	{

		//Verify Registration Details
		$dbh = self::connect();
		$c = "SELECT sId,sFname,sLname,sEmail,sPass FROM subscribers WHERE sEmail=:e";
		$queryC = $dbh->prepare($c);
		$queryC->bindParam(':e', $email, PDO::PARAM_STR);
		$queryC->execute();
		$result = $queryC->fetch(PDO::FETCH_OBJ);
		if ($queryC->rowCount() > 0) {

			//Genereate And Update Verification Code
			$varCode = mt_rand(2000, 9000);
			$stmt = "UPDATE subscribers SET sVerCode=$varCode WHERE sId=$result->sId";
			$query = $dbh->prepare($stmt);
			$query->execute();

			//Send Verification Code To User Email
			$email = $result->sEmail;
			$subject = "Account Recovery (" . $this->sitename . ")";
			$message = "<h3>Hi " . $result->sFname . ", You Recently Requested For A Password Recovery. Use The Verification Code \"" . $varCode . "\" To Recover Your Account. Thank You For Using " . $this->sitename . ".</h3>";
			$check = self::sendMail($email, $subject, $message);
			if ($check == 0) {
				return 0;
			} else {
				return 2;
			}
		} else {
			return 1;
		}
	}

	//Recover User Account
	public function verifyRecoveryCode($email, $code)
	{

		//Verify Registration Details
		$dbh = self::connect();
		$c = "SELECT sId FROM subscribers WHERE sEmail=:e AND sVerCode=:c";
		$queryC = $dbh->prepare($c);
		$queryC->bindParam(':e', $email, PDO::PARAM_STR);
		$queryC->bindParam(':c', $code, PDO::PARAM_STR);
		$queryC->execute();
		if ($queryC->rowCount() > 0) {
			return 0;
		} else {
			return 1;
		}
	}

	//Recover Seller Account
	public function updateUserKey($email, $code, $key)
	{

		//Verify Registration Details
		$dbh = self::connect();
		$hash = substr(sha1(md5($key)), 3, 10);
		$verCode = mt_rand(1000, 9999);
		$c = "UPDATE subscribers SET sPass=:k,sVerCode=:v WHERE sEmail=:e AND sVerCode=:c";
		$queryC = $dbh->prepare($c);
		$queryC->bindParam(':e', $email, PDO::PARAM_STR);
		$queryC->bindParam(':c', $code, PDO::PARAM_STR);
		$queryC->bindParam(':k', $hash, PDO::PARAM_STR);
		$queryC->bindParam(':v', $verCode, PDO::PARAM_INT);
		if ($queryC->execute()) {
			return 0;
		} else {
			return 1;
		}
	}


	//Create Virtual Bank Account
	public function createVirtualBankAccount($id, $fname, $lname, $phone, $email, $monnifyApi, $monnifySecret, $monnifyContract)
	{

		$fullname = $fname . " " . $lname;
		$accessKey = "$monnifyApi:$monnifySecret";
		$apiKey = base64_encode($accessKey);

		//Get Authorization Data
		$url = 'https://api.monnify.com/api/v1/auth/login';
		//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
		$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
		//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Basic {$apiKey}",
			),
		));


		$json = curl_exec($ch);
		$result = json_decode($json);
		curl_close($ch);

		$accessToken = null;
		if ($result && isset($result->responseBody) && isset($result->responseBody->accessToken)) {
			$accessToken = $result->responseBody->accessToken;
		}
		$ref = uniqid() . rand(1000, 9000);

		//Request Account Creation
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL =>  $url2,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS =>
			'{
											"accountReference": "' . $ref . '",
											"accountName": "' . $fullname . '",
											"currencyCode": "NGN",
											"contractCode": "' . $monnifyContract . '",
											"customerEmail": "' . $email . '",
											"bvn": "22433145825",
											"customerName": "' . $fullname . '",
											"getAllAvailableBanks": false,
											"preferredBanks": ["035"]
										
									}',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $accessToken,
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$value = json_decode($response, true);

		//Check And Save Account Details
		if (isset($value["requestSuccessful"]) && $value["requestSuccessful"] == true) {
			$account_name  = $value["responseBody"]["accountName"];
			if ($value["responseBody"]["accounts"][0]["bankCode"] == "035") {
				$wema =  $value["responseBody"]["accounts"][0]["accountNumber"];
				$wema_name = $value["responseBody"]["accounts"][0]["bankName"];

				$dbh = self::connect();
				$c = "UPDATE subscribers SET sBankName=:bn,sBankNo=:bno WHERE sId=$id";
				$queryC = $dbh->prepare($c);
				$queryC->bindParam(':bn', $wema_name, PDO::PARAM_STR);
				$queryC->bindParam(':bno', $wema, PDO::PARAM_STR);
				$queryC->execute();
			}
		}
	}

	//Create Virtual Bank Account
	public function createVirtualBankAccount2($id, $fname, $lname, $phone, $email, $monnifyApi, $monnifySecret, $monnifyContract)
	{

		$fullname = $fname . " " . $lname;
		$accessKey = "$monnifyApi:$monnifySecret";
		$apiKey = base64_encode($accessKey);

		//Get Authorization Data
		$url = 'https://api.monnify.com/api/v1/auth/login';
		//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
		$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
		//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Basic {$apiKey}",
			),
		));


		$json = curl_exec($ch);
		$result = json_decode($json);
		curl_close($ch);

		$accessToken = null;
		if ($result && isset($result->responseBody) && isset($result->responseBody->accessToken)) {
			$accessToken = $result->responseBody->accessToken;
		}
		$ref = uniqid() . rand(1000, 9000);

		//Request Account Creation
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL =>  $url2,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS =>
			'{
											"accountReference": "' . $ref . '",
											"accountName": "' . $fullname . '",
											"currencyCode": "NGN",
											"contractCode": "' . $monnifyContract . '",
											"customerEmail": "' . $email . '",
											"bvn": "22433145825",
											"customerName": "' . $fullname . '",
											"getAllAvailableBanks": false,
											"preferredBanks": ["50515","232"]
										
									}',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $accessToken,
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$value = json_decode($response, true);

		//Check And Save Account Details
		if (isset($value["requestSuccessful"]) && $value["requestSuccessful"] == true) {
			$account_name  = $value["responseBody"]["accountName"];
			$rolex = "";
			$sterling = "";

			if ($value["responseBody"]["accounts"][0]["bankCode"] == "50515") {
				$rolex =  $value["responseBody"]["accounts"][0]["accountNumber"];
			} elseif ($value["responseBody"]["accounts"][1]["bankCode"] == "50515") {
				$rolex =  $value["responseBody"]["accounts"][1]["accountNumber"];
			} else {
			}

			if ($value["responseBody"]["accounts"][0]["bankCode"] == "232") {
				$sterling =  $value["responseBody"]["accounts"][0]["accountNumber"];
			} elseif ($value["responseBody"]["accounts"][1]["bankCode"] == "232") {
				$sterling =  $value["responseBody"]["accounts"][1]["accountNumber"];
			} else {
			}

			//Save Account Number

			$dbh = self::connect();
			$c = "UPDATE subscribers SET sRolexBank=:rb,sSterlingBank=:sb WHERE sId=$id";
			$queryC = $dbh->prepare($c);
			$queryC->bindParam(':rb', $rolex, PDO::PARAM_STR);
			$queryC->bindParam(':sb', $sterling, PDO::PARAM_STR);
			$queryC->execute();
		}
	}

	//Create Virtual Bank Account
	public function createVirtualBankAccount3($id, $fname, $lname, $phone, $email, $monnifyApi, $monnifySecret, $monnifyContract)
	{

		$fullname = $fname . " " . $lname;
		$accessKey = "$monnifyApi:$monnifySecret";
		$apiKey = base64_encode($accessKey);

		//Get Authorization Data
		$url = 'https://api.monnify.com/api/v1/auth/login';
		//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
		$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
		//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Basic {$apiKey}",
			),
		));


		$json = curl_exec($ch);
		$result = json_decode($json);
		curl_close($ch);

		$accessToken = null;
		if ($result && isset($result->responseBody) && isset($result->responseBody->accessToken)) {
			$accessToken = $result->responseBody->accessToken;
		}
		$ref = uniqid() . rand(1000, 9000);

		//Request Account Creation
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL =>  $url2,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS =>
			'{
											"accountReference": "' . $ref . '",
											"accountName": "' . $fullname . '",
											"currencyCode": "NGN",
											"contractCode": "' . $monnifyContract . '",
											"customerEmail": "' . $email . '",
											"bvn": "22433145825",
											"customerName": "' . $fullname . '",
											"getAllAvailableBanks": false,
											"preferredBanks": ["070"]
										
									}',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $accessToken,
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$value = json_decode($response, true);

		//Check And Save Account Details
		if (isset($value["requestSuccessful"]) && $value["requestSuccessful"] == true) {
			$account_name  = $value["responseBody"]["accountName"];
			$fidelityBank = "";

			if ($value["responseBody"]["accounts"][0]["bankCode"] == "070") {
				$fidelityBank =  $value["responseBody"]["accounts"][0]["accountNumber"];
			} elseif ($value["responseBody"]["accounts"][1]["bankCode"] == "070") {
				$fidelityBank =  $value["responseBody"]["accounts"][1]["accountNumber"];
			} else {
			}


			//Save Account Number

			$dbh = self::connect();
			$c = "UPDATE subscribers SET sFidelityBank=:fb WHERE sId=$id";
			$queryC = $dbh->prepare($c);
			$queryC->bindParam(':fb', $fidelityBank, PDO::PARAM_STR);
			$queryC->execute();
		}
	}
}
