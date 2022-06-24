<?php
if (!session_id()) {    
    session_start();
}

$root_path = "../../";
include_once($root_path.'include/global.php');
include_once($root_path.'include/jdf.php');
include_once($root_path.'include/bitex.php');
include_once '../../qr-code/autoload.php';
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

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
$wallet_info = array();
$deposit_all = array();

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $wallet_address = validate_input($_GET['id']);
    $wallet_info = get_wallet_by_address_user_id($wallet_address,$_SESSION['user_id']);
    if(count($wallet_info)){
        $wallet_id = $wallet_info[0]['id'];
        //$deposit = get_wallet_deposit($wallet_id); 
        $deposit_all = get_all_deposit_by_userid($_SESSION['user_id']); 
        $balance = balance($wallet_id);
        $withdraw = 0;
        $network_protocol = "";
        $qrCode = new QrCode($wallet_info[0]['address']);
        $qrCode->setSize(150);
        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        $qrCode->writeFile(__DIR__.'/'.$wallet_info[0]['address'].'.png'); 
        
        
        if($wallet_info[0]['wallet_coin_id'] === "1") {
            if($balance > 0.0004) 
                $withdraw = $balance - 0.0004; 
            $network_protocol = "BTC";
        }
        if($wallet_info[0]['wallet_coin_id'] === "2") {
            if($balance > 1) 
                $withdraw = $balance - 1; 
            $network_protocol = "OMNI";
        }
        if($wallet_info[0]['wallet_coin_id'] === "3") {
            if($balance > 100000) 
                $withdraw = $balance - 100000;            
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>واریز</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/datatables.min.css">

    <script src="<?php echo $root_path; ?>asset/js/deposit.js?version=<?php echo $version; ?>"></script>
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
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/deposit.png">
                            </div>

                            <div class="uk-width-expand">
                                <?php if(count($wallet_info)) { ?>
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">واریز به <?php echo $wallet_info[0]['alias']; ?></h3>
                                <?php } else { ?>
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">واریز</h3>
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
                                <br><br>
                                <?php if($wallet_info[0]['symbol'] !== "IRR") { ?>
                                <div class="uk-text-center uk-padding">
                                    <button id="order_now" class="uk-button uk-button-small new_order_btn w15">این ارز را هم اکنون از ما بخرید</button>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="deposit-body-div2">
                                <div class="uk-text-center">
                                    <span class="shabnam elzami">پروتکل شبکه :</span>&nbsp;&nbsp;<span class="shabnam"><?php echo $network_protocol; ?></span>
                                </div>
                                <br>
                                <div class="uk-text-center">
                                    <span class="shabnam elzami">آدرس </span>&nbsp;<span class="shabnam elzami"><?php echo $wallet_info[0]['alias']; ?></span>
                                    <span id="address" class="shabnam"><?php echo $wallet_info[0]['address']; ?></span>
                                    <br><br>
                                    <button id="copy_addr_btn" class="uk-button uk-button-small pay_btn">کپی آدرس</button>
                                </div>
                                <div class="uk-text-center">
                                    <img src="<?php echo $wallet_info[0]['address'].'.png'; ?>">
                                </div>


                            </div>
                            <div class="deposit-body-div2">
                                <div class="uk-alert-primary alert-deposit" uk-alert="">مبالغ واریزی بعد از حداقل 2 تایید از شبکه بلاکچین به حساب کیف پول شما واریز می گردد.
                                </div>
                                <div class="uk-alert-primary alert-deposit" uk-alert="">در ارائه آدرس کیف پول خود به فرستنده وجه دقت نمایید.این سامانه در قبال واریز به آدرسی غیر از کیف پول شما، مسئولیتی ندارد.
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
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/deposit.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تاریخچه واریزی ها </h3>
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
                                        <th>ردیف</th>
                                        <th>ارز</th>
                                        <th>تاریخ</th>
                                        <th>ساعت</th>
                                        <th>مبلغ</th>
                                        <th>کد تراکنش</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $radif = 0; foreach($deposit_all as $row) { $radif++; ?>
                                    <tr>
                                        <td><?php echo $radif; ?></td>
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

        <?php add_footer($root_path); ?>
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
                        <div class="w7">
                            <label class="uk-form-label order-lable">آدرس کیف پول شما:</label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto p6" uk-grid="">
                                <input id="wallet_address" class="uk-input order-select-input w28" dir="ltr" value="<?php echo $wallet_info[0]['address']; ?>" readonly>
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
