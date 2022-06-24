<?php

session_start();

header('Content-Type: application/json');
$aResult = array();
if(!isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    $aResult['error'] = 'login';    
}

include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $order_id = validate_input($_POST['arguments'][0]);
        $order_info = get_order_info_by_id_user_id_status($order_id,$_SESSION['user_id'],"1");
        if(count($order_info)) {
            $user_info = get_user_info_by_id($_SESSION['user_id']);
            $user_status = $user_info[0]['status'];
            if($user_status === "1") { 
                $aResult['coin_id'] = $order_info[0]['coin_id'];                
                $aResult['order_type'] = $order_info[0]['order_type'];
                
                $calc = array();
                $calc = calculat($order_info[0]['amount'],$order_info[0]['coin_id']);
                
                $datas = array();
                $datas['amount_usd'] = $calc['amount_usd'];
                $datas['amount_toman'] = "";
                if($order_info[0]['order_type'] === "1") {
                    $datas['amount_toman'] = $calc['sell'];
                    $datas['fee_us'] = $calc['sell_to_us'];
                    $datas['fee_karmozd'] = ( $calc['sell'] * $calc['bitex_fee'] ) / 100;                
                }
                if($order_info[0]['order_type'] === "2") {
                    $datas['amount_toman'] = $calc['buy'];
                    $datas['fee_us'] = $calc['buy_from_us'];
                    $datas['fee_karmozd'] = ( $calc['buy'] * $calc['bitex_fee'] ) / 100; 
                }
                
                update_order($order_id,$datas);
                
                
                if (isset($_SESSION['pay_info_amount']))
                    unset($_SESSION['pay_info_amount']);
                $_SESSION['pay_info_amount'] = $order_info[0]['amount'];
                $_SESSION['pay_info_amount_toman'] = $datas['amount_toman'];
                $_SESSION['pay_info_user_id'] = $_SESSION['user_id'];
                $_SESSION['pay_info_order_id'] = $order_info[0]['id'];
                $_SESSION['pay_info_description'] = 'Order No. '.$order_info[0]['id'];
                if($order_info[0]['order_type'] === "2") {
                    if( ($datas['amount_toman'] * 10) < 1000 || ($datas['amount_toman'] * 10) > 499980000) {
                        $aResult['error'] = 'amount error';                        
                    }
                }                
                $aResult['ok'] = 'ok';
            } else {
                $aResult['error'] = 'user lock';                
            }
        } else {
            $aResult['error'] = 'invalid order';
        }
    }
}
echo json_encode($aResult);
?>