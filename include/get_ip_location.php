<?php
header('Content-Type: application/json');
include_once('global.php');


$aResult = array();
if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}
if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $ip = validate_input($_POST['arguments'][0]);
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $aResult['ok'] = 'ok';
        $aResult['city'] = $details->city;
        $aResult['region'] = $details->region;
        $aResult['country'] = $details->country;
        $aResult['org'] = $details->org;
        $aResult['hostname'] = $details->hostname;
    } 
}
echo json_encode($aResult); 
?>