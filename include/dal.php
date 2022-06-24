<?php
//session_start();
include_once('global.php');
include('db_inc.php');


/* insert a row to table. if row inserted succesfully return insert id or not return -1 
   $datas is an array with keys and values for table fields(key) and their values(value)*/ 
function insert($table_name,$datas) {  
    
    // Create connection
    $conn = ConnectToDatabase();
    if($conn === false)
        return -1;    

    // escape variables for security
    $new_datas = array();
    foreach($datas as $key => $value) {
        $new_datas[$key] = mysqli_real_escape_string($conn, $value);    
    }
    // retrieve the keys of the array (column titles)
    $fields = array_keys($new_datas);
    
    // build the query
    $sql = "INSERT INTO ".$table_name."
    (`".implode('`,`', $fields)."`)
    VALUES('".implode("','", $new_datas)."')";  
   //error_log($sql);
   if ($conn->query($sql) === TRUE) {
       $last_id = $conn->insert_id;
       if($last_id > 0){
           DisconnectFromDatabase();
           return $last_id;
       }
       else {
		   DisconnectFromDatabase();
           return -1;
	   }
   }
    else {
		DisconnectFromDatabase();
	    return -1;
	}    
}

// the where clause is left optional incase the user wants to delete every row!
function delte_by_id($table_name, $id){
    $conn = ConnectToDatabase();
    if($conn === false)
        return -1;
    
    // build the query
    $sql = "DELETE FROM ".$table_name." WHERE id = ".$id;

     // returen true for success update or false for fail update  
    if($conn->query($sql)){
        DisconnectFromDatabase();
        return true;        
    }
    else
    {
        DisconnectFromDatabase();
        return false; 
    }   
}

//whereSql must be like 'where filed = data' and so ex
function delte_by_where($table_name, $whereSQL){
    $conn = ConnectToDatabase();
    if($conn === false)
        return -1;
    
    // build the query
    $sql = "DELETE FROM ".$table_name." ".$whereSQL;

     // returen true for success update or false for fail update  
    if($conn->query($sql)){
        DisconnectFromDatabase();
        return true;        
    }
    else
    {
        DisconnectFromDatabase();
        return false; 
    }   
}

function update_by_id($table_name,$datas,$id){
    $conn = ConnectToDatabase();
    if($conn === false)
        return -1;
     // escape variables for security
    $new_datas = array();
    foreach($datas as $key => $value) {
        $new_datas[$key] = mysqli_real_escape_string($conn, $value);    
    }
    // retrieve the keys of the array (column titles)
    $fields = array_keys($new_datas);
    
    // build the query
    $sql = "UPDATE ".$table_name." SET ";
    $whereSQL = " WHERE id = ".$id;
    // loop and build the column /
    $sets = array();
    foreach($new_datas as $column => $value)
    {
         $sets[] = "`".$column."` = '".$value."'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;
    // returen true for success update or false for fail update 
    
    if($conn->query($sql)){
        DisconnectFromDatabase();
        return true;        
    }
    else
    {
        DisconnectFromDatabase();
        return false; 
    }   
} 

//whereSql must be like 'where filed = data' and so ex
function update_by_where($table_name,$datas,$whereSQL){
    $conn = ConnectToDatabase();
    if($conn === false)
        return -1;
     // escape variables for security
    $new_datas = array();
    foreach($datas as $key => $value) {
        $new_datas[$key] = mysqli_real_escape_string($conn, $value);    
    }
    // retrieve the keys of the array (column titles)
    $fields = array_keys($new_datas);
    
    // build the query
    $sql = "UPDATE ".$table_name." SET ";    
    // loop and build the column /
    $sets = array();
    foreach($new_datas as $column => $value)
    {
         $sets[] = "`".$column."` = '".$value."'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;
    // returen true for success update or false for fail update 
    
    if($conn->query($sql)){
        DisconnectFromDatabase();
        return true;        
    }
    else
    {
        DisconnectFromDatabase();
        return false; 
    }   
}    

function get_rows_count($table_name,$filed,$data){
    $conn = ConnectToDatabase();
    if($conn === false)
        return -1;
    $datai = mysqli_real_escape_string($conn, $data);    
    $sql = "SELECT * FROM ".$table_name." WHERE ".$filed." = '".$datai."'";
    $result = $conn->query($sql);
    return $result->num_rows; 
    DisconnectFromDatabase();
}

function get_all_select_by_where($table_name,$filed,$data){
    $conn = ConnectToDatabase();
    $table = array();
    if($conn === false)
        return $table;
    $datai = mysqli_real_escape_string($conn, $data);    
    $sql = "SELECT * FROM ".$table_name." WHERE ".$filed." = '".$datai."'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        while($row = mysqli_fetch_array($result)) {
            $table[] = $row;
        }
        DisconnectFromDatabase();
        return $table;
    }        
    else{
        DisconnectFromDatabase();
        return $table;
    }   
}

function get_all_select_by_query($query){
    //error_log($query);
    $conn = ConnectToDatabase();
    $table = array();
    if($conn === false)
        return $table;
           
    $result = $conn->query($query);
    if ($result->num_rows > 0){
        while($row = mysqli_fetch_array($result)) {
            $table[] = $row;
        }
        DisconnectFromDatabase();
        return $table;
    }        
    else{
        DisconnectFromDatabase();
        return $table;
    }
    
}
?>
