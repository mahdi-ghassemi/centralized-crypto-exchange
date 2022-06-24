<?php
session_start();
header('Content-Type: application/json');

$aResult = array();
if(!isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    $aResult['error'] = 'login';    
}

include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
include_once '../vendor/autoload.php';
use CoinRemitter\CoinRemitter;

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {        
        $alias = validate_input($_POST['arguments'][0]);
        $coin_id = validate_input($_POST['arguments'][1]);
        $user_id = $_SESSION['user_id'];
        $addr = "";
        if($coin_id === "1") {
            $params = [                
               'coin'=>'BTC',
               'api_key'=>'$2y$10$5esgXWzGQcE7UeCwKq9qPuKFIIGDfX3jqgohmzdD9zQVg00u2rYHS',
               'password'=>'shayaeel1351' 
                ];
            $obj = new CoinRemitter($params);
            $param = [
                'label'=> $user_id
            ];
            $address = $obj->get_new_address($param);
            $addr = $address['data']['address'];
        }
        
        if($coin_id === "2") {
            $params = [                
               'coin'=>'USDT',
               'api_key'=>'$2y$10$OWvhHRpxe8bhddMX0YHyg.PHSIUL/f/VrGO/HnCE9CgnnqgVhoHZq',
               'password'=>'shayaeel1351' 
                ];
            $obj = new CoinRemitter($params);
            $param = [
                'label'=> $user_id
            ];
            $address = $obj->get_new_address($param);
            $addr = $address['data']['address'];
        }
        
        if($coin_id === "3") {
            $addr =  generate_wallet_address($_SESSION['bitex_username']);
        }
        
        $datas = array();
        $datas['address'] = $addr;
        $datas['alias'] = $alias;
        $datas['user_id'] = $user_id;        
        $datas['create_date'] = jdate('Y-m-d','','','','en');
        $datas['create_time'] = jdate('H:i:s','','','','en');
        $datas['status'] = '1';
        $datas['wallet_coin_id'] = $coin_id;
        $last_id = insert_wallet($datas);
        if($last_id > 0) {
            insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ایجاد کیف پول',$_SESSION['bitex_username'].' - '.$addr);
            $aResult['ok'] = 'ok';
        } else 
            $aResult['error'] = 'insert';
    }
}

echo json_encode($aResult);  
?>