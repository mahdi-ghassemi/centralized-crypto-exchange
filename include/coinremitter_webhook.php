<?php
header('Content-Type: application/json');
include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
include_once('mail.php');
include_once('sendsms.php');


if($_SERVER['REQUEST_METHOD'] == 'POST'){    
    $coinmitter_id = validate_input($_POST['id']);
    $txid = validate_input($_POST['txid']);
    $explorer_url = validate_input($_POST['explorer_url']);
    $merchant_id = validate_input($_POST['merchant_id']);
    $type = validate_input($_POST['type']);
    $coin_short_name = validate_input($_POST['coin_short_name']);
    $wallet_id = validate_input($_POST['wallet_id']);
    $wallet_name = validate_input($_POST['wallet_name']);
    $address = validate_input($_POST['address']);
    $amount = validate_input($_POST['amount']);
    $confirmations = validate_input($_POST['confirmations']);
    $date = validate_input($_POST['date']);    
    
    
    $wallet_info = get_wallet_by_address($address);
    if(count($wallet_info) && $type === 'receive') {        
        $user_id = $wallet_info[0]['user_id'];
        $user_info = get_user_info_by_id($user_id);
        $wal_id = $wallet_info[0]['id']; 
        $transaction = array();
        $transaction['tr_date'] = jdate('Y-m-d','','','','en');
        $transaction['tr_time'] = jdate('H:i:s','','','','en');
        $transaction['user_id'] = $user_id;        
        $transaction['order_id'] = "0";        
        $transaction['amount'] = $amount;
        $transaction['bed_bes'] = "1";
        $transaction['serial'] = $txid;
        $transaction['wallet_id'] = $wal_id;
        $transaction['description'] = 'واریزی';
        $transaction['babat_id'] = "1";
        $last_id = insert_transaction($transaction);
        if($last_id > 0) {            
            $res = send_mail($no_reply_mail_address,$user_info[0]['email'],$no_reply_mail_address,$no_reply_mail_password,'Deposit Confirmed','amount: '.$amount,null);
        }
    } 
}
?>