<?php
   
session_start();
include_once('global.php');
include_once('sendsms.php');
include_once('bitex.php');
header('Content-Type: application/json');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}
if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $mobile = validate_input($_POST['arguments'][0]);
        $email = validate_input($_POST['arguments'][1]);
        $user_info = get_user_info_by_username_and_email($mobile,$email);
        if(count($user_info)) {
            $mobile_confirm = $user_info[0]['mobile_confirm'];
            $email_confirm = $user_info[0]['email_confirm'];
            if($mobile_confirm === "1" && $email_confirm === "1") {
                $_SESSION['random_code'] = mt_rand(111111,999999);
                $_SESSION['email_address'] = $user_info[0]['email'];
                $_SESSION['user_id'] = $user_info[0]['id'];
                $sms_result = sendsms_ghasedak_otp($_SESSION['random_code'],$mobile);
                
                $aResult['ok'] = 'ok';
            } else {
                $aResult['error'] = 'not confirm';
            }
        } else {
            $aResult['error'] = 'invalid';
        }
    }
}
echo json_encode($aResult); 
?>