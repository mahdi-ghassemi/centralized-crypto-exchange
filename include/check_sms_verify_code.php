<?php
  
session_start();

include_once('global.php');
include_once('bitex.php');

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
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $vcode = validate_input($_POST['arguments'][0]);        
        if((string) $vcode === (string) $_SESSION['random_code']) {
            $aResult['ok'] = 'ok';
            if(isset($_SESSION['mobile_2af']) && isset($_SESSION['tow_fa'])) {
                $datas = array();
                $datas['mobile_confirm'] = "1";
                if( $_SESSION['tow_fa'] === "1") { 
                    $aResult['status'] = "on";
                    $datas['tow_fa'] = $_SESSION['tow_fa'];
                    $datas['mobile_2af'] = $_SESSION['mobile_2af'];
                    $datas['ga_2af'] = $_SESSION['ga_2af'];
                }
                if( $_SESSION['tow_fa'] === "0") {
                    $aResult['status'] = "off";
                    $datas['tow_fa'] = $_SESSION['tow_fa'];
                    $datas['mobile_2af'] = "0";
                    $datas['ga_2af'] = "0";
                }
                $datas['tow_fa'] = $_SESSION['tow_fa'];
                $datas['mobile_2af'] = $_SESSION['mobile_2af'];
                $datas['ga_2af'] = $_SESSION['ga_2af'];
                update_users($_SESSION['user_id'],$datas);
                
                
                unset($_SESSION['tow_fa']);
                unset($_SESSION['mobile_2af']);
                unset($_SESSION['ga_2af']);
            }
        } else {
            $aResult['error'] = 'code error';            
        }
    } 
}
echo json_encode($aResult); 
?>