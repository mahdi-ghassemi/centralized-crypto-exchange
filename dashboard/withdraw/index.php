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


$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];
$user_bank_info = get_user_bank_confirmed_info_by_id($_SESSION['user_id']);

$wallet_info = array();
$withdraw_all = array();

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $wallet_address = validate_input($_GET['id']);
    $wallet_info = get_wallet_by_address_user_id($wallet_address,$_SESSION['user_id']);
    if(count($wallet_info)){
        $wallet_id = $wallet_info[0]['id'];
        //$deposit = get_wallet_deposit($wallet_id); 
        $withdraw_all = get_all_withdraw_by_userid($_SESSION['user_id']); 
        $balance = balance($wallet_id);
        $withdraw = 0;
        $network_protocol = "";
        $network_fee = 0;
        $minimum_w = 0;
        
        
        
        if($wallet_info[0]['wallet_coin_id'] === "1") {
            if($balance > 0.0004) 
                $withdraw = $balance - 0.0004; 
            $network_protocol = "BTC";
            $network_fee = 0.0004;
            $minimum_w = 0.001;
        }
        if($wallet_info[0]['wallet_coin_id'] === "2") {
            if($balance > 1) 
                $withdraw = $balance - 1; 
            $network_protocol = "OMNI";
            $network_fee = 1;
            $minimum_w = 2;
        }
        if($wallet_info[0]['wallet_coin_id'] === "3") {
            if($balance > 100000) 
                $withdraw = $balance - 100000; 
            $minimum_w = 100000;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>برداشت</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/datatables.min.css">
    <script type="text/javascript">
        var net_fee = "<?= $network_fee ?>";

    </script>
    <script type="text/javascript">
        var u_w_a = "<?= $wallet_address ?>";

    </script>
    <script src="<?php echo $root_path; ?>asset/js/withdraw.js?version=<?php echo $version; ?>"></script>
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
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/withdrawal.png">
                            </div>

                            <div class="uk-width-expand">
                                <?php if(count($wallet_info)) { ?>
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">برداشت از <?php echo $wallet_info[0]['alias']; ?></h3>
                                <?php } else { ?>
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">برداشت</h3>
                                <?php } ?>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                        </div>


                    </div>
                    <div class="uk-card-body no-padding">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-3@s deposit-body" uk-grid="">

                            <?php if(count($wallet_info)) { ?>
                            <div class="deposit-body-div1">
                                <div class="deposit-body-div3">
                                    <div>
                                        <span class="shabnam elzami">نوع ارز :</span>
                                        <br>
                                        <span class="shabnam elzami">نشانه :</span>
                                        <br>
                                        <span class="shabnam elzami">موجودی کل :</span>
                                        <br>
                                        <span class="shabnam elzami">موجودی قابل برداشت :</span>
                                    </div>
                                    &nbsp;&nbsp;
                                    <div>
                                        <span class="shabnam"><?php echo $wallet_info[0]['name_fa']; ?></span>
                                        <br>
                                        <span class="shabnam"><?php echo $wallet_info[0]['symbol']; ?></span>
                                        <br>
                                        <span class="shabnam direction-ltr uk-float-left"><?php echo number_format($balance,9,'.',',').' '.$wallet_info[0]['symbol']; ?></span>
                                        <br>
                                        <span class="shabnam direction-ltr uk-float-left"><?php echo number_format($withdraw,9,'.',',').' '.$wallet_info[0]['symbol']; ?></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="deposit-body-div3">
                                    <div>
                                        <span class="shabnam elzami">مقدار درخواستی :</span>
                                        <br>
                                        <span class="shabnam elzami">کارمزد شبکه :</span>
                                        <br>
                                        <span class="shabnam elzami">مقدار دریافتی :</span>
                                    </div>
                                    &nbsp;&nbsp;
                                    <div>
                                        <span id="req_amount" class="shabnam direction-ltr uk-float-left uk-margin-right">0.000000000</span><span class="shabnam"> <?php echo $wallet_info[0]['symbol']; ?></span>
                                        <br>
                                        <span class="shabnam direction-ltr uk-float-left uk-margin-right"><?php echo number_format($network_fee,9,'.',','); ?></span><span class="shabnam"><?php echo $wallet_info[0]['symbol']; ?></span>
                                        <br>
                                        <span id="take_amount" class="shabnam direction-ltr uk-float-left uk-margin-right">0.000000000</span><span class="shabnam"><?php echo $wallet_info[0]['symbol']; ?></span>
                                        <br>
                                    </div>
                                </div>
                                <hr>
                                <br>
                                <?php if($wallet_info[0]['symbol'] !== "IRR") { ?>
                                <?php if($withdraw > 0 ) {  ?>
                                <div class="uk-text-center uk-padding">
                                    <button id="order_now" class="uk-button uk-button-small new_order_btn w19"><i class="fa fa-spinner fa-spin spinner-onload"></i> این ارز را هم اکنون به ما بفروشید</button>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="deposit-body-div2">
                                <div class="uk-text-center">
                                    <span class="shabnam elzami">پروتکل شبکه :</span>&nbsp;&nbsp;<span class="shabnam"><?php echo $network_protocol; ?></span>
                                </div>
                                <br>
                                <div>
                                    <label class="uk-form-label shabnam uk-icon-navy">آدرس گیرنده &nbsp;<span class="shabnam uk-icon-navy"><?php echo $wallet_info[0]['symbol']; ?> :</span></label>
                                    <div class="uk-form-controls">
                                        <input id="address" class="uk-input" type="text" value="" placeholder="آدرس کیف پول گیرنده">
                                    </div>
                                    <span id="address_err" class="shabnam elzami"></span>
                                </div>
                                <br>
                                <div>
                                    <label class="uk-form-label shabnam uk-icon-navy">مقدار:</label>
                                    <div class="uk-form-controls">
                                        <input id="amount" class="uk-input" type="text" value="" placeholder="مقدار ارز دیجیتال برداشتی">
                                    </div>
                                    <span id="amount_err" class="shabnam elzami"></span>
                                </div>
                                <div class="uk-flex">
                                    <span class="shabnam uk-icon-navy">حداقل مقدار قابل برداشت :</span><span class="shabnam"><?php echo $wallet_info[0]['symbol']; ?></span><span class="shabnam direction-ltr uk-float-left uk-margin-right"><?php echo number_format($minimum_w,9,'.',','); ?></span>
                                </div>
                                <br>
                                <hr>
                                <br>
                                <div class="uk-text-center uk-padding">
                                    <button id="submit_w" class="uk-button uk-button-small pay_btn w15"><i class="fa fa-spinner fa-spin spinner-onload"></i> تایید درخواست برداشت</button>
                                </div>
                            </div>
                            <div class="deposit-body-div2">
                                <div class="uk-alert-primary alert-deposit" uk-alert="">برای تایید برداشت، باید آدرس ایمیل شما تایید شده و ورود دو مرحله ای برای حساب شما فعال باشد
                                </div>
                                <div class="uk-alert-primary alert-deposit" uk-alert="">لطفاً به کامزد شبکه و مقدار دریافتی دقت نمایید.مقداری که به دست گیرنده میرسد برابر با مقدار دریافتی خواد بود.
                                </div>
                                <div class="uk-alert-primary alert-deposit" uk-alert="">در وارد نمودن آدرس کیف پول گیرنده وجه دقت نمایید.این سامانه در قبال واریز به آدرس اشتباه، مسئولیتی ندارد.
                                </div>
                            </div>



                            <?php } else { ?>
                            <p class="shabnam">متاسفانه سابقه کیف پول موجود نمی باشد.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="history">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-2-@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-1@m card-wallet">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/withdrawal.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تاریخچه برداشت ها </h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s deposit-body" uk-grid="">
                            <table id="orders" class="uk-table uk-table-hover uk-table-striped uk-table-divider">
                                <thead>
                                    <tr>
                                        <th>وضعیت</th>
                                        <th>ارز</th>
                                        <th>تاریخ</th>
                                        <th>ساعت</th>
                                        <th>مبلغ</th>
                                        <th>کد تراکنش</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($withdraw_all as $row) {  ?>
                                    <?php $status_msg = "";
                                    if($row['status'] === "0") 
                                        $status_msg = "در انتظار تایید کاربر";
                                    if($row['status'] === "1") 
                                        $status_msg = "در حال انجام";  
                                    if($row['status'] === "2") 
                                        $status_msg = "پایان یافته";
                                    if($row['status'] === "3") 
                                        $status_msg = "مرجوع شده";                                     
                                    ?>
                                    <tr>
                                        <td><?php if($row['status'] < 2 ) echo '<i class="fa fa-spinner fa-spin"></i> '; ?> <?php echo $status_msg; ?></td>
                                        <td><?php echo $row['symbol']; ?></td>
                                        <td><?php echo $row['tr_date']; ?></td>
                                        <td><?php echo $row['tr_time']; ?></td>
                                        <td><?php echo $row['amount'].$sign; ?></td>
                                        <?php if($row['symbol'] === 'BTC' || $row['symbol'] === 'USDT') { ?>
                                        <td><a href="https://www.blockchain.com/btc/tx/<?php echo $row['serial']; ?>" target="_blank"><?php echo $row['serial']; ?></a>
                                        </td>
                                        <?php } else { ?>
                                        <td><span><?php echo $row['serial']; ?></span>
                                            <?php } ?>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php add_footer($root_path); 
              add_sms_verify_modal($root_path);
              add_ga_check_modal($root_path);        
        ?>
        <?php add_info_modal(); ?>
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
                    <div class="deposit-body-div3 flex-col">
                        <div class="uk-margin-left w7">
                            <label class="uk-form-label order-lable">مقدار:</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <input id="order_amount" class="uk-input order-select-input" value="">
                            </div>
                            
                        </div>
                        <div>
                            <label class="uk-form-label order-lable">شماره شبا تایید شده</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <select id="bank_shaba" class="uk-select order-select-input w28" name="bank_shaba">
                                    <?php foreach($user_bank_info as $row) { ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['shaba']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>
                    </div>
                    <span id="new_order_error" class="shabnam elzami"></span>

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
