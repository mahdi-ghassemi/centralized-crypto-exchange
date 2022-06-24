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
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
        $aResult['error'] = 'Error in arguments!';
    }
        
    else {
        
        $id = validate_input($_POST['arguments'][0]);
        $user_id = $_SESSION['user_id'];
        $is_order_valid = is_order_valid_for_user_delete($id,$user_id);
        if($is_order_valid) {
            $datas = array();
            $datas['is_delete'] = "1";
            if(update_order($id,$datas)) {
                $aResult['ok'] = 'ok';
                insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'کنسل نمودن سفارش',$_SESSION['bitex_username'].' - '.$id);
            }
            else
                $aResult['error'] = 'nok';                
                       
        } else {
            $aResult['error'] = 'order not valid';            
        }        
    }
}
echo json_encode($aResult);  
?>