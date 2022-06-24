<?php

session_start();


header('Content-Type: application/json');

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
include_once('mail.php');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 4) ) {
        $aResult['error'] = 'Error in arguments!';
    }        
    else {        
        $card_number = validate_input($_POST['arguments'][0]);
        $shaba = validate_input($_POST['arguments'][1]); 
        $bank_name = validate_input($_POST['arguments'][2]); 
        $acc_number = validate_input($_POST['arguments'][3]); 
       
        $datas = array();
        $datas['user_id'] = $_SESSION['user_id'];
        $datas['shaba'] = $shaba;
        $datas['card_number'] = $card_number;
        $datas['acc_number'] = $acc_number;
        $datas['card_hash'] = strtoupper(hash('sha256', $card_number));
        $datas['bank_name'] = $bank_name;
        $datas['status'] = "0";
        $datas['is_default'] = "0";
        $datas['is_delete'] = "0";
        $datas['insert_date'] = jdate('Y-m-d','','','','en');
        $datas['insert_time'] = jdate('H:i:s','','','','en');
        $last_id = insert_bank_account($datas);
        if($last_id > 0) {
            insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'به روز رسانی اطلاعات بانکی',$_SESSION['bitex_username']);
            $aResult['ok'] = 'ok';
            $aResult['shaba'] = $shaba;
            $aResult['card_number'] = $card_number;
            $aResult['bank_name'] = $bank_name;
            $aResult['acc_number'] = $acc_number;
            $aResult['status'] = 'در حال بررسی';
            $res = send_mail($no_reply_mail_address,$to_mail,$no_reply_mail_address,$no_reply_mail_password,'Update Bank Info','userid: '.$_SESSION['user_id'].' username: '.$_SESSION['bitex_username'],null);
            
        }  else
            $aResult['error'] = 'nok';
            
        
    }
}
echo json_encode($aResult);  
?>