<?php
session_start();
include_once('global.php');
include_once('bitex.php');

header('Content-Type: application/json');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

$token = validate_input($_POST['arguments'][2]);
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = [
    'secret' => '6LfvkuUUAAAAAFkBE6dPm08pdWZTR68oIOU3fACq',
    'response' => $token    
];
$header = [
  'Content-type'   => 'application/x-www-form-urlencoded',
];
$options = array(
    'http' => array(
        'header' => "Content-type:application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)        
        )
);
if(in_array('curl',get_loaded_extensions())){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);//10002
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//10015
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//10023    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//64
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//19913
    //curl_setopt($ch, CURLOPT_CONNECTIONTIMEOUT, 45);//78
    if(!$res = curl_exec($ch)){
        $error = curl_error($ch);
        $aResult['error'] = 'g_recap_error'; 
    } else {
        $res = json_decode($res,true);
        if($res['success'] != true) {
            $aResult['error'] = 'g_recap_error';    
        }
    }    
}


if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        if(strtolower(validate_input($_POST['arguments'][0])) === strtolower($_SESSION["vercode"])) {
            //$mobile = validate_input($_POST['arguments'][0]);                  
            $email = validate_input($_POST['arguments'][1]);
            $table = get_user_info_by_email($email);                       
            if(count($table) > 0)
                $aResult['error'] = 'email exist';            
            else
              $aResult['OK'] = 'OK';            
        } else
            $aResult['error'] = 'cap error'; 
    }
}
echo json_encode($aResult);  
?>