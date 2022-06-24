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
include_once('mail.php');


if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'درخواست ثبت سفارش',$_SESSION['bitex_username']);
        $user_info = get_user_info_by_id($_SESSION['user_id']);
        $mobile_confirm = $user_info[0]['mobile_confirm'];
        $identity_confirm = $user_info[0]['identity_confirm'];
        $selfi_confirm = $user_info[0]['selfi_confirm'];
        $user_bank_info = get_user_bank_confirmed_info_by_id($_SESSION['user_id']);
        
        $id_confirm = $bank_confirm = false;
        
        if( count($user_bank_info) ) 
            $bank_confirm = true;
        else 
            $aResult['error'] = 'bank_error';
        
        if($mobile_confirm == "1" && $identity_confirm == "1" && $selfi_confirm == "1") 
            $id_confirm = true;
        
        $amount = validate_input($_POST['arguments'][0]);
        $customer_wallet_addr = validate_input($_POST['arguments'][1]);
        $order_type = "2";
        $wallet_info = get_wallet_by_address_user_id($customer_wallet_addr,$_SESSION['user_id']);
        $coin_type = "";
        if(count($wallet_info)) {
            if($wallet_info[0]['symbol'] === "BTC")
                $coin_type = "1";
            if($wallet_info[0]['symbol'] === "USDT")
                $coin_type = "3";            
        } else {
            $aResult['error'] = 'wallet_invalid';
            
        }
                
        if($order_type === "2" && !$id_confirm) {
            $aResult['error'] = 'id_error';            
        }
        
        if($order_type === "2") {
            $coin_balance = get_coin_balance($coin_type);
            if($amount > $coin_balance)
                $aResult['error'] = 'coin_balance_error';            
        }
        
        if( !isset($aResult['error']) ) {
            $calc = array();
            $calc = calculat($amount,$coin_type);       
       
            $datas = array();
            $datas['user_id'] = $_SESSION['user_id'];
            $datas['order_type'] = $order_type;
            $datas['coin_id'] = $coin_type;
            $datas['amount'] = $amount;                    
            $datas['bank_account_id'] = $user_bank_info[0]['id'];        
            $datas['order_date'] = jdate('Y-m-d','','','','en');;
            $datas['order_time'] = jdate('H:i:s','','','','en');;
            $datas['status'] = "1";
            $datas['amount_usd'] = $calc['amount_usd'];
            $datas['fee_unit'] = $calc['fee_unit'];
            $datas['amount_toman'] = "";
            $datas['customer_wallet_addr'] = $customer_wallet_addr;
            $datas['ip_address'] = $_SESSION['ip_address'];
            
            if($order_type === "1") {
                $datas['amount_toman'] = $calc['sell'];
                $datas['fee_us'] = $calc['sell_to_us'];
                $datas['fee_karmozd'] = ( $calc['sell'] * $calc['bitex_fee'] ) / 100;                
            }
            if($order_type === "2") {
                $datas['amount_toman'] = $calc['buy'];                
                $datas['fee_us'] = $calc['buy_from_us'];
                $datas['fee_karmozd'] = ( $calc['buy'] * $calc['bitex_fee'] ) / 100; 
            }
            
            if($calc['buy'] >= 100 && $calc['buy'] <= 49998000) {
                $last_id = insert_new_order($datas);
                insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ثبت سفارش',$_SESSION['bitex_username'] .' - '.$last_id);
                if ($last_id > 0) {
                    $res = send_mail($no_reply_mail_address,$to_mail,$no_reply_mail_address,$no_reply_mail_password,'New Order','userid: '.$_SESSION['user_id'].' username: '.$_SESSION['bitex_username'].' order id: '.$last_id,null);
                    $aResult['ok'] = 'ok';
                    $aResult['amount_toman'] = $datas['amount_toman'];
                } else {
                    $aResult['error'] = 'insert error';           
                }
            }
            else {
                $aResult['error'] = 'amount error';                
            }     
        }        
    }
}
echo json_encode($aResult);  
?>