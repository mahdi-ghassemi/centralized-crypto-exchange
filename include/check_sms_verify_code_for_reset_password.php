<?php
   
session_start();

include_once('global.php');
include_once('bitex.php');
include_once('mail.php');

header('Content-Type: application/json');

if( !isset($_SESSION['user_id']) || !isset($_SESSION['random_code']) || !isset($_SESSION['email_address'])) {
    header("location:".$root_path."login/");
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
        if((string) $vcode === (string) $_SESSION['random_code']) {
            
            $new_pass = random_code(8);
            $datas = array();
            $datas['password'] = password_hash($new_pass, PASSWORD_DEFAULT);
            update_users($_SESSION['user_id'],$datas);
            $ip_address = get_user_ip();
            insert_user_action($ip_address,$_SESSION['user_id'],'تایید بازنشانی کلمه عبور',null);
            $body = '<br>
                    کاربر گرامی؛';
            $body .= '<br> به درخواست شما کلمه عبور جدید برای شما ایجاد گردید.لطفاً پس از ورود به داشبورد خود از منوی تنظیمات امنیتی کلمه عبور خود را تغییر دهید.';
            $body .= '<br>کلمه عبور جدید: <br>'.$new_pass; 
            $body .= '<br><br>خدمات ارز دیجیتال آسان'; 
            
            $res = send_mail($no_reply_mail_address,$_SESSION['email_address'],$no_reply_mail_address,$no_reply_mail_password,'New Password',$body,null);
            $aResult['ok'] = 'ok';
            
            
        } else {
            $aResult['error'] = 'code error';            
        }
    }
}
echo json_encode($aResult); 
?>