<?php
include_once("robot.php");

$furl = 'https://api.binance.com/api/v3/ticker/price';
$data = '';
if( ini_get('allow_url_fopen') ) {
    $data = file_get_contents($furl);    
} else {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $furl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $data = curl_exec($curl);
    curl_close($curl);
}
$json = json_decode($data);
$price = $symbol = "";
if(!empty($json)) {
    foreach($json as $obj) {        
        if($obj->symbol == 'BTCUSDT'){
            $price = $obj->price;
            $symbol = $obj->symbol;
            insert_price($price,$symbol,'binance_price');
            
        }
        if($obj->symbol == 'ETHUSDT'){
            $price = $obj->price;
            $symbol = $obj->symbol;
            insert_price($price,$symbol,'binance_price');
        }
            
      
        $price = $symbol = "";       
       
    }
}
?>