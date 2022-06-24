<?php
include_once('bitex.php');
include_once('jdf.php');

$order_temp = get_orders_by_status("1");


foreach($order_temp as $row) {
    $order_id = $row['id'];
    $order_date = $row['order_date'];
    $order_time = $row['order_time'];
    $start_date = strtotime($order_date.' '.$order_time);
    $now = strtotime(jdate('Y-m-d','','','','en').' '.jdate('H:i:s','','','','en'));
    $diff = $now - $start_date; 
    $hourdiff = ($diff / 3600);
    if($hourdiff > 1) {
        $datas = array();
        $datas['is_delete'] = "1";
        update_order($order_id,$datas);
        unset($datas);          
    }    
}
?>