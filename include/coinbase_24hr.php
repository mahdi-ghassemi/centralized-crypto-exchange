<?php
include_once("robot.php");
include_once("jdf.php");

$exchange_info = get_exchange_info_by_name('Coinbase');

$coin_pairs = get_coin_pairs_by_exchange_id($exchange_info[0]['id']);

foreach($coin_pairs as $coin_pair) {
    $furl = 'https://api.pro.coinbase.com/products/'.$coin_pair['symbol'].'/stats';
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
    if(!empty($json)) {        
        $datas = array();        
        $datas['lastPrice'] = $json->last;
        $datas['openPrice'] = $json->open;        
        $datas['highPrice'] = $json->high;
        $datas['lowPrice'] = $json->low;
        $datas['volume'] = $json->volume;
        $datas['volume_30day'] = $json->volume_30day;       
        $datas['exchange_id'] = $exchange_info[0]['id'];
        $datas['coin_pair_id'] = $coin_pair['id'];
        $datas['insert_date'] = jdate('Y-m-d','','','','en');
        $datas['insert_time'] = jdate('H:i:s','','','','en');
        $last_id = insert_data('24hr',$datas);
        unset($datas); 
    }
}
?>