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
if( isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;  
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به پرداخت وبمانی',$_SESSION['bitex_username']);
} else {
    header("location:".$root_path."login/");
    exit();
}

if(!isset($_SESSION['pay_info_order_id'])) {
    header("location:".$root_path."dashboard/");
    exit();    
}

$admin_setting = get_setting();
$webmoney_address = $admin_setting[0]['webmoney_address'];
$user_id = $_SESSION['user_id'];        
$order_id = $_SESSION['pay_info_order_id'];
$amount = $_SESSION['pay_info_amount'];

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
    <title>پرداخت وبمانی</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <script src="<?php echo $root_path; ?>asset/js/sell_webmoney.js?version=<?php echo $version; ?>"></script>
    <script type='text/javascript' src='https://merchant.wmtransfer.com/conf/lib/widgets/wmApp.js?v=1.1'></script>

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>

        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-2@m card-calc">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="40" height="40" src="<?php echo $root_path; ?>asset/img/wmz_icon.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">پرداخت دلار وبمانی</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <p class="shabnam">جهت پرداخت فاکتور فروش خود دکمه زیر را کلیک نمایید.با کلیک بر روی دکمه پرداخت وبمانی به سایت وبمانی منتقل می گردید.</p>
                        <div class="row">
                            <div id="wm_pay"> </div>

                        </div>
                    </div>
                    <div class="uk-card-footer">
                        <a href="https://passport.wmtransfer.com/asp/certView.asp?wmid=807524534649" target="_blank">مشاهده تاییدیه ما در وبمانی</a>

                    </div>
                </div>
            </div>
        </section>
        <?php add_footer($root_path);
              add_info_modal();
        ?>
    </div>
</body>

</html>
