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
        $sec_que_id = validate_input($_POST['arguments'][0]);
        $sec_que_ans = validate_input($_POST['arguments'][1]); 
        $datas = array();
        $datas['sec_que_id'] = $sec_que_id;
        $datas['sec_que_ans'] = $sec_que_ans;
        update_users($_SESSION['user_id'],$datas);
        insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'به روز رسانی سوال امنیتی',$_SESSION['bitex_username']);
        $aResult['ok'] = 'ok';
    }
}
echo json_encode($aResult);  
?>