<?php
   
    class AccountAccess extends Controller{

        protected $model;

        public function __construct(){
            $this->model=new Account;
        }

        public function verifyAdminLogin(){
            $data=extract($_POST);
            $username=strip_tags($username);
            $password=strip_tags($password);
            $result=$this->model->verifyAdminAccount($username,$password);
           
            if($result == 0){return 0;}
            elseif($result == 1){return 1;}
            elseif($result == 2){ return 2;}
            else{return 3;}
        }
             
        public function cleanMyData($data){
            $data=strip_tags($data);
            $data= preg_replace("/[^a-zA-Z0-9\s]/", "", $data);
            return $data;
        }

        //Register/Create User Account
        public function registerUser(){
            extract($_POST);
            if(!isset($_POST["referal"])){$referal="";}

            //Clean Input
            $fname=$this->cleanMyData($fname); $lname=$this->cleanMyData($lname); $email=strip_tags($email); $phone=strip_tags($phone);
            $state=$this->cleanMyData($state); $account=strip_tags($account); $referal=strip_tags($referal); $transpin=strip_tags($transpin);
           
            $check=$this->model->registerUser($fname,$lname,$email,$phone,$password,$state,$account,$referal,$transpin);
            return $check;
        }

        //Login User Account
        public function loginUser(){
            extract($_POST);
            $phone=strip_tags($phone);
            $password=strip_tags($password);
            $check=$this->model->loginUser($phone,$password);
            return $check;
        }

        //Recover User Account
        public function recoverUserLogin(){
            extract($_POST);
            $email=strip_tags($email);
            $check=$this->model->recoverUserLogin($email);
            return $check;
        }

        //Recover User Account
        public function verifyRecoveryCode(){
            extract($_POST);
            $email=strip_tags($email);
            $code=strip_tags($code);
            $check=$this->model->verifyRecoveryCode($email,$code);
            return $check;
        }

        //Recover Seller Account
        public function updateUserKey(){
            extract($_POST);
            $email=strip_tags($email);
            $code=strip_tags($code);
            $check=$this->model->updateUserKey($email,$code,$password);
            return $check;
        }

    }

?>