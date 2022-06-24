<?php
header('Content-Type: application/json');
include_once('bitex.php');

$aResult = array();
$admin_setting = get_setting();
$aResult['wmz'] = $admin_setting[0]['webmoney_address'];
$aResult['ok'] = 'ok';
echo json_encode($aResult); 
?>