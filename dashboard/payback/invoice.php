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


?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php add_asset($root_path); ?>
    <title>صرافی آسان</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path); ?>
        <?php add_dashboard_toolbar($root_path); ?>

        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s dashboard-content" uk-grid="">

            <div class="uk-card uk-card-default card-invoice">
                <div class="uk-card-header">
                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                        <div class="uk-width-auto">
                            <img width="40" height="40" src="../../asset/img/invoice.png">
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
                    <div class="uk-flex uk-flex-row">
                        <div class="row-right uk-margin-xlarge-left">
                            <div>
                                <span class="shabnam">شماره فاکتور :</span>
                                <span id="inv_order_id" class="shabnam elzami"></span>
                            </div>
                        </div>
                        <div class="row-right uk-margin-xlarge-left">
                            <div>
                                <span class="shabnam"> تاریخ ثبت سفارش :</span>
                                <span id="inv_order_date" class="shabnam elzami"></span>
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
                    <div class="uk-flex uk-flex-row">
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
                    <div class="uk-flex uk-flex-row uk-flex-around">
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
                                        <span id="inv_amount_usd" class="shabnam elzami"></span>
                                    </div>
                                </div>
                                <div class="row-right">
                                    <div>
                                        <span class="shabnam">نرخ محاسبه شده دلار :</span>
                                        <span id="inv_usd_toman" class="shabnam elzami"></span>
                                    </div>
                                </div>
                                <div class="row-right">
                                    <div>
                                        <span class="shabnam">کارمزد سامانه :</span>
                                        <span id="inv_fee_karmozd" class="shabnam elzami"></span>
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
                                        <span id="inv_fee_shaparak" class="shabnam elzami"></span>
                                    </div>
                                </div>
                                <div class="row-right">
                                    <div>
                                        <span class="shabnam">مبلغ نهایی فاکتور :</span>
                                        <span id="inv_amount_toman" class="shabnam elzami"></span>
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
                                        <span id="inv_tx_id" class="shabnam elzami"></span>
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
                                        <span class="shabnam">شماره شبا :</span>
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
                                        <span id="inv_pay_date" class="shabnam elzami"></span>
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
