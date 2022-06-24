<?php
session_start();

header('Content-Type: application/json');
if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location: ../login/");
    exit(); 
}

include_once('global.php');
require '../binance-api/autoload.php';

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 7) ) {
        $aResult['error'] = 'Error in arguments!';
    }        
    else {        
        $api_key = validate_input($_POST['arguments'][0]);
        $secret_key = validate_input($_POST['arguments'][1]); 
        $exchange_id = validate_input($_POST['arguments'][2]); 
        $risk = validate_input($_POST['arguments'][3]); 
        $limitation = validate_input($_POST['arguments'][4]); 
        $time_trade = validate_input($_POST['arguments'][5]); 
        $coin_pair_id = validate_input($_POST['arguments'][6]); 
        
        $api = new Binance\API($api_key,$secret_key);
        $ticker = $api->prices();
        $balances = $api->balances($ticker);
        if(empty($balances)) {
            $aResult['error'] = 'key error';
        } else {
            $keys = array_keys($balances);
            $aResult['balances'] = array();
            foreach($keys as $row) {
                if($balances[$row]['available'] > 0) {
                    array_push($aResult['balances'],array($row,$balances[$row]['available']));                
                }
            }
            
            $aResult['ok'] = 'ok';            
        }
    }
}
echo json_encode($aResult);  
?>