<?php
session_start();


include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');


header('Content-Type: application/json');
if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

$aResult = array();

if(isset($_POST["webmoney_data"]) && !empty($_POST["webmoney_data"]) && isset($_SESSION['pay_info_order_id'])) {
    $data = $_POST["webmoney_data"];    
    $amount = $data['amount'];
    $LMI_PAYMENT_NO = $data['LMI_PAYMENT_NO'];
    $LMI_SYS_INVS_NO = $data['LMI_SYS_INVS_NO'];
    $LMI_SYS_TRANS_NO = $data['LMI_SYS_TRANS_NO'];
    $LMI_SYS_TRANS_DATE = $data['LMI_SYS_TRANS_DATE'];
    
    
    $datas = array();
    $datas['status'] = "3";
    $datas['customer_pay_date'] = jdate('Y-m-d','','','','en');
    $datas['customer_pay_time'] = jdate('H:i:s','','','','en');
    $datas['customer_invoice_number'] = $LMI_SYS_TRANS_NO;
    $order_id = $_SESSION['pay_info_order_id'];
    $res = update_order($order_id,$datas);

    if($res) {
        $aResult['ok'] = 'ok';    
    } else {
        $aResult['error'] = 'update error';
    }
    
} else {
    $aResult['error'] = 'data error';    
}
echo json_encode($aResult);
?>
