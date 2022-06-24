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
$mail_type = validate_input($_POST['arguments'][0]);
$order_id = validate_input($_POST['arguments'][1]);
$subject = $mail_type;
$body = 'userid: '.$_SESSION['user_id'].' username: '.$_SESSION['bitex_username'].' order id: '.$order_id; 
$res = send_mail($no_reply_mail_address,$to_mail,$no_reply_mail_address,$no_reply_mail_password,$subject,$body,null);
error_log($res);   
echo json_encode($aResult);  
?>