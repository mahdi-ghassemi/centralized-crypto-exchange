<?php
session_start();
header('Content-Type: application/json');

$aResult = array();
if(!isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    $aResult['error'] = 'login';    
}

include_once('global.php');
include_once('bitex.php');


if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {   
        $description = validate_input($_POST["arguments"][0]);
        $ticket_id = validate_input($_POST["arguments"][1]);
        
        if(is_ticket_id_valid_for_reply($ticket_id,$_SESSION['user_id'])) {
            $ticket_info = array();
            $ticket_info = get_ticket_info_by_id($ticket_id);
            //$to_user_id = $ticket_info[0]['user_id'];
            //$geter_user_info = get_user_info_by_id($_SESSION['user_id']);


            $subject = $ticket_info[0]['subject'];
            $ticket_type = $ticket_info[0]['ticket_type'];
            $parent_id = $ticket_id;
            $root_id = $ticket_info[0]['root_id'];
            if($root_id === "0")
                $root_id = $parent_id;

            $datas = array();
            $datas['user_id'] = $_SESSION['user_id'];
            $datas['create_date'] = jdate('Y-m-d','','','','en');
            $datas['create_time'] = jdate('H:i:s','','','','en');
            $datas['subject'] = $subject;
            $datas['description'] = $description;
            $datas['ticket_type'] = $ticket_type;
            $datas['status'] = "1";
            $datas['parent_id'] = $parent_id;
            $datas['root_id'] = $root_id;    
            $datas['sender'] = "s";
            if($_SESSION['access_level_id'] === "1")
                $datas['sender'] = "u";        
            $datas['is_new'] = "0";
            //$datas['to_user_id'] = $to_user_id;

            $last_id = insert_ticket($datas);
            if($last_id > 0) {
                insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ثبت تیکت',$_SESSION['bitex_username'] .' - '.$last_id);
                if(isset($_SESSION['ticket_temp_filename'])) {
                    $file_temp = array();
                    $file_temp = $_SESSION['ticket_temp_filename'];
                        unset($_SESSION['ticket_temp_filename']);
                    $targetPath = "../tickets-files/".$last_id."/";
                    if(!file_exists($targetPath))
                        mkdir($targetPath);
                    foreach($file_temp as $filename){
                        $data_file = array();
                        $data_file['ticket_id'] = $last_id;
                        $data_file['file_name'] = $filename;
                        $id = insert_ticket_file($data_file);
                        rename('../temp-files/'.$filename,$targetPath.$filename);                    
                    }
                }
                $aResult['ok'] = 'ok';
                $aResult['ticket_id'] = $last_id; 
                $aResult['ticket_type'] = $ticket_type; 
                $aResult['subject'] = $subject; 
                $aResult['description'] = $description; 
                $aResult['create_date'] = $datas['create_date']; 
                $aResult['create_time'] = $datas['create_time'];

            } else
                $aResult['error'] = 'insert error';            
        } else
            $aResult['error'] = 'id error';
    }
}
echo json_encode($aResult);
?>