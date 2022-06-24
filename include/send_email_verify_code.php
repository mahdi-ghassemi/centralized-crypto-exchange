<?php
  
session_start();

include_once('global.php');
include_once('bitex.php');
include_once('mail.php');
header('Content-Type: application/json');
if(! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit();
}

$aResult = array();

$user_info = get_user_info_by_id($_SESSION['user_id']);
$email = $user_info[0]['email'];
if(isset($_SESSION['random_email_code']))
    unset($_SESSION['random_email_code']);
$_SESSION['random_email_code'] = mt_rand(111111,999999);

$body = confirm_code_body($_SESSION['random_email_code']);
$r = send_mail($no_reply_mail_address,$email,$no_reply_mail_address,$no_reply_mail_password,'کد تایید ایمیل',$body,null);
//error_log($r);
$aResult['ok'] = 'ok'; 
echo json_encode($aResult); 
?>