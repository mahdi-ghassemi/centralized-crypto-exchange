<?php
if (!session_id()) {    
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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به داشبورد',$_SESSION['bitex_username']);
}

if(!$user_login) {
    header("Location: ".$root_path);
    exit();
}

if(!isset($_SESSION['access_level_id'])){
    header("Location: ".$root_path);
    exit();
}

$user_type = (int)$_SESSION['access_level_id'];

if($user_type === 10) {
    header("Location: ".$root_path."admin-panel/");
    exit();
}

$announcement = array();
$announcement = get_announcement_not_show();

$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);

$user_bank_info = get_user_bank_confirmed_info_by_id($_SESSION['user_id']);
$user_bank_info_all = get_user_bank_info_by_id($_SESSION['user_id']);

$mobile_confirm = $user_info[0]['mobile_confirm'];
$identity_confirm = $user_info[0]['identity_confirm'];
$selfi_confirm = $user_info[0]['selfi_confirm'];
$tow_fa = $user_info[0]['tow_fa'];
$code_meli = $user_info[0]['code_meli'];

$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];


$orders = array();
$orders = get_user_orders($_SESSION['user_id']);

$order_type_array = array();
$order_type_array = get_order_type();

$coin_type_array = array();
$coin_type_array = get_coin_type();

$all_confirm = $id_confirm = $bank_confirm = false;

if($identity_confirm == "1" && $selfi_confirm == "1") 
    $id_confirm = true;
if( count($user_bank_info) ) 
    $bank_confirm = true;

if($id_confirm && $bank_confirm && $mobile_confirm === "1") 
    $all_confirm = true;

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
$eth = $btc = $ripple = $BCHABC = $ltc = $xmr = $zec = $dash = $usdt = 0; //USDSUSDT  
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
        if($obj->symbol == 'USDSUSDT')
            $usdt = $obj->price;
    }
}
$setting = array();
$setting = get_setting();

$bitex_fee = $setting[0]['fee_bitex'];

$usd_price_info = array();
$usd_price_info = get_usd_price();

$buy_from_us = $usd_price_info[0]['buy_from_us'];
$sell_to_us = $usd_price_info[0]['sell_to_us'];

$wmz = 1;
$usdt = 1;
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>داشبورد | صرافی آسان</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/datatables.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">


    <script src="<?php echo $root_path; ?>asset/js/dashboard.js?version=<?php echo $version; ?>"></script>

    <script src="<?php echo $root_path; ?>asset/js/datatables.min.js"></script>


</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>


        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s dashboard-content" uk-grid="">
            <?php if($mobile_confirm === "0") { ?>
            <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a>شماره همراه شما هنوز تایید نگردیده است.برای انجام سفارشات شماره همراه شما باید تایید گردد.جهت تایید شماره همراه <a class="sell_link" href="<?php echo $root_path; ?>dashboard/identity/index.php?p=mobile"> اینجا </a> را کلیک نمایید.
            </div>
            <?php } ?>
            <?php if ($code_meli == null) { ?>
            <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a>اطلاعات هویتی شما هنوز ثبت نگردیده است.جهت ثبت اطلاعات هویتی می توانید از منوی احراز هویت اقدام نمایید یا<a class="sell_link" href="<?php echo $root_path; ?>dashboard/identity/index.php?p=identity"> اینجا </a> را کلیک نمایید.
            </div>
            <?php } else { ?>
            <?php if(! $id_confirm) { ?>
            <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a>هویت شما هنوز تایید نگردیده است.جهت انجام سفارش خرید لازم است که هویت شما تایید گردد.
            </div>
            <?php } } ?>
            <?php if(! count($user_bank_info_all)) { ?>
            <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a>برای انجام سفارشات فروش به ما فقط کافیست حساب بانکی شما تایید گردد.برای ثبت حساب بانکی خود از منوی تنظیمات بانکی اقدام نمایید <a class="sell_link" href="<?php echo $root_path; ?>dashboard/identity/index.php?p=bank"> اینجا </a> را کلیک نمایید.
            </div>
            <?php } else {  ?>

            <?php if(! $bank_confirm ) { ?>
            <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a> اطلاعات حساب بانکی شما هنوز تایید نگردیده است.
            </div>
            <?php } }?>


            <?php if($tow_fa == "0") { ?>
            <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a> ورود دو مرحله ای برای حساب کاربری شما غیرفعال است. در هر لحظه می توانید از منوی تنظیمات امنیتی نسبت به فعال کردن آن و افزایش امنیت حساب کاربری خود اقدام نمایید یا<a class="sell_link" href="<?php echo $root_path; ?>dashboard/identity/"> اینجا </a> را کلیک نمایید.
            </div>
            <?php } ?>
            <?php foreach($announcement as $announce) { ?>
            <div class="uk-alert-primary alert" uk-alert="" <?php if($announce[ 'bg_color_code'] !="" ) echo 'style="background-color:'.$announce[ 'bg_color_code']. '";'; ?>><a class="uk-alert-close" uk-close></a><span <?php if($announce[ 'color_code'] !="" ) echo 'style="color:'.$announce[ 'color_code']. '";'; ?>><?php echo $announce['title']; ?></span>
            </div>
            <?php } ?>
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                <div class="uk-card uk-card-hover uk-card-default order-list">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img class="uk-border-circle" width="40" height="40" src="<?php echo $root_path; ?>asset/img/shopping_cart.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">سفارشات</h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                            <div>
                                <button id="new_order" class="uk-button uk-button-default new_order_btn">
                                    ثبت سفارش جدید
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body" style="padding: 28px 6px;">
                        <table id="orders" class="uk-table uk-table-hover uk-table-striped uk-table-divider">
                            <thead>
                                <tr>
                                    <th>شماره</th>
                                    <th class="expand">نوع</th>
                                    <th class="expand">مقدار</th>
                                    <th class="expand">ارز</th>
                                    <th class="expand">مبلغ (تومان)</th>
                                    <th class="expand">تاریخ</th>
                                    <th class="expand">وضعیت</th>
                                    <th class="expand"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $row) { ?>
                                <tr class="font-yekan">
                                    <td><span><?php echo $row['id']; ?></span></td>
                                    <td><span><?php echo $row['order_type_title']; ?></span></td>
                                    <td><span><?php echo $row['amount']; ?></span></td>
                                    <td><span><?php echo $row['name_fa']; ?></span></td>
                                    <td><span><?php echo number_format($row['amount_toman'],0,"","،"); ?></span></td>
                                    <td><span> <?php  echo $row['order_date'];?> </span></td>
                                    <td><?php if($row['status'] <= 2 ) { ?>
                                        <i class="fa fa-spinner fa-spin marron"></i>&nbsp;&nbsp;
                                        <?php } ?>

                                        <span><?php   echo $row['order_status_title']; ?></span></td>
                                    <td>
                                        <?php if($row['status'] === "1") { ?>
                                        <button id="py<?php echo $row['id']; ?>" class="uk-button uk-button-default uk-button-small pay_btn pay"><i class="fa fa-spinner fa-spin spinner-onload"></i> پرداخت</button>
                                        <button id="or<?php echo $row['id']; ?>" class="uk-button uk-button-default uk-button-small cancel_btn cnl"><i class="fa fa-spinner fa-spin spinner-onload"></i> کنسل</button>
                                        <?php } else { ?>
                                        <button id="in<?php echo $row['id']; ?>" class="uk-button uk-button-default uk-button-small orange-btn info">مشاهده فاکتور</button>

                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-3@s dashboard-content" uk-grid="">
               <?php foreach($coin_type_array as $coin) { ?>
                <div class="uk-card uk-card-hover uk-card-default card-coin">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img class="uk-border-circle" width="40" height="40" src="../asset/img/<?php echo $coin['image']; ?>">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom input-font"><?php echo $coin['name_fa'];?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                       <div class="card-coin-body-div1">
                                <div class="deposit-body-div3">
                                    <div>
                                        <span class="shabnam elzami">نرخ واحد به دلار :</span>
                                        <br>
                                        <span class="shabnam elzami">خرید از ما :</span>
                                        <br>
                                        <span class="shabnam elzami">فروش به ما :</span>
                                        <br>
                                        <span class="shabnam elzami">موجودی :</span>
                                    </div>
                                    &nbsp;&nbsp;
                                    <div>
                                        <span class="shabnam"><?php 
                                            if($coin['symbol'] === "BTC")
                                                echo get_persian_numbers(number_format($btc, 2 , '/',','));
                                            if($coin['symbol'] === "WMZ")
                                                echo get_persian_numbers($wmz);
                                            if($coin['symbol'] === "USDT")
                                                echo get_persian_numbers($usdt);
                                            ?></span>
                                        <br>
                                        <span class="shabnam"><?php 
                                            if($coin['symbol'] === "BTC")
                                                echo get_persian_numbers(number_format(($btc * $buy_from_us) + ((($btc * $buy_from_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "WMZ")
                                                echo get_persian_numbers(number_format(($wmz * $buy_from_us) + ((($wmz * $buy_from_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "USDT")
                                                echo get_persian_numbers(number_format(($usdt * $buy_from_us) + ((($usdt * $buy_from_us) * $bitex_fee) / 100 )), 2 , '/',',');                        
                                            ?></span>&nbsp;<span class="shabnam elzami">تومان</span>
                                        <br>
                                        <span class="shabnam"><?php 
                                            if($coin['symbol'] === "BTC")
                                                echo get_persian_numbers(number_format(($btc * $sell_to_us) - ((($btc * $sell_to_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "WMZ")
                                                echo get_persian_numbers(number_format(($wmz * $sell_to_us) - ((($wmz * $sell_to_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "USDT")
                                                echo get_persian_numbers(number_format(($usdt * $sell_to_us) - ((($usdt * $sell_to_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            ?></span>&nbsp;<span class="shabnam elzami">تومان</span>
                                        <br>
                                        <span class="shabnam direction-ltr uk-float-left"><?php echo $coin['balance'].' '.$coin['symbol']; ?></span>
                                    </div>
                                </div>
                           
                            </div>
                       
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

            <?php add_footer($root_path); ?>
        </div>
        <div class="uk-modal" id="modal_order" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div class="uk-modal-header">
                    <div>
                        <h2 class="uk-modal-title uk-margin-remove-bottom calculate-title">ثبت سفارش</h2>
                        <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                        </p>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="order-body-div">
                        <div>
                            <label class="uk-form-label order-lable">نوع سفارش</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <select id="order_type" class="uk-select order-select-input w10" name="order_type">
                                    <?php foreach($order_type_array as $row) { ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if($row['id'] === "2") echo 'selected'; ?>><?php echo $row['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="uk-form-label order-lable">نوع ارز</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <select id="coin_type" class="uk-select order-select-input w10" name="coin_type">
                                    <?php foreach($coin_type_array as $row) { ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name_fa']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="uk-form-label order-lable">مقدار</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <input id="order_amount" class="uk-input order-select-input" value="">
                            </div>
                            <span id="new_order_error" class="shabnam elzami"></span>
                        </div>
                    </div>
                    <br>

                    <div class="order-body-div">
                        <div id="w_addr_div" class="enabled_div">
                            <label class="uk-form-label order-lable">آدرس کیف پول شما:</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <input id="wallet_address" class="uk-input order-select-input w38" value="" dir="ltr">
                            </div>
                            <span id="wallet_address_error" class="shabnam elzami"></span>
                        </div>
                    </div>
                    <br>
                    <div class="order-body-div">
                        <div id="shaba_div" class="disabled_div">
                            <label class="uk-form-label order-lable">شماره شبا تایید شده</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <select id="bank_shaba" class="uk-select order-select-input w38" name="bank_shaba">
                                    <?php foreach($user_bank_info as $row) { ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['shaba']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <span id="bank_shaba_error" class="shabnam elzami"></span>
                        </div>
                    </div>



                </div>
                <div class="uk-modal-footer uk-text-left">
                    <button class="uk-button uk-button-default uk-modal-close cancel_btn" type="button">انصراف</button>
                    <button id="submit_order" class="uk-button uk-button-primary pay_btn" type="button"><i class="fa fa-spinner fa-spin spinner-onload"></i> ثبت</button>
                </div>
            </div>
        </div>

        <?php echo add_info_modal(); ?>
        <?php echo add_delete_modal(); ?>
        <?php echo add_pay_modal(); ?>


        <div id="modal_invoice" uk-modal>
            <div class="uk-modal-dialog w80">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div class="uk-card uk-card-default card-invoice">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="40" height="40" src="../asset/img/invoice.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">فاکتور خدمات</h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-flex uk-flex-row invoice-h1">
                            <div class="row-right uk-margin-xlarge-left">
                                <div>
                                    <span class="shabnam">شماره فاکتور :</span>
                                    <span id="inv_order_id" class="shabnam elzami"></span>
                                </div>
                            </div>
                            <div class="row-right uk-margin-xlarge-left">
                                <div>
                                    <span class="shabnam"> تاریخ ثبت سفارش :</span>
                                    <span id="inv_order_date" class="shabnam elzami uk-float-left"></span>
                                </div>
                            </div>
                            <div class="row-right uk-margin-xlarge-left">
                                <div>
                                    <span class="shabnam">ساعت ثبت سفارش :</span>
                                    <span id="inv_order_time" class="shabnam elzami"></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="uk-flex uk-flex-row invoice-h1">
                            <div class="row-right uk-margin-xlarge-left">
                                <div>
                                    <span class="shabnam">نوع سفارش :</span>
                                    <span id="inv_order_type" class="shabnam elzami"></span>
                                </div>
                            </div>


                            <div class="row-right uk-margin-xlarge-left">
                                <div>
                                    <span class="shabnam">نوع ارز دیجیتال :</span>
                                    <span id="inv_coin_type" class="shabnam elzami"></span>
                                </div>
                            </div>
                            <div class="row-right uk-margin-xlarge-left">
                                <div>
                                    <span class="shabnam">وضعیت سفارش :</span>
                                    <span id="inv_order_status" class="shabnam elzami"></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <br>
                        <div class="uk-flex uk-flex-row uk-flex-around invoice-h1">
                            <div class="uk-card uk-card-default uk-width-1-2@m card-invoice-2">
                                <div class="uk-card-body">
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">مقدار ارز دیجیتال :</span>
                                            <span id="inv_coin_amount" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">معادل دلاری :</span>
                                            <span id="inv_amount_usd" class="shabnam elzami"></span>&nbsp;&nbsp;<span class="shabnam elzami">دلار</span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">نرخ محاسبه شده دلار :</span>
                                            <span id="inv_usd_toman" class="shabnam elzami"></span>&nbsp;&nbsp;<span class="shabnam elzami">تومان</span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">کارمزد سامانه :</span>
                                            <span id="inv_fee_karmozd" class="shabnam elzami"></span>&nbsp;&nbsp;<span class="shabnam elzami">تومان</span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">کارمزد شبکه :</span>
                                            <span id="inv_fee_network" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">کارمزد درگاه شاپرک :</span>
                                            <span id="inv_fee_shaparak" class="shabnam elzami"></span>&nbsp;&nbsp;<span class="shabnam elzami">تومان</span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">مبلغ فاکتور :</span>
                                            <span id="inv_amount_toman" class="shabnam elzami"></span>&nbsp;&nbsp;<span class="shabnam elzami">تومان</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-card uk-card-default uk-width-1-2@m card-invoice-2">
                                <div class="uk-card-body">
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">آدرس کیف پول :</span>
                                            <span id="inv_wallet_addr" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">رسید شبکه :</span>
                                            <span id="inv_tx_id" class="shabnam elzami fz12"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">شماره کارت :</span>
                                            <span id="inv_card_no" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">شماره شبا : </span>
                                            <span id="inv_shaba" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">رسید بانک :</span>
                                            <span id="inv_bank_res" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">تاریخ پرداخت :</span>
                                            <span id="inv_pay_date" class="shabnam elzami uk-float-left"></span>
                                        </div>
                                    </div>
                                    <div class="row-right">
                                        <div>
                                            <span class="shabnam">ساعت پرداخت :</span>
                                            <span id="inv_pay_time" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


</body>

</html>
