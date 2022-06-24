<?php

session_start();


header('Content-Type: application/json');

include_once('bitex.php');

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

$aResult = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);

$aResult['ok'] = 'ok';
$aResult['tow_fa_current'] = $user_info[0]['tow_fa'];
$aResult['mobile_2af_current'] = $user_info[0]['mobile_2af'];
$aResult['ga_2af_current'] = $user_info[0]['ga_2af'];
$aResult['mobile_confirm'] = $user_info[0]['mobile_confirm'];

echo json_encode($aResult);  
?>