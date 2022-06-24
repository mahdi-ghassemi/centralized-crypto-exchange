<?php
if (!session_id()) {
    ini_set( 'session.cookie_httponly', 1 );
    ini_set( 'session.cookie_secure', 1 );
    session_start();
}

$root_path = "../../";



include_once($root_path.'include/global.php');
include_once($root_path.'include/jdf.php');
include_once($root_path.'include/bitex.php');

session_secure();

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location:".$root_path."login/");
    exit(); 
}

if( !isset($_SESSION['pay_info_amount']) ) {
    header("location:".$root_path."dashboard/");
    exit();    
}

$error = 0;
$error_msg = "تراکنش موفق";

if( !isset($_POST) || empty($_POST) ) {
   $error = 1; // no data from shaparak
}

$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];

$status = validate_input($_POST['status']);
$track_id = validate_input($_POST['track_id']);
$id = validate_input($_POST['id']);
$order_id = validate_input($_POST['order_id']);
$amount = validate_input($_POST['amount']);
$card_no = validate_input($_POST['card_no']);
$hashed_card_no = validate_input($_POST['hashed_card_no']);
$date = validate_input($_POST['date']);

if($status < 10) {
    $error = 3; // not confirm
    $error_msg = "تراکنش ناموفق";    
} else {
    if($status == 10 ) {
        $bank_card_info = get_user_bank_confirmed_info_by_id($_SESSION['user_id']);
        $bank_error = true;
        foreach($bank_card_info as $bank) {
            if($bank['card_hash'] === $hashed_card_no) {
                $bank_error = false;
                break;
            }
        }
        
        if(!$bank_error) {
            $pay_info = get_idpay_info_by_id($id,$order_id,$_SESSION['user_id']);
            if(count($pay_info)) {
                $params = array(
                    'id' => $id,
                    'order_id' => $order_id,
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, '');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-API-KEY: ',
                'X-SANDBOX: 0'
                ));
                $result = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($result);
                $status = $json->status;
                //error_log(print_r($json,true));
                if($status == 100) {
                    //$date_verify = $json->verify[0];
                    $error = 0;
                    $datas = array();
                    $datas['track_id'] = $track_id;
                    $datas['card_no'] = $card_no;
                    $datas['status'] = "1";
                    $datas['hashed_card_no'] = $hashed_card_no;
                    $datas['date_pay'] = $date;
                    $datas['date_verify'] = $date;
                    update_idpay($pay_info[0]['id'],$datas);
                    unset($datas);
                    $datas['customer_pay_date'] = jdate('Y-m-d','','','','en');
                    $datas['customer_pay_time'] = jdate('H:i:s','','','','en');
                    $datas['customer_invoice_number'] = $pay_info[0]['id'];
                    $datas['status'] = "3";            
                    update_order($order_id,$datas);
                    $order_info = get_order_info_by_id($order_id);
                    if($order_info[0]['order_type'] === "2" && $order_info[0]['coin_id'] === "2") { //webmoney to customer
                        $amount_wm = $order_info[0]['amount'];
                        $customer_wallet_addr = $order_info[0]['customer_wallet_addr'];
                        $res_pay = pay_to_customer($order_id,$amount_wm,$customer_wallet_addr);
                        if($res_pay !== -1) {
                             $datas['site_pay_date'] = jdate('Y-m-d','','','','en');
                             $datas['site_pay_time'] = jdate('H:i:s','','','','en');
                             $datas['site_invoice_number'] = $res_pay;
                             $datas['status'] = "4";            
                             update_order($order_id,$datas);
                        }
                    }
                } 
            } else {
                $error = 2; // pay id not valid  
                $error_msg = "تراکنش مورد تایید نمی باشد";
            }    
        } else {
            $error = 4; //card not confirm
            $error_msg = "شماره کارت بانکی که با آن پرداخت انجام شده مورد تایید نمی باشد.وجه کسر شده به حساب شما برگشت داده خواهد شد.";
        }
    }    
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>صرافی آسان</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>

        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s dashboard-content" uk-grid="">

            <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                <div class="uk-card-body">
                    <br>
                    <div class="row-right">
                        <div>
                            <span class="shabnam">نتیجه تراکنش :</span>
                            <span id="pay_span" <?php if($error == 0) echo 'class="shabnam success"'; else echo 'class="shabnam elzami"'; ?>><?php echo $error_msg; ?></span>
                        </div>
                    </div>
                    <div class="row-right">
                        <div>
                            <span class="shabnam">رسید پرداخت :</span>
                            <span id="fullname_span" class="shabnam elzami"><?php echo $track_id; ?></span>
                        </div>
                    </div>
                    <br>
                    <div class="row-right">
                        <div>
                            <span class="shabnam">شماره فاکتور :</span>
                            <span id="fullname_span" class="shabnam elzami"><?php echo $order_id; ?></span>
                        </div>
                    </div> 
                    <br>
                    <div class="row-right">
                        <div>
                            <span class="shabnam">مبلغ فاکتور :</span>
                            <span id="total_cost_span" class="yekan elzami"><?php echo number_format($amount, 0, '.', ','); ?> </span>&nbsp;&nbsp;<span class="shabnam">ریال</span>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                    <br>
                </div>
                <div id="footer" class="uk-card-footer uk-text-center">
                    <div class="row">
                        <div class="uk-text-center"><a href="<?php echo $root_path; ?>dashboard/" class="uk-button uk-button-small pay_btn">برو به داشبورد</a></div>
                    </div>
                </div>
            </div>
        </div>
        <?php add_footer($root_path); ?>
    </div>    
</body>
</html>