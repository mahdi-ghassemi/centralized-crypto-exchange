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

if(isset($_SESSION['random_code']))
    unset($_SESSION['random_code']);
$_SESSION['random_code'] = mt_rand(111111,999999);

$user_info = get_user_info_by_id($_SESSION['user_id']);
$mobile = $user_info[0]['mobile'];
if($mobile == null) {
    $mobile = validate_input($_POST['arguments'][0]);
}

$sms_result = sendsms_ghasedak_otp($_SESSION['random_code'],$mobile); 

$aResult['ok'] = 'ok'; 
echo json_encode($aResult); 
?>