<?php
session_start();
header('Content-Type: application/json');

$aResult = array();
if(!isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    $aResult['error'] = 'login';    
}

include_once('global.php');
include_once('bitex.php');


if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $id = validate_input($_POST["arguments"][0]); 
        if(is_ticket_id_valid_for_reply($id,$_SESSION['user_id'])) {
            if(isset($_SESSION['reply_ticket_id']))
                unset($_SESSION['reply_ticket_id']);
            $_SESSION['reply_ticket_id'] = $id;
            $aResult['ok'] = 'ok';
        } else
            $aResult['error'] = 'error_id';
    }
}
echo json_encode($aResult);
?>