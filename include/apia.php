<?php
/*
 * Get Balance Binance Exchange
 * Documentation https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md
 */
include('api.php');
/**
 * Get server time
 * the server time must be obtained to sign the requests curl
 * Time is the variable used for requests
 */
$ServerTimeUrl='https://api.binance.com/api/v1/time'; 
$ClassServerTime = new APIREST($ServerTimeUrl);
$CallServerTime = $ClassServerTime->call(array());
$DecodeCallTime= json_decode($CallServerTime);
$Time = $DecodeCallTime->serverTime;
$ApiKey=''; // the Api key provided by binance
$ApiSecret=''; // the Secret key provided by binance
$Timestamp = 'timestamp='.$Time; // build timestamp type url get
$Signature = hash_hmac('SHA256',$Timestamp ,$ApiSecret); // build firm with sha256
/**
 * Get balance
 * @var BalanceUrl is the url of the request
 * @var ClassBalance initializes the APIREST class
 * @var CallBalance request balance sheets, X-MBX-APIKEY is required by binance api
 */
$BalanceUrl='https://api.binance.com/api/v3/account?timestamp='.$Time.'&signature='.$Signature;
$ClassBalance = new APIREST($BalanceUrl);
$CallBalance= $ClassBalance->call(
	array('X-MBX-APIKEY:'.$ApiKey)
);

var_dump(json_decode($CallBalance,true));
//echo "$CallBalance";
?>