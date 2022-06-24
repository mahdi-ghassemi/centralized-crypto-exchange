<?php
include_once("connection.php");
include_once("global.php");
include_once("jdf.php");


function sendsms_ippanel_pattern($mobile_number,$patern_code,$datas) {
    try {
        $client = new SoapClient(""); 
       
	    $user = "";
        $pass = "";
        
	    $fromNum = ""; 
	    $toNum = array($mobile_number); 
	    $pattern_code = $patern_code; 
        $input_data = $datas;
	   
	    $status = $client->sendPatternSms($fromNum,$toNum,$user,$pass,$pattern_code,$input_data);
        return $status;        
    }
    catch (SoapFault $ex) {
        return $ex->faultstring;
    } 
}

function sendsms_ippanel_com($mobile_number,$msg){
    try {
        $client = new SoapClient(""); 
       
	    $user = "";
        $pass = "";
        
	    $fromNum = ""; 
        $toNum = array($mobile_number);
        $messageContent = $msg;
	    $op  = "send";
	   
	
	   $time = null;
	
	   $status = $client->SendSMS($fromNum,$toNum,$messageContent,$user,$pass,$time,$op);
	   return $status;
    }
    catch (SoapFault $ex) {
        return $ex->faultstring;
    }    
}

function sendsms_ghasedak_otp($verification_code,$mobile_number) { 
    $params = array(
            'type' => 1,
            'param1' => $verification_code,
            'receptor' => $mobile_number,
            'template' => 'verification2020',
        );
    $fields_string = http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, '');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "apikey: ",
    "cache-control: no-cache",
    "content-type: application/x-www-form-urlencoded"
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;    
} 
?>