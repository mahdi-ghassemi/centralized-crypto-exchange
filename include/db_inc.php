<?php
/**
 * Database configuration
 * 
 */
define('MYSQL_HOST','localhost');
define('MYSQL_USER','');
define('MYSQL_PASSWORD','');
define('MYSQL_DB','');


$db = '';

function ConnectToDatabase(){
    global $db;
    $db = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD);
    mysqli_select_db($db,MYSQL_DB) or die(mysqli_error($db));
    if (mysqli_connect_errno()){
        return false;
    }else{
        mysqli_query($db,"SET NAMES 'utf8'"); 
        mysqli_query($db,"SET CHARACTER SET utf8");
        mysqli_query($db,"SET character_set_results=utf8");
        mysqli_query($db,"SET character_set_client=utf8");
        mysqli_query($db,"SET character_set_connection=utf8");
        mysqli_query($db,"SET character_set_server=utf8");
        mysqli_query($db,"SET character_set_database=utf8");
        return $db;
    }
}
function DisconnectFromDatabase(){
    global $db;
    mysqli_close($db);
}

?>