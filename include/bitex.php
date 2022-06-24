<?php
include_once('dal.php');

function get_coins_table() {
    $query = "SELECT * FROM coins";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;        
}

function get_wallet_coins_table() {
    $query = "SELECT * FROM wallet_coins";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;        
}

function get_setting() {
    $query = "SELECT * FROM setting WHERE id = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_usd_price() {
    $query = "SELECT * FROM usd_price ORDER BY id DESC LIMIT 2";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_user_info_by_username($username){
    $query = "SELECT * FROM users WHERE username = '".$username."'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_user_exchange_balance($user_id) {
    $user_exchange = array();
    $user_exchange = get_user_exchange($user_id);
    $balanse = array();
    if(count($user_exchange)) {
        foreach($user_exchange as $exchange) {
            $exchange_id = $exchange['exchange_id'];
            $api_key = $exchange['api_key'];
            $secret_key = $exchange['secret_key'];
            $title = $exchange['title'];
            if($exchange_id == "1") {
                $api = new Binance\API($api_key,$secret_key);
                $ticker = $api->prices();
                $balances = $api->balances($ticker);
                print_r($balances,true);
            }
        }
        
    }
    
    return $balanse;
}

function get_user_exchange($user_id) {
    $query = "SELECT * FROM user_exchange WHERE user_id = ".$user_id." AND status = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_user_info_by_username_and_email($username,$email){
    $query = "SELECT * FROM users WHERE username = '".$username."' AND email = '".$email."'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_user_bank_account_by_user_id($user_id){
    $query = "SELECT * FROM bank_account WHERE user_id = ".$user_id." AND is_delete = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_bank_list(){
    $query = "SELECT * FROM bank_name  order by CONVERT(title USING utf8) COLLATE utf8_persian_ci";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_security_question_list(){
    $query = "SELECT * FROM security_question  order by CONVERT(title USING utf8) COLLATE utf8_persian_ci";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function is_refer_code_unique($refer_code){
    $query = "SELECT * FROM users WHERE refer_code_left = '".$refer_code."' OR refer_code_right = '".$refer_code."'";
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table) > 0)
        return false;
    else
        return true;    
}

function insert_new_user($user_info){
    $last_id = insert('users',$user_info);
    if($last_id > 0)
        return $last_id;
    else
        return -1;
}

function insert_coinremitter($datas) {
    $last_id = insert('coinremitter_payment',$datas);
    if($last_id > 0)
        return $last_id;
    else
        return -1;    
}

function is_coinremitter_payment_exist($user_id,$order_id) {
    $query = "SELECT * FROM coinremitter_payment WHERE user_id = ".$user_id." AND order_id = ".$order_id;
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table) > 0)
        return true;
    else
        return false;      
}

function get_coinremitter_payment_by_order_id($user_id,$order_id) {
    $query = "SELECT * FROM coinremitter_payment WHERE user_id = ".$user_id." AND order_id = ".$order_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function is_gourl_payment_exist($user_id,$order_id) {
    $query = "SELECT * FROM crypto_payments WHERE userID = '".$user_id."' AND orderID = '".$order_id."'";
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table) > 0)
        return true;
    else
        return false;     
}

function get_coinremitter_payment($coinmitter_id,$invoice_id,$merchant_id) {
    $query = "SELECT * FROM coinremitter_payment WHERE coinmitter_id = '".$coinmitter_id."' AND invoice_id = '".$invoice_id."' AND merchant_id = '".$merchant_id."'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_gourl_payment_by_order_id($user_id,$order_id) {
    $query = "SELECT * FROM crypto_payments WHERE userID = '".$user_id."' AND orderID = '".$order_id."'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_coinremitter_info_by_id($coinmitter_id) {
    $query = "SELECT * FROM coinremitter_payment WHERE coinmitter_id = '".$coinmitter_id."'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function insert_new_order($order_info){
    $last_id = insert('orders',$order_info);
    if($last_id > 0)
        return $last_id;
    else
        return -1;
}

function insert_bot($datas) {
    $last_id = insert('robot',$datas);
    if($last_id > 0)
        return $last_id;
    else
        return -1;    
}

function get_coin_balance($coin_type) {
    $query = "SELECT * FROM coins WHERE id = ".$coin_type;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]['balance'];     
}

function get_toman_balance() {
    $query = "SELECT * FROM setting WHERE id = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]['toman_balance'];    
}

function get_bot_exchange() {
    $query = "SELECT * FROM exchange_bot";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_robots_by_user_id($user_id) {
    $query = "SELECT * FROM vw_robot WHERE user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function dateDiffInDays($date1, $date2)  
{ 
    // Calulating the difference in timestamps 
    $diff = strtotime($date2) - strtotime($date1); 
      
    // 1 day = 24 hours 
    // 24 * 60 * 60 = 86400 seconds 
    return abs(round($diff / 86400)); 
}

function get_user_subscribe_plan_by_userid($user_id) {
    $query = "SELECT * FROM user_subscribe_plan WHERE user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function update_robot($id,$datas) {
    if(update_by_id('robot',$datas,$id))
        return true;
    else
        return false;       
}

function can_user_get_bot($limitation,$user_id) {
    $result = false;
    $query = "SELECT * FROM user_subscribe_plan WHERE user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table) == 0) {
        $expire_date_unix = strtotime("+7 day");
        $expire_date = date('Y-m-d', $expire_date_unix);
        $datas = array();
        $datas['user_id'] = $user_id;
        $datas['plan_id'] = "3";
        $datas['create_date'] = date("Y-m-d");
        $datas['create_time'] = date("H:i:s");
        $datas['expire_date'] = $expire_date;
        $datas['status'] = "1";
        $datas['expire_time'] = date("H:i:s");
        $id = insert_user_subscribe_plan($datas);
        if($id > 0) 
            $result = true;
    } else {
        $expire_date = $table[0]['expire_date'];
        $expire_time = $table[0]['expire_time'];
        $today = date("Y-m-d");
        $time = date("H:i:s");
        $date1 = new DateTime($today.' '.$time); 
        $date2 = new DateTime($expire_date.' '.$expire_time);
        if($date1 > $date2)
            $result = false;
        else {
            $plan_id = $table[0]['plan_id'];
            $plan_info = get_plan_info_by_id($plan_id);
            $max_bot_count = $plan_info[0]['max_bot'];
            if($max_bot_count === "-1") 
                $result = true;
            else {
                if($limitation == "1") {
                    $smart_bot = $plan_info[0]['smart_bot'];
                    if($smart_bot == 0) 
                        $result = false;
                    else {
                        $smart_bot_count = get_bot_count($user_id,$limitation);
                        if($smart_bot_count < $smart_bot) 
                            $result = true;
                        else
                            $result = false;                            
                    }                    
                } 
                if($limitation == "2") {
                    $limit_bot = $plan_info[0]['limit_bot'];
                    if($limit_bot == 0) 
                        $result = false;
                    else {
                        $limit_bot_count = get_bot_count($user_id,$limitation);
                        if($limit_bot_count < $limit_bot) 
                            $result = true;
                        else
                            $result = false;                            
                    }
                }
            }
        }        
    }    
    return $result;
}

function get_plan_info_by_id($plan_id) {
    $query = "SELECT * FROM robot_subcribe_plan WHERE id = ".$plan_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_bot_count($user_id,$limitation) {
    $query = "SELECT * FROM robot WHERE user_id = ".$user_id." AND limitation = ".$limitation;
    $table = array();
    $table = get_all_select_by_query($query);
    return count($table);    
}

function get_user_info_by_email($email){
    $table = array();
    $table = get_all_select_by_where('users','email',$email);
    return $table;
}

function insert_login_info($login_info){
    $last_id = insert('login_log',$login_info);
    if($last_id > 0)
        return $last_id;
    else
        return -1;
}

function insert_user_subscribe_plan($datas) {
    $last_id = insert('user_subscribe_plan',$datas);
    if($last_id > 0)
        return $last_id;
    else
        return -1;    
}

function get_announcement_not_show() {
    $query = "SELECT * FROM announcement WHERE showing = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_user_info_by_id($user_id){
    $query = "SELECT * FROM users WHERE id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_user_bank_confirmed_info_by_id($user_id){
    $query = "SELECT * FROM bank_account WHERE user_id = ".$user_id." AND status = 2 AND is_delete = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;     
}

function get_user_bank_info_by_id($user_id) {
    $query = "SELECT * FROM bank_account WHERE user_id = ".$user_id." AND is_delete = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_user_wallet_info($user_id) {
    $query = "SELECT * FROM vw_wallet WHERE user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function update_users($id,$datas) {
    if(update_by_id('users',$datas,$id))
        return true;
    else
        return false;    
}

function update_order($id,$datas) {
    if(update_by_id('orders',$datas,$id))
        return true;
    else
        return false;    
}

function update_idpay($id,$datas) {
    if(update_by_id('idpay_payments',$datas,$id))
        return true;
    else
        return false;     
}

function is_confirm_code_unique($confirm_code) {
    $query = "SELECT * FROM vw_transactions WHERE token_code = '".$confirm_code."'";
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table)) 
        return false;
    else
        return true;    
}

function validate_wallet_address($address){
    $res = true;
    
    $decoded = decodeBase58($address);

    $d1 = hash("sha256", substr($decoded,0,21), true);
    $d2 = hash("sha256", $d1, true);

    if(substr_compare($decoded, $d2, 21, 4)){
        $res = false;
    }
    return $res;
}
function decodeBase58($input) {
        $alphabet = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
 
        $out = array_fill(0, 25, 0);
        for($i=0;$i<strlen($input);$i++){
                if(($p=strpos($alphabet, $input[$i]))===false){
                        throw new \Exception("invalid character found");
                }
                $c = $p;
                for ($j = 25; $j--; ) {
                        $c += (int)(58 * $out[$j]);
                        $out[$j] = (int)($c % 256);
                        $c /= 256;
                        $c = (int)$c;
                }
                if($c != 0){
                    throw new \Exception("address too long");
                }
        }
 
        $result = "";
        foreach($out as $val){
                $result .= chr($val);
        }
 
        return $result;
}

function update_coinremitter_payment($id,$datas) {
    if(update_by_id('coinremitter_payment',$datas,$id))
        return true;
    else
        return false;      
}

function insert_madrak($datas){
    $last_id = insert('users_madrak',$datas); 
    return $last_id;
}

function insert_bank_account($datas){
    $last_id = insert('bank_account',$datas);
    return $last_id;
}

function insert_idpay_payment($datas) {
    $last_id = insert('idpay_payments',$datas);
    return $last_id;    
}

function get_users_count() {
    $query = "SELECT COUNT(id) as 'count' FROM users";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]['count'];    
}

function get_balance($wallet_id) {
    
}

function get_bitcoin_rate() {
    
}

function get_user_orders($user_id) {
    $query = "SELECT * FROM vw_orders WHERE user_id = ".$user_id." AND is_delete = 0 ORDER BY order_date DESC,order_time DESC" ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_order_info_by_id_user_id_status($order_id,$user_id,$status) {
    $query = "SELECT * FROM vw_orders WHERE id = ".$order_id." AND user_id = ".$user_id." AND  status = ".$status." AND  is_delete = 0 ORDER BY order_date DESC,order_time DESC" ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_order_info_by_id($order_id) {
    $query = "SELECT * FROM vw_orders WHERE id = ".$order_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_idpay_info_by_id($id,$order_id,$user_id) {
    $query = "SELECT * FROM idpay_payments WHERE idpay_id = '".$id."' AND order_id = ".$order_id." AND user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_order_info_by_id_userid($id,$user_id) {
    $query = "SELECT * FROM vw_orders WHERE id = ".$id." AND user_id = ".$user_id." AND  is_delete = 0" ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_gourl_info_by_id($id) {
    $query = "SELECT * FROM crypto_payments WHERE paymentID = ".$id;    
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_order_count_by_type($order_type) {
    $query = "SELECT COUNT(id) as 'count' FROM orders WHERE order_type = ".$order_type;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]['count'];
}

function get_order_sum() {
    $query = "SELECT SUM(amount_usd) as 'sum_amount' FROM orders";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]['sum_amount'];    
}

function get_order_type() {
    $query = "SELECT * FROM order_type" ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_orders_by_status($status) {
    $query = "SELECT * FROM orders WHERE status = ".$status;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_coin_type() {
    $query = "SELECT * FROM coins" ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_coin_pairs() {
    $query = "SELECT * FROM coin_pair" ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_coin_pairs_by_exchange_id($exchange_id) {
    $query = "SELECT * FROM coin_pair WHERE exchange_id = ".$exchange_id ;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function calculat($amount,$coin) {
    $aResult = array();
    $setting = array();
    $setting = get_setting();

    $bitex_fee = $setting[0]['fee_bitex'];

    $usd_price_info = array();
    $usd_price_info = get_usd_price();

    $buy_from_us = $usd_price_info[0]['buy_from_us'];
    $sell_to_us = $usd_price_info[0]['sell_to_us'];
    
    $aResult['buy_from_us'] = $buy_from_us;
    $aResult['sell_to_us'] = $sell_to_us;
    
    $aResult['bitex_fee'] = $bitex_fee;
    
    
    if($coin === "1") {
        $furl = 'https://api.binance.com/api/v3/ticker/price';
        $data = '';
        if( ini_get('allow_url_fopen') ) {
            $data = file_get_contents($furl);    
        } else {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $furl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $data = curl_exec($curl);
            curl_close($curl);
        }
        $json = json_decode($data);
        $eth = $btc = $ripple = $BCHABC = $ltc = $xmr = $zec = $dash = 0;
        if(!empty($json)) {
            foreach($json as $obj) {
                if($obj->symbol == 'BTCUSDT')
                    $btc = $obj->price;
                if($obj->symbol == 'ETHUSDT')
                    $eth = $obj->price;
                if($obj->symbol == 'XRPUSDT')
                    $ripple = $obj->price;
                if($obj->symbol == 'BCHABCUSDT')
                    $BCHABC = $obj->price;
                if($obj->symbol == 'LTCUSDT')
                    $ltc = $obj->price;
                if($obj->symbol == 'XMRUSDT')
                    $xmr = $obj->price;
                if($obj->symbol == 'ZECUSDT')
                    $zec = $obj->price;
                if($obj->symbol == 'DASHUSDT')
                    $dash = $obj->price;
            }
        }
        $aResult['buy'] = round(($amount * $btc * $buy_from_us) + ((($amount * $btc * $buy_from_us) * $bitex_fee) / 100 ));
        
        $aResult['sell'] = round(($amount * $btc * $sell_to_us) - ((($amount * $btc * $sell_to_us) * $bitex_fee) / 100 ));
        
        $aResult['amount_usd'] = round(($amount * $btc ),2);
        $aResult['fee_unit'] = $btc;
        
        
    } elseif($coin === "2") {
        $wmz = 1;
        $aResult['buy'] = round(($amount * $wmz * $buy_from_us) + ((($amount * $wmz * $buy_from_us) * $bitex_fee) / 100 ));
        
        $aResult['sell'] = round(($amount * $wmz * $sell_to_us) - ((($amount * $wmz * $sell_to_us) * $bitex_fee) / 100 ));
        
        $aResult['amount_usd'] = round(($amount * $wmz ),2);
        $aResult['fee_unit'] = $wmz;
        
    } elseif($coin === "3") {
        $usdt = 1;
        $aResult['buy'] = round(($amount * $usdt * $buy_from_us) + ((($amount * $usdt * $buy_from_us) * $bitex_fee) / 100 ));
        
        $aResult['sell'] = round(($amount * $usdt * $sell_to_us) - ((($amount * $usdt * $sell_to_us) * $bitex_fee) / 100 ));
        
        $aResult['amount_usd'] = round($amount * $usdt,2) ;
        $aResult['fee_unit'] = $usdt;
    }
    
    return $aResult;
}

function is_order_valid_for_user_delete($id,$user_id) {
    $query = "SELECT * FROM orders WHERE id = ".$id." AND user_id = ".$user_id." AND status = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table))
        return true;
    else
        return false;    
}

function get_bot_by_id_user_id($bot_id,$user_id) {
    $q = "select * from robot where user_id = ".$user_id." AND id = '".$bot_id."'";
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;
}

function get_bot_by_status($status) {
    $q = "select * from robot where status = ".$status;
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;    
}

function is_order_valid_for_user($id,$user_id) {
    $query = "SELECT * FROM orders WHERE id = ".$id." AND user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table))
        return true;
    else
        return false;    
}

function get_ticket_type() {
    $q = "select * from ticket_type";
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;    
}

function insert_ticket($datas) {
    $last_id = insert('ticket',$datas);
    return $last_id;    
}

function insert_ticket_file($data_file) {
    $last_id = insert('ticket_file',$data_file);
    return $last_id;    
}

function get_emial_ticket_to() {
    $query = "SELECT * FROM support_email";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_ticket_files($ticket_id) {
    $query = "SELECT * FROM ticket_file WHERE ticket_id = ".$ticket_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}

function get_ticket_type_title($ticket_type) {
    $query = "SELECT * FROM ticket_type WHERE id = ".$ticket_type;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]['title'];    
}

function get_user_root_tickets($user_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE user_id = ".$user_id." AND root_id = 0 order by create_date desc,create_time desc";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}

function get_user_tickets_by_root_id($root_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE (root_id = ".$root_id." OR id = ".$root_id.") order by create_date desc,create_time desc";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}

function update_ticket_readed($root_id) {
    $datas = array();
    $datas['is_new'] = "1";
    $counter = 0;
    $where_sql = " where root_id = ".$root_id." AND is_new = 0 AND sender <> 'u'";
    $res = update_by_where('ticket',$datas,$where_sql);
}

function update_ticket_readed_admin($root_id) {
    $datas = array();
    $datas['is_new'] = "1";
    $counter = 0;
    $where_sql = " where (root_id = ".$root_id." OR id = ".$root_id." OR parent_id = ".$root_id.") AND is_new = 0 AND sender <> 's'";
    $res = update_by_where('ticket',$datas,$where_sql);
}

function get_all_tickets() {
    $query = "SELECT * FROM vw_user_ticket WHERE root_id = 0 order by create_date desc,create_time desc";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}

function get_new_ticket_count($user_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE user_id = ".$user_id." AND root_id = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    $counter = 0;
    if(count($table)) {
        foreach($table as $row) {
            $id = $row['id'];
            $ticket_info = get_tickets_by_root_id_from_admin($id);
            foreach($ticket_info as $t) {
                if($t['is_new'] === "0")
                    $counter++;
            }
            unset($ticket_info);
        }
        
    }
    return $counter; 
}

function get_new_ticket_count_for_admin() {
    $query = "SELECT * FROM vw_user_ticket WHERE sender = 'u' AND is_new = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    return count($table); 
}

function get_new_ticket_count_for_user($user_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE sender = 's' AND is_new = 0 AND to_user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return count($table);     
}

function get_tickets_by_root_id_from_admin($id) {
    $query = "SELECT * FROM vw_user_ticket WHERE root_id = ".$id." AND sender = 's'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}

function get_tickets_by_root_id_from_admin_unread($id) {
    $query = "SELECT * FROM vw_user_ticket WHERE root_id = ".$id." AND sender = 's' AND is_new = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}

function get_tickets_by_root_id_from_user_unread($id) {
    $query = "SELECT * FROM vw_user_ticket WHERE (id = ".$id." or parent_id = ".$id." or root_id = ".$id.") AND sender = 'u' AND is_new = 0";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table; 
}


function  get_ticket_replies($root_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE root_id = ".$root_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return count($table);    
}

function is_ticket_id_valid($id,$user_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE id = ".$id." AND user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($query);
    if(count($table))
        return true;
    else
        return false;    
}

function is_ticket_id_valid_for_reply($id,$user_id) {
    $result = false;
    $user_tickets = get_user_root_tickets($user_id);
    $ticket_info = get_ticket_info_by_id($id);
    if(count($ticket_info)) {
        $root_id = $ticket_info[0]['root_id'];
        foreach($user_tickets as $row) {
            if($row['id'] === $root_id) {
                $result = true;
                break;            
            }
        }
    } else
        $result = false;
    
    return $result;       
}

function get_ticket_info_by_id($ticket_id) {
    $query = "SELECT * FROM vw_user_ticket WHERE id = ".$ticket_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function delete_order($order_id) {
    $whereSQL = ' WHERE id = '.$order_id;
    $table_name = 'orders';
    return delte_by_where($table_name, $whereSQL);
}


function generate_wallet_address($username){
    $hash = password_hash($username,PASSWORD_DEFAULT);
    $p = substr($hash,10,24);    
    return strrev($p);
}

function insert_wallet($datas){   
    $insert_id = insert('wallet',$datas); 
    return $insert_id;
}

function get_user_wallets($user_id){
    $q = "select * from wallet where user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;
}

function balance($wallet_id){
    $balanse = "0";
    $query_bed = 'SELECT SUM(amount) AS bed_sum FROM transactions WHERE wallet_id = '.$wallet_id.' AND bed_bes = 1';
    $query_bes = 'SELECT SUM(amount) AS bes_sum FROM transactions WHERE wallet_id = '.$wallet_id.' AND bed_bes = 2';
    $table_bed = array();
    $table_bed = get_all_select_by_query($query_bed);
   
    $bed_sum = $table_bed[0]['bed_sum'];
    if($bed_sum === null)
        $bed_sum = 0;
    $table_bes = array();
    $table_bes = get_all_select_by_query($query_bes);
    $bes_sum = $table_bes[0]['bes_sum'];
    if($bes_sum === null)
        return $bed_sum;
    else
        return ($bed_sum - $bes_sum);
}

function get_wallet_by_address($wallet_address){
    $q = "select * from wallet where address = '".$wallet_address."'";
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;    
}

function update_withdraw($id,$datas) {
    if(update_by_id('transactions',$datas,$id))
        return true;
    else
        return false;      
}

function get_withdraw_confirm_info($confirm_code){
    $query = "SELECT * FROM  transactions WHERE token_code = '".$confirm_code."' AND expire >= '".time()."'";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_wallet_by_address_user_id($wallet_address,$user_id){
    $q = "select * from vw_wallet where address = '".$wallet_address."' and user_id = ".$user_id;
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;    
}

function get_wallet_for_other($wallet_address,$user_id) {
    $q = "select * from vw_wallet where address = '".$wallet_address."' and user_id <> ".$user_id;
    $table = array();
    $table = get_all_select_by_query($q);
    return $table;
}

function insert_transaction($transaction){    
    $insert_id = insert('transactions',$transaction);
    return $insert_id;    
}


function get_wallet_transactions($wallet_id){
    $query = 'SELECT * FROM vw_transactions WHERE wallet_id = '.$wallet_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_wallet_deposit($wallet_id) {
    $query = 'SELECT * FROM vw_transactions WHERE wallet_id = '.$wallet_id.' AND babat_id = 1';
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_all_deposit_by_userid($user_id) {
    $query = 'SELECT * FROM vw_transactions WHERE user_id = '.$user_id.' AND babat_id = 1';
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;       
}

function get_all_withdraw_by_userid($user_id) {
    $query = 'SELECT * FROM vw_transactions WHERE user_id = '.$user_id.' AND babat_id = 2';
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;       
}

function update_wallet($wallet_id,$datas) {
     update_by_id("wallet",$datas,$wallet_id);
}

function get_payment_request_all() {
    $query = 'SELECT * FROM vw_payment_request';
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function get_payment_request_by_status($status) {
    $query = 'SELECT * FROM vw_payment_request WHERE status = '.$status;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
}

function update_payment_request($id,$datas) {
     update_by_id("payment_request",$datas,$id);
}

function update_transactions($id,$datas) {
     update_by_id("transactions",$datas,$id);
}

function get_payment_by_transaction_id($transaction_id){
    $query = 'SELECT * FROM payment_request WHERE transaction_id = '.$transaction_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_payment_by_id($id){
    $query = 'SELECT * FROM vw_payment_request WHERE id = '.$id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

function get_transaction_by_orderid_userid_babatid($order_id,$fl_id,$babat_id) {
    $query = 'SELECT * FROM transactions WHERE order_id = '.$order_id.' AND user_id = '.$fl_id.' AND babat_id = '.$babat_id;
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;
    
}

function get_all_transaction(){
    $query = 'SELECT * FROM vw_transactions';
    $table = array();
    $table = get_all_select_by_query($query);
    return $table;    
}

?>