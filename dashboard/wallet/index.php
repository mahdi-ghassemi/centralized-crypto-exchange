<?php
if (!session_id()) {    
    session_start();
}

$root_path = "../../";



include_once($root_path.'include/global.php');
include_once($root_path.'include/jdf.php');
include_once($root_path.'include/bitex.php');

session_secure();
$user_login = false;
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;    
}

if(!$user_login) {
    header("Location: ".$root_path.'login/');
    exit();
}

if(!isset($_SESSION['access_level_id'])){
    header("Location: ".$root_path.'login/');
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
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];

$user_wallets = get_user_wallet_info($_SESSION['user_id']);
$coin_type_array = get_wallet_coins_table();


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

$usd_price_info = array();
$usd_price_info = get_usd_price();

$buy_from_us = $usd_price_info[0]['buy_from_us'];
$sell_to_us = $usd_price_info[0]['sell_to_us'];



?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>کیف پول</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/datatables.min.css">

    <script src="<?php echo $root_path; ?>asset/js/wallet.js?version=<?php echo $version; ?>"></script>

    <script src="<?php echo $root_path; ?>asset/js/datatables.min.js"></script>

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-2-@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-1@m card-wallet">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/wallet.png">
                            </div>
                            <div class="uk-width-expand display-none">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">کیف پول</h3>
                            </div>
                            <div>
                                <button id="new_wallet" class="uk-button uk-button-default new_order_btn">
                                    ایجاد کیف پول
                                </button>
                            </div>
                        </div>


                    </div>
                    <div class="uk-card-body">
                        <table id="orders" class="uk-table uk-table-hover uk-table-striped uk-table-divider">
                            <thead>
                                <tr>
                                    <th class="uk-table-shrink"></th>
                                    <th class="expand">عنوان</th>
                                    <th class="expand">نوع</th>
                                    <th class="expand">موجودی</th>
                                    <th class="expand">ارزش به دلار</th>
                                    <th class="expand">ارزش به تومان</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($user_wallets as $row) { ?>
                                <?php 
    $balance = balance($row['id']);
    $usd_value = $toman_value = 0;
    if($row['wallet_coin_id'] === "1") {
        $usd_value = $btc * $balance;
        $toman_value = $sell_to_us * $usd_value;
    }
    if($row['wallet_coin_id'] === "2") {
        $usd_value = $usdt * $balance;
        $toman_value = $sell_to_us * $usd_value;
    }
    if($row['wallet_coin_id'] === "3") {
        $usd_value = $balance / $sell_to_us;
        $toman_value = $balance;
    }
                                
                                ?>

                                <tr class="font-yekan">
                                    <td><img class="uk-preserve-width uk-border-circle" src="<?php echo $root_path.'asset/img/'.$row['image']; ?>" width="40" alt=""></td>
                                    <td><span><?php echo $row['alias']; ?></span></td>
                                    <td><span><?php echo $row['name']; ?></span></td>
                                    <td><span><?php echo $balance; ?></span></td>
                                    <td><span><?php echo number_format($usd_value,2,'.',','); ?></span></td>
                                    <td><span><?php echo number_format($toman_value,0,'',','); ?></span></td>
                                    <td><a href="<?php echo $root_path; ?>dashboard/transactions/index.php?id=<?php echo $row['address']; ?>" class="a1" target="_blank">تراکنش ها</a></td>
                                    <td><a href="<?php echo $root_path; ?>dashboard/deposit/index.php?id=<?php echo $row['address']; ?>" class="a1" target="_blank">واریز</a></td>
                                    <td><a href="<?php echo $root_path; ?>dashboard/wirhdraw/index.php?id=<?php echo $row['address']; ?>" class="a1" target="_blank">برداشت</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <?php add_footer($root_path); ?>
        <?php add_info_modal(); ?>
    </div>
    <div class="uk-modal" id="modal_wallet" uk-modal>
        <div class="uk-modal-dialog w30">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-header">
                <div>
                    <h2 class="uk-modal-title uk-margin-remove-bottom calculate-title">ایجاد کیف پول جدید</h2>
                </div>
            </div>
            <div class="uk-modal-body">
                <div class="uk-flex uk-flex-column uk-flex-around">
                    <div>
                        <label class="uk-form-label order-lable">عنوان کیف پول</label>
                        <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                            <input id="alias" class="uk-input order-select-input w100" value="" placeholder="مثال: کیف پول اصلی">
                        </div>
                        <span id="alias_err" class="shabnam elzami"></span>
                    </div>
                    <div>
                        <label class="uk-form-label order-lable">نوع ارز</label>
                        <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                            <select id="coin_type" class="uk-select order-select-input w100" name="coin_type">
                                <?php foreach($coin_type_array as $row) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name_fa']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-center">
                <button class="uk-button uk-button-default uk-modal-close cancel_btn" type="button">انصراف</button>
                <button id="submit_wallet" class="uk-button uk-button-primary pay_btn" type="button"><i class="fa fa-spinner fa-spin spinner-onload"></i> تایید</button>
            </div>
        </div>
    </div>
</body>

</html>
