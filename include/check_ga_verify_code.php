<?php
  
session_start();

include_once('global.php');
include_once('bitex.php');
include_once('google_2fa_generate.php');
require "authenticator.php";

header('Content-Type: application/json');

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}
if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $vcode = validate_input($_POST['arguments'][0]);
        $user_info = get_user_info_by_id($_SESSION['user_id']);
        $ga_code = $user_info[0]['ga_code'];
        if($ga_code != null) {
            $google2fa = new Google2FA;
            $result = $google2fa->verify_key($ga_code, $vcode);
            $aResult['ok'] = 'ok';
            if(isset($_SESSION['ga_2af']) && isset($_SESSION['tow_fa'])) {
                $datas = array();                
                $datas['tow_fa'] = $_SESSION['tow_fa'];
                $datas['ga_2af'] = $_SESSION['ga_2af'];                
                $datas['mobile_2af'] = $_SESSION['mobile_2af'];                
                update_users($_SESSION['user_id'],$datas);
                if($_SESSION['tow_fa'] === "1")
                    $aResult['status'] = 'on';
                if($_SESSION['tow_fa'] === "0")
                    $aResult['status'] = 'off';
                unset($_SESSION['tow_fa']);
                unset($_SESSION['ga_2af']);
            }
        } else if(isset($_SESSION['auth_secret']) ) {
            $Authenticator = new Authenticator();
            $checkResult = $Authenticator->verifyCode($_SESSION['auth_secret'], $vcode, 2);
            if($checkResult) {
                $aResult['ok'] = 'ok';
                if(isset($_SESSION['ga_2af']) && isset($_SESSION['tow_fa'])) {
                    $datas = array();                
                    $datas['tow_fa'] = $_SESSION['tow_fa'];
                    $datas['ga_2af'] = $_SESSION['ga_2af'];                
                    $datas['mobile_2af'] = $_SESSION['mobile_2af'];                
                    $datas['ga_code'] = $_SESSION['auth_secret'];                
                    update_users($_SESSION['user_id'],$datas);
                                    
                    unset($_SESSION['tow_fa']);
                    unset($_SESSION['ga_2af']);
                
                } else {
                    $aResult['error'] = 'session error';
                }                
            } else {
                $aResult['error'] = 'code error';
            }            
        } else {
            $aResult['error'] = 'session error';            
        }
    } 
}
echo json_encode($aResult); 
?>