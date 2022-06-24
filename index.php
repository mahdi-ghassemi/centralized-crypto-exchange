<?php
if (!session_id()) {
    ini_set( 'session.cookie_httponly', 1 );
    ini_set( 'session.cookie_secure', 1 );
    session_start();
}

$root_path = "./";



include_once('include/global.php');
include_once('include/jdf.php');
include_once('include/bitex.php');

session_secure();
get_visitor_info($ipaddress,$page,$referrer,$useragent);
$user_login = false;
$user_bank_info = array();
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;
    $user_bank_info = get_user_bank_confirmed_info_by_id($_SESSION['user_id']);
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به صفحه اصلی',null);
}


$coins = array();
$coins = get_coins_table();

$users_count = get_users_count() + 130;
$order_sell_count = get_order_count_by_type("1") + 420;
$order_buy_count = get_order_count_by_type("2") + 320;

$sum_amount_usd = get_order_sum() + 157620;

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

$order_type_array = array();
$order_type_array = get_order_type();

$coin_type_array = array();
$coin_type_array = get_coin_type();

?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>خدمات ارز دیجیتال آسان</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/custom-icon.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <script src="asset/js/home.js?version=<?php echo $version; ?>"></script>
    <script src="asset/js/jquery.countTo.js"></script>
</head>

<body>
    <div id="main-content" class="uk-container">
        <?php add_home_navbar($root_path,$user_login); ?>
        <?php add_home_toolbar($root_path,$user_login); ?>
        
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-2@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-2@m card-calc">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="40" height="40" src="./asset/img/calculator.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">محاسبه گر ارز الکترونیک</h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <p class="shabnam">مقدار یا مبلغ به تومان و نوع ارز را مشخص نمایید تا قیمت خرید و فروش آن نمایش داده شود.</p>
                        <div class="row">
                            <div class="amount-div">
                                <input class="uk-input" type="text" id="amount" value="" placeholder="مقدار ارز دیجیتال" onkeypress="return isNumber(event)">
                            </div>
                            <div class="coin-select-div">
                                <select class="uk-select" id="cur_name">
                                    <option value="1">BTC - بیت کوین</option>
                                    <option value="2">WMZ - وب مانی</option>
                                    <option value="3">USDT - تتر</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="amount-div">
                                <input class="uk-input" type="text" id="amount_buy_t" value="" placeholder="مبلغ خرید از ما به تومان" onkeypress="return isNumber(event)">
                            </div>
                            <div class="amount-div">
                                <input class="uk-input" type="text" id="amount_sell_t" value="" placeholder="مبلغ فروش به ما به تومان" onkeypress="return isNumber(event)">
                            </div>

                        </div>
                    </div>
                    <div class="uk-card-footer">
                        <button id="buy_btn" class="uk-button uk-button-default pay_btn buy-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> خرید از ما</button>
                        <button id="sell_btn" class="uk-button uk-button-default cancel_btn sell-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> فروش به ما </button>
                    </div>
                </div>
                <div class="uk-card uk-card-default uk-width-1-2@m card-price h1">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="40" height="40" src="./asset/img/bar_chart.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom price-title">قیمت لحظه ای</h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-gregorian"><?php echo jdate("l").', '.get_persian_numbers(date("d")).' '. miladi_month_to_farsi().' '. get_persian_numbers(date("Y")); ?></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body t1">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                            <table id="price" class="uk-table uk-table-hover uk-table-divider">
                                <thead>
                                    <tr>
                                        <th class="width-3"></th>
                                        <th class="width-1"></th>
                                        <th>قیمت به دلار</th>
                                        <th>خرید از ما (تومان)</th>
                                        <th>فروش به ما (تومان)</th>
                                        <th class="width-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($coins as $coin) { ?>
                                    <tr>
                                        <td>
                                            <img src="./asset/img/<?php echo $coin['image'];?>" width="25" height="25">
                                        </td>
                                        <td>
                                            <span uk-tooltip="title:<?php echo $coin['symbol'];?>"><?php echo $coin['name_fa']; ?></span>
                                        </td>
                                        <td><span id="<?php echo $coin['symbol']; ?>">
                                                <?php 
                                            if($coin['symbol'] === "BTC")
                                                echo get_persian_numbers(number_format($btc, 2 , '/',','));
                                            if($coin['symbol'] === "WMZ")
                                                echo get_persian_numbers($wmz);
                                            if($coin['symbol'] === "USDT")
                                                echo get_persian_numbers($usdt);
                                            ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span id="bu_<?php echo $coin['symbol']; ?>">
                                                <?php 
                                            if($coin['symbol'] === "BTC")
                                                echo get_persian_numbers(number_format(($btc * $buy_from_us) + ((($btc * $buy_from_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "WMZ")
                                                echo get_persian_numbers(number_format(($wmz * $buy_from_us) + ((($wmz * $buy_from_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "USDT")
                                                echo get_persian_numbers(number_format(($usdt * $buy_from_us) + ((($usdt * $buy_from_us) * $bitex_fee) / 100 )), 2 , '/',',');                        
                                            ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span id="se_<?php echo $coin['symbol']; ?>">
                                                <?php 
                                            if($coin['symbol'] === "BTC")
                                                echo get_persian_numbers(number_format(($btc * $sell_to_us) - ((($btc * $sell_to_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "WMZ")
                                                echo get_persian_numbers(number_format(($wmz * $sell_to_us) - ((($wmz * $sell_to_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            if($coin['symbol'] === "USDT")
                                                echo get_persian_numbers(number_format(($usdt * $sell_to_us) - ((($usdt * $sell_to_us) * $bitex_fee) / 100 )), 2 , '/',',');
                                            ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button id="co_<?php echo $coin['id']; ?>" class="uk-button uk-button-default uk-button-small new_order_btn order-btn quick-new-order-btn width-2"><i class="fa fa-spinner fa-spin spinner-onload"></i> ثبت سفارش</button>

                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <hr>
        <section>
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                <div class="uk-card uk-card-default uk-card-body uk-width-1-1@m counter">
                    <h2 class="uk-card-title uk-text-center">امکانات</h2>
                    <div class="uk-form-stacked uk-grid-small uk-flex-around uk-child-width-1-1 uk-child-width-1-2@s" uk-grid="">
                        <div class="uk-card uk-card-default card-features">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <img width="64" height="64" src="./asset/img/no_fee.png">
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom price-title">بدون کارمزد</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-card-body uk-text-justify">
                                <p class="shabnam">ما هیچ کارمزدی بابت نقل و انتقال دریافت نمی کنیم.سود ما در خرید و فروش رمزارز می باشد.</p>
                            </div>
                        </div>

                        <div class="uk-card uk-card-default card-features">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <img width="64" height="64" src="./asset/img/safe1.png">
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom price-title">ایمنی و امنیت</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-card-body uk-text-justify">
                                <p class="shabnam">کلیه نقل و انتقالات با بالاترین پروتکل های امنیتی محافظت گردیده و وجوه در سریعترین زمان ممکن به حساب مشتریان واریز میگردد.</p>
                            </div>
                        </div>

                        <div class="uk-card uk-card-default card-features">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <img width="64" height="64" src="./asset/img/support.png">
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom price-title">پشتیبانی</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-card-body uk-text-justify">
                                <p class="shabnam">پشتیبانی 24 ساعت در 7 روز هفته.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <hr>
        <section>
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s counter" uk-grid="">
                <h2 class="uk-card-title uk-text-center">وضعیت سیستم</h2>
            </div>
            <div class="uk-form-stacked uk-grid-small uk-flex-around uk-child-width-1-1 uk-child-width-1-4@s" uk-grid="">
                <div class="counter">
                    <div>
                        <p class="shabnam">کاربران</p>
                    </div>
                    <div>
                        <p><b class="timer" id="users" data-to="<?php echo $users_count; ?>" data-speed="10000"></b><img src="asset/img/users.png" width="24" alt="users"></p>
                    </div>
                </div>
                <div class="counter">
                    <div>
                        <p class="shabnam">معاملات خرید</p>
                    </div>
                    <div>
                        <p><b class="timer" id="users" data-to="<?php echo $order_buy_count; ?>" data-speed="10000"></b><img src="asset/img/shopping_cart.png" width="24" alt="shopping cart"></p>
                    </div>
                </div>
                <div class="counter">
                    <div>
                        <p class="shabnam">معاملات فروش</p>
                    </div>
                    <div>
                        <p><b class="timer" id="users" data-to="<?php echo $order_sell_count; ?>" data-speed="10000"></b><img src="asset/img/coins.png" width="24" alt="coins"></p>
                    </div>
                </div>
                <div class="counter">
                    <div>
                        <p class="shabnam">حجم معاملات</p>
                    </div>
                    <div>
                        <p><b class="timer" id="users" data-to="<?php echo $sum_amount_usd; ?>" data-speed="10000"></b><img src="asset/img/dollar-sign1.png" width="24" alt="dollar"></p>
                    </div>
                </div>
            </div>
        </section>
        <?php add_footer($root_path); ?>
        <?php add_info_modal(); ?>
        <div id="modal_order" uk-modal>
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
                                    <option value="<?php echo $row['id']; ?>">IR-<?php echo $row['shaba']; ?></option>
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
    </div>
</body>

</html>
