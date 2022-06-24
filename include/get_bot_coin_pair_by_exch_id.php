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
        $exchange_id = validate_input($_POST['arguments'][0]);
        $coin_pairs = get_coin_pairs_by_exchange_id($exchange_id);        
        $aResult['coin_pairs'] = $coin_pairs;
        $aResult['ok'] = 'ok';
    }
}
echo json_encode($aResult);  
?>