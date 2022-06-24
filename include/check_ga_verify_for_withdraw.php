<?php

session_start();

include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
include_once('mail.php');
include_once('google_2fa_generate.php');
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
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 4) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $vcode = validate_input($_POST['arguments'][0]);
        $amount = validate_input($_POST['arguments'][1]);
        $address = validate_input($_POST['arguments'][2]);
        $user_wallet_address = validate_input($_POST['arguments'][3]);
        $table = get_user_info_by_id($_SESSION['user_id']);
        $ga_code = $table[0]['ga_code'];
        if($ga_code != null) {
            
            $google2fa = new Google2FA;
            $result = $google2fa->verify_key($ga_code, $vcode);
            if($result) {
                $email = $table[0]['email'];

                $wallet_info = get_wallet_by_address_user_id($user_wallet_address,$_SESSION['user_id']);
                if(count($wallet_info)) {
                    $taker_address = false;
                    if($wallet_info[0]['wallet_coin_id'] === "3") {
                        $wallet_info_other = get_wallet_for_other($address,$_SESSION['user_id']);
                        if(count($wallet_info_other))
                            $taker_address = true;
                    } else 
                        $taker_address = validate_wallet_address($address);

                    if($taker_address) {
                        $wallet_id = $wallet_info[0]['id'];
                        $balance = balance($wallet_id);
                        $minimum_w = $network_fee = 0;
                        if($wallet_info[0]['wallet_coin_id'] === "1") {
                            $minimum_w = 0.001;
                            $network_fee = 0.0004;                    
                        }
                        if($wallet_info[0]['wallet_coin_id'] === "2") {
                            $user_wallet_address = 1;
                            $minimum_w = 2;
                        }
                        if($wallet_info[0]['wallet_coin_id'] === "3") {                    
                            $minimum_w = 100000;
                        }

                        if($amount >= $minimum_w) {
                            if($amount <= $balance) {
                                $confirm_code = "";
                                while($confirm_code == ""){
                                    $confirm_code = random_code(24);
                                    if(!is_confirm_code_unique($confirm_code))
                                        $confirm_code = "";           
                                }

                                $url = $url.'dashboard/confirm-withdraw/index.php?token='.$confirm_code;

                                // Token expiration
                                $expires = new DateTime('NOW');
                                $expires->add(new DateInterval('PT24H')); // 24 hour
                                $wallet_info = get_wallet_by_address_user_id($user_wallet_address,$_SESSION['user_id']);

                                $transaction = array();
                                $transaction['user_id'] = $_SESSION['user_id'];
                                $transaction['tr_date'] = jdate('Y-m-d','','','','en');
                                $transaction['tr_time'] = jdate('H:i:s','','','','en');
                                $transaction['order_id'] = "0";
                                $transaction['token_code'] = $confirm_code;
                                $transaction['status'] = "0";
                                $transaction['expire'] = $expires->format('U'); 
                                $transaction['amount'] = $amount;
                                $transaction['bed_bes'] = "2";
                                $transaction['serial'] = "";
                                $transaction['wallet_id'] = $wallet_id;
                                $transaction['description'] = "";
                                $transaction['babat_id'] = "2";            
                                $transaction['recipt_w_address'] = $address;

                                $last_id = insert_transaction($transaction);
                                if($last_id > 0) {
                                    $body = $url;
                                    $res = send_mail($no_reply_mail_address,$email,$no_reply_mail_address,$no_reply_mail_password,'لینک تایید برداشت',$body,null);
                                    error_log($res);
                                    $aResult['ok'] = 'ok';
                                } else 
                                    $aResult['error'] = 'insert error';
                            } else
                                $aResult['error'] = 'no balance';
                        } else {
                            $aResult['minimum_w'] = $minimum_w;
                            $aResult['error'] = 'amount minimum';
                        }
                    } else
                        $aResult['error'] = 'address invalid';
                } else
                    $aResult['error'] = 'user wallet invalid';
            } else 
                $aResult['error'] = 'code error';
        } else 
            $aResult['error'] = 'code error';
    }
}
echo json_encode($aResult); 
?>