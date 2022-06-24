<?php
session_start();
include_once('global.php');
include_once('bitex.php');

header('Content-Type: application/json');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        if(strtolower(validate_input($_POST['arguments'][1])) === strtolower($_SESSION["vercode"])) {
            $mobile = validate_input($_POST['arguments'][0]);                  
            $email = validate_input($_POST['arguments'][2]);
            $table2 = get_user_info_by_username($mobile);
            $table = array();
            if($email !== "0") {
                $table = get_user_info_by_email($email);                
            }
            if(count($table) > 0)
                $aResult['error'] = 'email exist';
            else if(count($table2) > 0){ 
                $aResult['error'] = 'username exist'; 
            }
            else
              $aResult['OK'] = 'OK';            
        } else
            $aResult['error'] = 'cap error'; 
    }
}
echo json_encode($aResult);  
?>