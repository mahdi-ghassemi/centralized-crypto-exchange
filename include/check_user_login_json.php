<?php
session_start();
include_once('global.php');
include_once('bitex.php');

header('Content-Type: application/json');

$aResult = array();


if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

/*
$token = validate_input($_POST['arguments'][3]);
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = [
    'secret' => '6LfvkuUUAAAAAFkBE6dPm08pdWZTR68oIOU3fACq',
    'response' => $token    
];
$header = [
  'Content-type'   => 'application/x-www-form-urlencoded',
];
$options = array(
    'http' => array(
        'header' => "Content-type:application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)        
        )
);
if(in_array('curl',get_loaded_extensions())){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);//10002
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//10015
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//10023    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//64
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//19913
    //curl_setopt($ch, CURLOPT_CONNECTIONTIMEOUT, 45);//78
    if(!$res = curl_exec($ch)){
        $error = curl_error($ch);
        $aResult['error'] = 'g_recap_error'; 
    } else {
        $res = json_decode($res,true);
        if($res['success'] != true) {
            $aResult['error'] = 'g_recap_error';    
        }
    }    
}
*/

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 4) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    
    elseif(strtolower(validate_input($_POST['arguments'][2])) !== strtolower($_SESSION["vercode"])) {        
        $aResult['error'] = 'cap error';
    }
    else {        
        $username = validate_input($_POST['arguments'][0]);
        $password = validate_input($_POST['arguments'][1]);
        $captcha = validate_input($_POST['arguments'][2]);
        
        $table = get_user_info_by_username($username);
        
        if(count($table) > 0){
            $hash = $table[0]['password'];
            if(password_verify ($password,$hash)){
                $_SESSION['user_id'] = $table[0]['id'];
                $aResult['tow_fa'] = '0';
                $aResult['mobile_2af'] = '0';
                $aResult['ga_2af'] = '0';
                $tow_fa = $table[0]['tow_fa'];
                if($tow_fa === "1") {
                    $aResult['tow_fa'] = '1';
                    $mobile_2af = $table[0]['mobile_2af'];
                    $ga_2af = $table[0]['ga_2af'];
                    if($mobile_2af === "1") {
                        $aResult['mobile_2af'] = '1';
                        $aResult['ok'] = 'ok';
                    } 
                    if($ga_2af === "1") {
                        $aResult['ga_2af'] = '1';
                        $aResult['ok'] = 'ok';
                    } 
                } else {
                    $_SESSION['user_id'] = $table[0]['id'];
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
                    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'لاگین',$username);
                    $aResult['ok'] = 'ok';
                    if($_SESSION['user_status'] == "1"){
                        $last_log_id = insert_login_info($login_info);
                        if($last_log_id > 0){
                            if(isset($_SESSION['bitex_sessionid'])){
                                unset($_SESSION['bitex_sessionid']);                    
                            }
                            $_SESSION['bitex_sessionid'] = md5($_SESSION['user_id']);
                            $_SESSION['bitex_username'] = $username;
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
                }
            }
            else
                $aResult['error'] = 'login error';
        } else
            $aResult['error'] = 'login error';            
    }
}
echo json_encode($aResult);  
?>