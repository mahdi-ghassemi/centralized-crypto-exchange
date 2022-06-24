<?php
session_start();

header('Content-Type: application/json');
if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}


$aResult = array();
$aResult['amount'] = $_SESSION['pay_info_amount'];
$aResult['ok'] = 'ok';
echo json_encode($aResult); 
?>