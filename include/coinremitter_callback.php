<?php 
header('Content-Type: application/json');
include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
include_once('mail.php');


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    $coinmitter_id = validate_input($_POST['id']);
    $invoice_id = validate_input($_POST['invoice_id']);
    $merchant_id = validate_input($_POST['merchant_id']);
    $status_code = validate_input($_POST['status_code']);
    $status = validate_input($_POST['status']);
    $txid = validate_input($_POST['payment_history'][0]['txid']);
    $total_amount = validate_input($_POST['total_amount'][0]);
    $paid_amount = validate_input($_POST['paid_amount'][0]);
    $last_updated_date = validate_input($_POST['last_updated_date']);
    
    $pay_info = get_coinremitter_payment($coinmitter_id,$invoice_id,$merchant_id);
    if(count($pay_info)) {
        $order_id = $pay_info[0]['order_id'];
        $id = $pay_info[0]['id'];
        
        $datas = array();
        $datas['status_code'] = $status_code;
        $datas['status'] = $status;
        $datas['txid'] = $txid;
        $datas['total_amount'] = $total_amount;
        $datas['paid_amount'] = $paid_amount;
        $datas['last_updated_date'] = $last_updated_date;
        $res = update_coinremitter_payment($id,$datas);
        
        unset($datas);
        if($status_code == "1" && $res) {
            $datas['status'] = "3";
            $datas['customer_invoice_number'] = $coinmitter_id;
            $datas['customer_txid'] = $txid;
            $datas['is_delete'] = "0";
            $datas['customer_pay_date'] = jdate('Y-m-d','','','','en');
            $datas['customer_pay_time'] = jdate('H:i:s','','','','en');
            update_order($order_id,$datas);
            $res = send_mail($no_reply_mail_address,$to_mail,$no_reply_mail_address,$no_reply_mail_password,'Coinremitter Pay Confirmed','order id: '.$order_id,null);
        }
    }
 
}
?>