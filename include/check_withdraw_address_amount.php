<?php 
  
session_start();

include_once('global.php');
include_once('bitex.php');
include_once('sendsms.php');


header('Content-Type: application/json');

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

$user_info = get_user_info_by_id($_SESSION['user_id']);
$email_confirm = $user_info[0]['email_confirm'];
$tow_fa = $user_info[0]['tow_fa'];
$mobile_2af = $user_info[0]['mobile_2af'];
$ga_2af = $user_info[0]['ga_2af'];

if($email_confirm == "0")
    $aResult['error'] = 'email confirm';

if($tow_fa == "0")
    $aResult['error'] = 'tow_fa confirm';

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $amount = validate_input($_POST['arguments'][0]);
        $address = validate_input($_POST['arguments'][1]);
        $user_wallet_address = validate_input($_POST['arguments'][2]);
        
        $wallet_info = get_wallet_by_address_user_id($user_wallet_address,$_SESSION['user_id']);
        if(count($wallet_info)) {
            $taker_address = false;
            if($wallet_info[0]['wallet_coin_id'] === "3") {
                $wallet_info_other = get_wallet_for_other($address,$_SESSION['user_id']);
                if(count($wallet_info_other))
                    $taker_address = true;
            } else 
                $taker_address = validate_wallet_address($address);
            
            if($taker_address) {
                $wallet_id = $wallet_info[0]['id'];
                $balance = balance($wallet_id);
                $minimum_w = $network_fee = 0;
                if($wallet_info[0]['wallet_coin_id'] === "1") {
                    $minimum_w = 0.001;
                    $network_fee = 0.0004;                    
                }
                if($wallet_info[0]['wallet_coin_id'] === "2") {
                    $user_wallet_address = 1;
                    $minimum_w = 2;
                }
                if($wallet_info[0]['wallet_coin_id'] === "3") {                    
                    $minimum_w = 100000;
                }
                
                if($amount >= $minimum_w) {
                    if($amount <= $balance) {
                        if($mobile_2af == "1" && $ga_2af == "0") { 
                            $mobile = $user_info[0]['mobile'];
                            $_SESSION['random_code'] = mt_rand(111111,999999); 
                            $sms_result = sendsms_ghasedak_otp($_SESSION['random_code'],$mobile); 
                            $aResult['mobile_2af'] = $mobile_2af;
                            $aResult['ga_2af'] = $ga_2af;
                            $aResult['ok'] = 'ok'; 
                        } 
                        if($mobile_2af == "0" && $ga_2af == "1") {                             
                            $aResult['mobile_2af'] = $mobile_2af;
                            $aResult['ga_2af'] = $ga_2af;
                            $aResult['ok'] = 'ok'; 
                        }
                        
                    } else 
                        $aResult['error'] = 'no balance';
                    
                } else {
                    $aResult['minimum_w'] = $minimum_w;
                    $aResult['error'] = 'amount minimum';
                }
                
            } else
                $aResult['error'] = 'address invalid';            
        } else
            $aResult['error'] = 'user wallet invalid';
    }
}
echo json_encode($aResult); 
?>