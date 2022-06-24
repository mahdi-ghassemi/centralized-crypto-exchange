<?php
header('Content-Type: application/json');
include_once('bitex.php');
require '../binance-api/autoload.php';

$user_id = "10";
$user_exchange = array();
$user_exchange = get_user_exchange($user_id);
$available_balanse = array();
if(count($user_exchange)) {
    foreach($user_exchange as $exchange) {
        $exchange_id = $exchange['exchange_id'];
        $api_key = $exchange['api_key'];
        $secret_key = $exchange['secret_key'];
        $title = $exchange['title'];
        if($exchange_id == "1") {
            $api = new Binance\API($api_key,$secret_key);
            $ticker = $api->prices();
            $balances = $api->balances($ticker);
            $keys = array_keys($balances);
            
            foreach($keys as $row) {
                if($balances[$row]['available'] > 0) {
                    array_push($available_balanse,array($exchange_id,$row,$balances[$row]['available']));                
                }
            }            
        }
    }
}

echo json_encode($available_balanse);
?>