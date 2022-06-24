<?php
  
session_start();


header('Content-Type: application/json');
if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

include_once('global.php');
include_once('bitex.php');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
        $aResult['error'] = 'Error in arguments!';
    }        
    else {        
        $password = validate_input($_POST['arguments'][0]);
        $new_password = validate_input($_POST['arguments'][1]);                
        
        $table = get_user_info_by_id($_SESSION['user_id']);
        if(count($table) > 0){            
            $hash = $table[0]['password'];
            if(password_verify ($password,$hash)) {
                $data = array(); 
                $data['password'] = password_hash($new_password, PASSWORD_DEFAULT);                
                $result = update_users($_SESSION['user_id'],$data);
                insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'به روز رسانی  کلمه عبور',$_SESSION['bitex_username']);
                if($result)
                    $aResult['ok'] = 'ok';
                else
                    $aResult['error'] = 'update'; 
                                
            } else 
                $aResult['error'] = 'pass error'; 
        
        } else {
            $aResult['error'] = 'pass error';            
        }
    }
}
echo json_encode($aResult);  
?>