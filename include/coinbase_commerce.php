<?php
if (!session_id()) {
    session_start();
}
include_once('bitex.php');
$admin_setting = get_setting();
$api = $admin_setting[0]['coinbase_api'];
$curl = curl_init();
$postFilds=array(
    'name'=>'eSaraafi',
    'description'=>$_SESSION['pay_info_description'],
    'pricing_type'=>'fixed_price', 
    'pwcb_enabled'=>'false',
    'local_price'=> array('amount'=>$_SESSION['pay_info_amount'], 'currency'=> 'BTC'),
'metadata'=>array('user_id'=>$_SESSION['pay_info_user_id'],'order_id'=>$_SESSION['pay_info_order_id'])
);
$postFilds=urldecode(http_build_query($postFilds));
curl_setopt_array($curl, 
    array(

        CURLOPT_URL => "https://api.commerce.coinbase.com/charges",       
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postFilds,
        CURLOPT_HTTPHEADER => array(
            "content-type: multipart/form-data",
            "X-CC-Api-Key: ".$api,
            "X-CC-Version: 2018-03-22"
        ),
    )
);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

$json = json_decode($response,true);

$hosted_url = $json['data']['hosted_url'];

header("Location: ".$hosted_url);
exit();
?>