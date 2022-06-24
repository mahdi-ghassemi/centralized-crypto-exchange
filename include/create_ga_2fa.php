<?php
   
session_start();

include_once('global.php');
include_once('bitex.php');
require "authenticator.php";

header('Content-Type: application/json');

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}
if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $ga_2af = validate_input($_POST['arguments'][0]);
        $tow_fa = validate_input($_POST['arguments'][1]);
        $mobile_2af = validate_input($_POST['arguments'][2]);
        
        $Authenticator = new Authenticator();
        $secret = $Authenticator->generateRandomSecret();
        $_SESSION['auth_secret'] = $secret;
        $qrCodeUrl = $Authenticator->getQR('esaraafi.ir ('.$_SESSION['bitex_username'].')', $_SESSION['auth_secret']);   $aResult['ok'] = 'ok'; 
        $aResult['qrCodeUrl'] = $qrCodeUrl;
        if(isset($_SESSION['ga_2af']))
            unset($_SESSION['ga_2af']);
        if(isset($_SESSION['tow_fa']))
            unset($_SESSION['tow_fa']);
        if(isset($_SESSION['mobile_2af']))
            unset($_SESSION['mobile_2af']);
        $_SESSION['ga_2af'] = $ga_2af;
        $_SESSION['tow_fa'] = $tow_fa;
        $_SESSION['mobile_2af'] = $mobile_2af;
    }
}

echo json_encode($aResult);
?>