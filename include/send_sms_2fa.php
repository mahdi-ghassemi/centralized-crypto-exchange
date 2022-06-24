<?php
   
session_start();
include_once('global.php');
include_once('bitex.php');
include_once('sendsms.php');
header('Content-Type: application/json');
if(! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit();
}

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}
if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $mobile_2af = validate_input($_POST['arguments'][0]);
        $tow_fa = validate_input($_POST['arguments'][1]);
        $ga_fa = validate_input($_POST['arguments'][2]);
       
        $user_info = get_user_info_by_id($_SESSION['user_id']);
        $mobile = $user_info[0]['mobile'];
        if(isset($_SESSION['random_code']))
            unset($_SESSION['random_code']);
        if(isset($_SESSION['mobile_2af']))
            unset($_SESSION['mobile_2af']);
        if(isset($_SESSION['tow_fa']))
            unset($_SESSION['tow_fa']);
        if(isset($_SESSION['ga_2af']))
            unset($_SESSION['ga_2af']);
        $_SESSION['mobile_2af'] = $mobile_2af;
        $_SESSION['tow_fa'] = $tow_fa;
        $_SESSION['ga_2af'] = $ga_fa;
        
        $_SESSION['random_code'] = mt_rand(111111,999999);       
        
        $sms_result = sendsms_ghasedak_otp($_SESSION['random_code'],$mobile); 
        $aResult['ok'] = 'ok';
    }
}

echo json_encode($aResult); 
?>