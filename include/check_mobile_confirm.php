<?php
  
session_start();

include_once('global.php');
include_once('bitex.php');

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
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $vcode = validate_input($_POST['arguments'][0]);        
        $mobile = validate_input($_POST['arguments'][1]);        
        if((string) $vcode === (string) $_SESSION['random_code']) {
            $aResult['ok'] = 'ok';
            $datas = array();
            $datas['mobile_confirm'] = "1";
            $datas['mobile'] = $mobile;
            update_users($_SESSION['user_id'],$datas);
            unset($_SESSION['random_code']);
           
        } else {
            $aResult['error'] = 'code error';            
        }
    } 
}
echo json_encode($aResult); 
?>