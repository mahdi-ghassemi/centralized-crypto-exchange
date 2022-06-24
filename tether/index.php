<?php
if (!session_id()) {
    ini_set( 'session.cookie_httponly', 1 );
    ini_set( 'session.cookie_secure', 1 );
    session_start();
}

$root_path = "../";


include_once($root_path.'include/global.php');
include_once($root_path.'include/jdf.php');
include_once($root_path.'include/bitex.php');

session_secure();
$user_login = false;
get_visitor_info($ipaddress,$page,$referrer,$useragent);
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به پرداخت تتر',$_SESSION['bitex_username']);
} else {
    header("location:".$root_path."login/");
    exit();
}

if(!isset($_SESSION['pay_info_order_id']) || !isset($_SESSION['pay_info_amount'])) {
    header("location:".$root_path."dashboard/");
    exit();    
}

$is_payment_exist = is_coinremitter_payment_exist($_SESSION['user_id'],$_SESSION['pay_info_order_id']);
include_once '../vendor/autoload.php';
use CoinRemitter\CoinRemitter;

if(!$is_payment_exist) {   

   $params = [
   'coin'=>'USDT',//coin for which you want to use this object.
   'api_key'=>'$2y$10$OWvhHRpxe8bhddMX0YHyg.PHSIUL/f/VrGO/HnCE9CgnnqgVhoHZq',//api key from coinremitter wallet
   'password'=>'shayaeel1351' // password for selected wallet
   ];
   $obj = new CoinRemitter($params);


   $param = [
    'amount'=>$_SESSION['pay_info_amount'],      //required.
    'notify_url'=>'https://esaraafi.ir/include/coinremitter_callback.php', //required,url on which you wants to receive notification,
    'name'=>'eSaraafi',//optional,
    //'currency'=>'usd',//optional,
    //'expire_time'=>'',//optional,
    //'description'=>'',//optional.
   ];

   $invoice  = $obj->create_invoice($param);

   $id = $invoice['data']['id'];
   $url = "";
   if($id != null) {
      $datas = array();
      $datas['coinmitter_id'] = $id;
      $datas['user_id'] = $_SESSION['user_id'];
      $datas['order_id'] = $_SESSION['pay_info_order_id'];
      $datas['coinmitter_id'] = $id;
      $datas['invoice_id'] = $invoice['data']['invoice_id'];
      $datas['merchant_id'] = $invoice['data']['merchant_id'];
      $datas['total_amount'] = $invoice['data']['total_amount'];
      $datas['paid_amount'] = $invoice['data']['paid_amount'];
      $datas['usd_amount'] = $invoice['data']['usd_amount'];
      $datas['coin'] = $invoice['data']['coin'];
      $datas['wallet_name'] = $invoice['data']['wallet_name'];
      $datas['address'] = $invoice['data']['address'];
      $datas['status'] = $invoice['data']['status'];
      $datas['status_code'] = $invoice['data']['status_code'];
      $datas['expire_on'] = $invoice['data']['expire_on'];
      $datas['invoice_date'] = $invoice['data']['invoice_date'];
      $datas['last_updated_date'] = $invoice['data']['last_updated_date'];
      $datas['url'] = $invoice['data']['url'];
      $last_id = insert_coinremitter($datas);
      if($last_id > 0) 
          $url = $invoice['data']['url'];
   }
} else {
    $pay_info = get_coinremitter_payment_by_order_id($_SESSION['user_id'],$_SESSION['pay_info_order_id']);
    $status = $pay_info[0]['status_code']; 
    $url = $pay_info[0]['url'];
}

$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];



?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>پرداخت تتر</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">    
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>

        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-@s home-content" uk-grid="">
               <?php if(!$is_payment_exist) {  ?>
                <div class="uk-alert-primary alert" uk-alert="">لطفاً مبلغ مشخص شده در کادر پرداخت زیر را به آدرسی که مشخص شده ارسال نمایید.</div>
                <div class="uk-alert-primary alert" uk-alert="">در صورت تمایل می توانید صفحه را ببندید یا به صفحه داشبورد برگردید، پس از پرداخت و تایید آن توسط شبکه بلاکچین، فاکتور شما به صورت خودکار تایید می گردد.</div> 
                <div class="uk-alert-primary alert" uk-alert=""> برای جلوگیری از مشکل  دابل اسپندینگ ، پرداخت شما باید 3 تاییدیه از شبکه بلاک چین دریافت نماید تا وضعیت سفارش شما تایید پرداخت مشتری گردد.این پروسه ممکن است حدود 1 ساعت طول بکشد.لطفاً صبور باشید.</div>               
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: در هنگام پرداخت ، کارمزد انتقال تتر از کیف پول خود را به مبلغ مشخص شده اضافه نمایید، چنانچه مبلغ واریزی با مبلغ مشخص شده در کادر زیر یکسان نباشد پرداخت شما برگشت خواهد خورد.</div>
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: آدرس کیف پول زیر بر اساس پروتکل OMNI ایجاد گردیده است.در هنگام انتقال به این نکته توجه داشته باشید.</div>
                
                <iframe class="iframe-pay" src="<?php echo $url; ?>">
                    
                </iframe>
                <?php } else {  
                if($status == "0") { ?>
                <div class="uk-alert-primary alert" uk-alert="">لطفاً مبلغ مشخص شده در کادر پرداخت زیر را به آدرسی که مشخص شده ارسال نمایید.</div>
                <div class="uk-alert-primary alert" uk-alert="">در صورت تمایل می توانید صفحه را ببندید یا به صفحه داشبورد برگردید، پس از پرداخت و تایید آن توسط شبکه بلاکچین، فاکتور شما به صورت خودکار تایید می گردد.</div> 
                <div class="uk-alert-primary alert" uk-alert=""> برای جلوگیری از مشکل  دابل اسپندینگ ، پرداخت شما باید 3 تاییدیه از شبکه بلاک چین دریافت نماید تا وضعیت سفارش شما تایید پرداخت مشتری گردد.این پروسه ممکن است حدود 1 ساعت طول بکشد.لطفاً صبور باشید.</div>               
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: در هنگام پرداخت ، کارمزد انتقال تتر از کیف پول خود را به مبلغ مشخص شده اضافه نمایید، چنانچه مبلغ واریزی با مبلغ مشخص شده در کادر زیر یکسان نباشد پرداخت شما برگشت خواهد خورد.</div>
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: آدرس کیف پول زیر بر اساس پروتکل OMNI ایجاد گردیده است.در هنگام انتقال به این نکته توجه داشته باشید.</div>
                
                <iframe class="iframe-pay" src="<?php echo $url; ?>">
                    
                </iframe>
                 <?php } else {  ?>
                 <div class="uk-alert-primary alert" uk-alert="">این سفارش قبلاً پرداخت گردیده است.</div>
                
                <?php } } ?>
            </div>
        </section>
        <?php add_footer($root_path); ?>
    </div>
</body>

</html>
