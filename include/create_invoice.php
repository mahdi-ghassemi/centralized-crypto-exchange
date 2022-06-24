<?php
 
session_start();


$root_path = "../";

header('Content-Type: application/json');
if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location:".$root_path."login/");
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
        $order_info = get_order_info_by_id_userid($id,$user_id);
        if(count($order_info)) {
            $aResult['ok'] = 'ok';
            $aResult['order_type'] = $order_info[0]['order_type'];
            $aResult['coin_id'] = $order_info[0]['coin_id'];
            $aResult['coin_name'] = $order_info[0]['name'];
            $aResult['coin_name_fa'] = $order_info[0]['name_fa'];
            $aResult['symbol'] = $order_info[0]['symbol'];
            $aResult['image'] = $order_info[0]['image'];
            $aResult['order_date'] = $order_info[0]['order_date'];
            $aResult['order_time'] = $order_info[0]['order_time'];
            $aResult['status'] = $order_info[0]['status'];
            $aResult['amount'] = $order_info[0]['amount'];
            $aResult['order_type_title'] = $order_info[0]['order_type_title'];
            $aResult['order_status_title'] = $order_info[0]['order_status_title'];
            $aResult['fee_us'] = number_format($order_info[0]['fee_us'],0,'',',');
            $aResult['fee_karmozd'] = number_format($order_info[0]['fee_karmozd'],0,'',',');
            $aResult['fee_network'] = $order_info[0]['fee_network'];
            $aResult['amount_toman'] = number_format($order_info[0]['amount_toman'],0,'',',');
            $aResult['amount_usd'] = $order_info[0]['amount_usd'];
            $aResult['customer_invoice_number'] = $order_info[0]['customer_invoice_number'];
            $aResult['shaba'] = $order_info[0]['shaba'];
            $aResult['card_number'] = $order_info[0]['card_number'];
            $aResult['site_pay_date'] = $order_info[0]['site_pay_date'];
            $aResult['site_pay_time'] = $order_info[0]['site_pay_time'];
            $aResult['site_invoice_number'] = $order_info[0]['site_invoice_number'];
            $aResult['description'] = $order_info[0]['description'];
            $aResult['customer_pay_date'] = $order_info[0]['customer_pay_date'];
            $aResult['customer_pay_time'] = $order_info[0]['customer_pay_time'];
            $shaparak_fee = 0;
            if($order_info[0]['order_type'] === "1") { // sell to us
                if($order_info[0]['coin_id'] === "1") { //btc 
                    $pay_info = get_gourl_info_by_id($order_info[0]['customer_invoice_number']);
                    if(count($pay_info)) {                        
                        $aResult['addr'] = $pay_info[0]['addr'];
                        $aResult['txID'] = $pay_info[0]['txID'];
                        $aResult['txConfirmed'] = $pay_info[0]['txConfirmed'];
                    } else {
                        $aResult['addr'] = "";
                        $aResult['txID'] = "";
                        $aResult['txConfirmed'] = "";                        
                    }
                }
                if($order_info[0]['coin_id'] === "3") { //usdt
                    $pay_info = get_coinremitter_info_by_id($order_info[0]['customer_invoice_number']);
                    if(count($pay_info)) {                        
                        $aResult['addr'] = $pay_info[0]['address'];
                        $aResult['txID'] = $pay_info[0]['txid'];
                        $aResult['txConfirmed'] = $pay_info[0]['status_code'];
                    } else {
                        $aResult['addr'] = "";
                        $aResult['txID'] = "";
                        $aResult['txConfirmed'] = "";                        
                    }
                    
                }
            }
            if($order_info[0]['order_type'] === "2") { // buy from us
                $aResult['addr'] = $order_info[0]['customer_wallet_addr'];
                $aResult['txID'] = $order_info[0]['customer_txid'];
                if($order_info[0]['amount_toman'] <= 150000) 
                    $shaparak_fee = round($order_info[0]['amount_toman'] / 100);
                else
                    $shaparak_fee = 1500;
            }
            $aResult['fee_shaparak'] = number_format($shaparak_fee,0,'',',');            
            
        } else {
            $aResult['error'] = 'order not valid';            
        }        
    }
}
echo json_encode($aResult);  
?>