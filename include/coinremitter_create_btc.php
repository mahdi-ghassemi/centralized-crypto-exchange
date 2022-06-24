<?php 
session_start();
include_once('bitex.php');
$root_path = "../";
session_secure();
$user_login = false;
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;    
} else {
    header("location:".$root_path."login/");
    exit();
}


include_once '../vendor/autoload.php';
use CoinRemitter\CoinRemitter;

$params = [
   'coin'=>'USDT',//coin for which you want to use this object.
   'api_key'=>'',//api key from coinremitter wallet
   'password'=>'' // password for selected wallet
];
$obj = new CoinRemitter($params);


$param = [
    'amount'=>$_SESSION['pay_info_amount'],      //required.
    'notify_url'=>'', //required,url on which you wants to receive notification,
    'name'=>'eSaraafi',//optional,
    //'currency'=>'usd',//optional,
    //'expire_time'=>'',//optional,
    //'description'=>'',//optional.
];

$invoice  = $obj->create_invoice($param);

$id = $invoice['data']['id'];

if($id != null) {
    $datas = array();
    $datas['coinmitter_id'] = $id;
    $datas['invoice_id'] = $invoice['data']['invoice_id'];
    $datas['merchant_id'] = $invoice['data']['merchant_id'];
    $datas['total_amount'] = $invoice['data']['total_amount'];
    $datas['paid_amount'] = $invoice['data']['paid_amount'];
    $datas['usd_amount'] = $invoice['data']['usd_amount'];
    $datas['coin'] = $invoice['data']['coin'];
    $datas['wallet_name'] = $invoice['data']['wallet_name'];
    $datas['address'] = $invoice['data']['address'];
    $datas['status'] = $invoice['data']['status'];
    $datas['status_code'] = $invoice['data']['status_code'];
    $datas['expire_on'] = $invoice['data']['expire_on'];
    $datas['invoice_date'] = $invoice['data']['invoice_date'];
    $datas['last_updated_date'] = $invoice['data']['last_updated_date'];
    $last_id = insert_coinremitter($datas);
   
    
}



echo 'id:'.$invoice['data']['id'];
echo '<br>';

var_dump($invoice);
?>