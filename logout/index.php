<?php
if (!session_id()) {    
    session_start();
}
include_once("../include/global.php");
if(isset($_SESSION['user_id'])) {
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'خروج',$_SESSION['bitex_username']);
    session_destroy();
}
    
header("Location: ../");
exit();
?>