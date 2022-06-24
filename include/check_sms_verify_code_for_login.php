<?php
   
session_start();

include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');

header('Content-Type: application/json');


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
            $aResult['ok'] = 'ok';
            $user_id = $_SESSION['user_id'];
            $table = get_user_info_by_id($user_id);
            $_SESSION['bitex_username'] = $table[0]['username'];
            $_SESSION['firstname'] = $table[0]['firstname'];
            $_SESSION['lastname'] = $table[0]['lastname'];
            $_SESSION['email'] = $table[0]['email'];               
            $_SESSION['access_level_id'] = $table[0]['access_level_id'];
            $_SESSION['user_status'] = $table[0]['status'];               
            $_SESSION['email_confirm'] = $table[0]['email_confirm'];               
            $user_type = (int)$_SESSION['access_level_id'];
                
            $_SESSION['ip_address'] = get_user_ip();
            $login_info = array();
            $login_info['user_id'] = $_SESSION['user_id'];
            $login_info['login_date'] = jdate('Y-m-d','','','','en');
            $login_info['login_time'] = jdate('H:i:s','','','','en');
            $login_info['ip_address'] = $_SESSION['ip_address'];                
            $aResult['ok'] = 'ok';
            if($_SESSION['user_status'] == "1"){
                $last_log_id = insert_login_info($login_info);
                if($last_log_id > 0) {
                    if(isset($_SESSION['bitex_sessionid'])){
                        unset($_SESSION['bitex_sessionid']); 
                    }
                    $_SESSION['bitex_sessionid'] = md5($_SESSION['user_id']);
                    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'تایید کد پیامک در لاگین',$_SESSION['bitex_username']);
                    if($user_type === 10)
                        $aResult['url'] = '../admin-panel/';
                    else if($user_type === 1)
                        $aResult['url'] = '../dashboard/';                    
                }
                else
                    $aResult['error'] = 'login error';
            }
            else
                $aResult['url'] = '../accounts/lock/';
        } else {
            $aResult['error'] = 'code error';            
        }
    }
}
echo json_encode($aResult); 
?>