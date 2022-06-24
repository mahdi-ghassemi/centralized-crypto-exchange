<?php

session_start();


header('Content-Type: application/json');
if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

include_once('global.php');
include_once('bitex.php');
include_once('mail.php');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 5) ) {
        $aResult['error'] = 'Error in arguments!';
    }        
    else {        
        $firstname = validate_input($_POST['arguments'][0]);
        $lastname = validate_input($_POST['arguments'][1]); 
        $fathername = validate_input($_POST['arguments'][2]); 
        $code_meli = validate_input($_POST['arguments'][3]); 
        $birthday = validate_input($_POST['arguments'][4]); 
        $datas = array();
        $datas['firstname'] = $firstname;
        $datas['lastname'] = $lastname;
        $datas['fathername'] = $fathername;
        $datas['code_meli'] = $code_meli;
        $datas['birthday'] = $birthday;
        update_users($_SESSION['user_id'],$datas);
        insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'به روز رسانی اطلاعات پروفایل',$_SESSION['bitex_username']);
        $res = send_mail($no_reply_mail_address,$to_mail,$no_reply_mail_address,$no_reply_mail_password,'Update User Profile','userid: '.$_SESSION['user_id'].' username: '.$_SESSION['bitex_username'],null);
        $aResult['ok'] = 'ok';
    }
}
echo json_encode($aResult);  
?>