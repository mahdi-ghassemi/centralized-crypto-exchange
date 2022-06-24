<?php
if (!session_id()) {
    session_start();
}
include_once('global.php');
include_once('mail.php');
include_once('bitex.php');

header('Content-Type: application/json');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}

if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 6) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else { 
        $ticket_id = validate_input($_POST['arguments'][0]);
        $ticket_type = validate_input($_POST['arguments'][1]);
        $subject = validate_input($_POST['arguments'][2]);
        $description = validate_input($_POST['arguments'][3]);
        $create_date = validate_input($_POST['arguments'][4]);
        $create_time = validate_input($_POST['arguments'][5]);
        
        $email_to = get_emial_ticket_to();
        $attachments = get_ticket_files($ticket_id);
        $ticket_type_title = get_ticket_type_title($ticket_type);
        $msg = new_ticket_from_user_body($_SESSION['bitex_username'],$ticket_id,$subject,$ticket_type_title,$create_date,$create_time,$description);
        $mail_result = send_mail_for_ticket($no_reply_mail_address,$email_to,'New Ticket Received',$msg,null,$attachments,$ticket_id);
    }        
}
echo json_encode($aResult);  
?>