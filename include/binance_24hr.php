<?php
include_once("robot.php");
include_once("jdf.php");

$exchange_info = get_exchange_info_by_name('Binance');

$coin_pairs = get_coin_pairs_by_exchange_id($exchange_info[0]['id']);

foreach($coin_pairs as $coin_pair) {
    $furl = 'https://api.binance.com/api/v3/ticker/24hr?symbol='.$coin_pair['symbol'];
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
        $datas['priceChange'] = $json->priceChange;
        $datas['priceChangePercent'] = $json->priceChangePercent;
        $datas['weightedAvgPrice'] = $json->weightedAvgPrice;
        $datas['prevClosePrice'] = $json->prevClosePrice;
        $datas['lastPrice'] = $json->lastPrice;
        $datas['lastQty'] = $json->lastQty;
        $datas['bidPrice'] = $json->bidPrice;
        $datas['bidQty'] = $json->bidQty;
        $datas['askPrice'] = $json->askPrice;
        $datas['askQty'] = $json->askQty;
        $datas['openPrice'] = $json->openPrice;
        $datas['highPrice'] = $json->highPrice;
        $datas['lowPrice'] = $json->lowPrice;
        $datas['volume'] = $json->volume;
        $datas['quoteVolume'] = $json->quoteVolume;
        $datas['openTime'] = $json->openTime;
        $datas['closeTime'] = $json->closeTime;
        $datas['count_trade_id'] = $json->count;
        $datas['exchange_id'] = $exchange_info[0]['id'];
        $datas['coin_pair_id'] = $coin_pair['id'];
        $datas['insert_date'] = jdate('Y-m-d','','','','en');
        $datas['insert_time'] = jdate('H:i:s','','','','en');
        $last_id = insert_data('24hr',$datas);
        unset($datas);            
      
    }
}
?>