<?php
    //Auto Load Classes
    require_once("../autoloader.php");

    //Allowed API Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header("Access-Control-Allow-Methods: POST");
    header("Allow: POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

    $headers = apache_request_headers();
    $response = array();
    $controller = new ApiAccess;
    $controller2 = new RechargePin;
    date_default_timezone_set('Africa/Lagos');
            


    // -------------------------------------------------------------------
    //  Check Request Method
    // -------------------------------------------------------------------

    $requestMethod = $_SERVER["REQUEST_METHOD"]; 
    if ($requestMethod !== 'POST') {
        header('HTTP/1.0 400 Bad Request');
        $response["status"] = "fail";
        $response["msg"] = "Only POST method is allowed";
        echo json_encode($response); exit(); 
    } 
    
    // -------------------------------------------------------------------
    //  Check For Api Authorization
    // -------------------------------------------------------------------
    
    if((isset($headers['Authorization']) || isset($headers['authorization'])) || (isset($headers['Token']) || isset($headers['token']))){
        if((isset($headers['Authorization']) || isset($headers['authorization']))){
            $token = trim(str_replace("Token", "", (isset($headers['Authorization'])) ? $headers['Authorization'] : $headers['authorization']));
        }
        if((isset($headers['Token']) || isset($headers['token']))){
            $token = trim(str_replace("Token", "", (isset($headers['Token'])) ? $headers['Token'] : $headers['token']));
        }
        $result=$controller->validateAccessToken($token);
        if($result["status"] == "fail"){
            // tell the user no products found
            header('HTTP/1.0 401 Unauthorized');
            $response["status"] = "fail";
            $response["msg"] = "Authorization token not found $token";
            echo json_encode($response); exit(); 
        }
        else{
            $usertype = $result["usertype"];
            $userbalance = (float) $result["balance"]; 
            $userid = $result["userid"];
            $refearedby = $result["refearedby"];
            $referal = $result["phone"];
            $referalname = $result["name"];
         }
    }
    else{
        header('HTTP/1.0 401 Unauthorized');
        // tell the user no products found
        $response["status"] = "fail";
        $response["msg"] = "Your authorization token is required.";
        echo json_encode($response); exit(); 
    }

    // -------------------------------------------------------------------
    //  Get The Request Details
    // -------------------------------------------------------------------

    $input = @file_get_contents("php://input");
    //decode the json file
    $body = json_decode($input);
    

    // Support Other API Format
    $body2 = array();   
    if(!isset($body->ref)){$body2["ref"]=time();}
    if(isset($body->card_name)){$body2["businessname"]=$body->card_name ;}
    $body = (object) array_merge( (array)$body, $body2 );

    $network= (isset($body->network)) ? $body->network : "";
    $quantity= (isset($body->quantity)) ? $body->quantity : "";
    $amount= (isset($body->amount)) ? $body->amount : "";
    $ref= (isset($body->ref)) ? $body->ref : "";
    $businessname= (isset($body->name_on_card)) ? $body->name_on_card : "";

    // -------------------------------------------------------------------
    //  Check Inputs Parameters
    // -------------------------------------------------------------------

    $requiredField = "";
    if($ref == ""){$requiredField ="Ref Is Required"; }
    if($amount == ""){$requiredField ="Amount Is Required"; }
    if($quantity == ""){$requiredField ="Quantity Is Required"; }
    if($network == ""){$requiredField ="Network Id Required"; }
    if($businessname == ""){$requiredField ="Business Name Is Required"; }

    if($requiredField <> ""){
        header('HTTP/1.0 400 Parameters Required');
        $response['status']="fail";
        $response['msg'] = $requiredField;
        echo json_encode($response);
        exit();
    }
    
    function changeNetworkID($network) {
    switch ($network) {
        case 1:
            return 1;
        case 2:
            return 3;
        case 3:
            return 4;
        case 4:
            return 2;
        default:
            return $network;
    }
}
    
$network =    changeNetworkID($network); 
    
    

    // -------------------------------------------------------------------
    //  Verify Network Id
    // -------------------------------------------------------------------
    
    $result = $controller->verifyNetworkId($network);
    if($result["status"]=="fail"){
        header('HTTP/1.0 400 Invalid Network Id');
        $response['status']="fail";
        $response['msg'] = "The Network id is invalid";
        echo json_encode($response);
        exit();
    }
    else{
        $networkDetails=$result; 
    }


    // -------------------------------------------------------------------
    //  Check If Network Is Available
    // -------------------------------------------------------------------
    
    if($networkDetails["datapinStatus"] <> "On"){
        header('HTTP/1.0 400 Network Not Available');
        $response['status']="fail";
        $response['msg'] = "Sorry, {$networkDetails["network"]} is not available at the moment";
        echo json_encode($response);
        exit();
    }

    // 
    else{

        
        $datagroup = $result["datatype"];
        $actualPlanId = $result["datapin"];
        
        //Compute Amount To Pay
        $quantity = (int) $quantity;
        $amount =  (float) $amount; 
        $buyprice =  (float) $result["buyprice"]; 
        

        
        $buyprice = $buyprice * $quantity;
        $discount = 99/100 * $amount;
        //Compute Profit
        $amountopay = $discount * $quantity;
        $profit = $discount - $buyprice;

        $plandesc = "Purchase of $quantity quantity of recharge pin of At The Rate Of N{$discount}"; 
        $plandes = "Purchase of $quantity recharge pin of ".$networkDetails["network"]." ".$result['name']." ".$result['datatype']." ".$result['day']." Days Plan At The Rate Of N{$amount}"; 
      //$thedatasize=$result['amount'];
      $thedatasize= "tyyuuiibbh" ;
    }
    


    // -------------------------------------------------------------------
    //  Check Id User Balance Can Perform The Transaction
    // -------------------------------------------------------------------
    if($amountopay > $userbalance || $amountopay < 0){
            header('HTTP/1.0 400 Insufficient Balance');
            $response['status']="fail";
            $response['msg'] = "Insufficient balance fund your wallet and try again";
            echo json_encode($response);
            exit();
    }


    // -------------------------------------------------------------------
    //  Check For Api Authorization
    // -------------------------------------------------------------------
    
    $result = $controller->checkIfTransactionExist($ref);
    if($result["status"]=="fail"){
        header('HTTP/1.0 400 Transaction Ref Already Exist');
        $response['status']="fail";
        $response['msg'] = "Transaction Ref Already Exist";
        echo json_encode($response);
        exit();
    }

    // -------------------------------------------------------------------
    // Purchase Data
    // -------------------------------------------------------------------
    // -------------------------------------------------------------------

    $servicename = "Recharge Pin";
    $servicedesc = $plandesc;
    
     
    $result = $controller->checkTransactionDuplicate($servicename,$servicedesc);
    if($result["status"]=="fail"){
        header('HTTP/1.0 400 Possible Transaction Duplicate, Please Verify Transaction & Try Again After 60 Seconds');
        $response['status']="fail";
        $response['msg'] = "Possible Transaction Duplicate, Please Verify Transaction & Try Again After 60 Seconds";
        echo json_encode($response);
        exit();
    }

    // Debit User Before Performing The Transaction
    $oldbalance = (float) $userbalance;
    $deibt = $oldbalance - $amountopay;

    $checkDebit=$controller->debitUserBeforeTransaction($userid,$deibt);

    if($checkDebit <> "success"){
        header('HTTP/1.0 400 Could Not Complete Transaction');
        $response['status']="fail";
        $response['msg'] = "Could Not Complete Transaction";
        echo json_encode($response);
        exit();
    }

    // -------------------------------------------------------------------
    //  Record Transaction As Processing With Status 5
    // -------------------------------------------------------------------
    $transRecord = $controller->recordTransaction($userid,$servicename,$servicedesc,$amountopay,$userbalance,$body->ref,"5");
    
    // -------------------------------------------------------------------
    //  Send Request To Purchase Recharge Pin
    // -------------------------------------------------------------------
    $result = $controller2->purchaseRechargePin($body,$networkDetails);
     
    // -------------------------------------------------------------------
    // Debit User Wallet & Record Transaction
    // -------------------------------------------------------------------
     
    if($result["status"]=="success"){
        if($refearedby <> ""){ $controller->creditReferalBonus($referal,$referalname,$refearedby,$servicename); }
        $controller->updateTransactionStatus($userid,$body->ref,$amountopay,"0");
        $controller->saveProfit($body->ref,$profit);
        $controller->saveRechargePin($userid,$body->ref,$businessname,$result["network"],$result["load_pin"],$result["quantity"],$result["serial"],$result["pin"]);
        $response['status']="success";
        $response['Status']="successful";
        $response["quantity"] = $result["quantity"];
        $response["serial"] = $result["serial"];
        $response["pin"] = $result["pin"];
        header('HTTP/1.0 200 Transaction Successful');
        echo json_encode($response);
        exit(); 
    }
    else{
        header('HTTP/1.0 400 Transaction Failed');
        $response['status']="fail";
        $response['Status']="failed";
        $response['msg'] = $result["msg"];
        $controller->updateTransactionStatus($userid,$body->ref,$amountopay,"1");
        echo json_encode($response);
        exit();
    }

?>