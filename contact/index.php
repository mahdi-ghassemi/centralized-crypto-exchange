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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به تماس با ما',$_SESSION['bitex_username']);
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>تماس با ما</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_home_navbar($root_path,$user_login); ?>
        <?php add_home_toolbar($root_path,$user_login); ?>

        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-2-@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/contact.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">اطلاعات تماس</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <span class="uk-icon-navy" uk-icon="location"></span>
                        <span class="shabnam elzami">آدرس</span>
                        <br>
                        <div class="uk-text-center">
                            <span class="shabnam"></span>
                        </div>
                        <br><br>
                        <span class="uk-icon-navy" uk-icon="receiver"></span>
                        <span class="shabnam elzami">شماره تماس </span>
                        <br>
                        <div class="uk-text-center">
                            <span class="shabnam"></span>
                        </div>
                        <br><br>
                        <span class="uk-icon-navy" uk-icon="mail"></span>
                        <span class="shabnam elzami">ایمیل پشتیبانی</span>
                        <br>
                        <div class="uk-text-center">
                            <span class="shabnam"></span>
                        </div>
                        <br><br>
                        <span class="uk-icon-navy" uk-icon="social"></span>
                        <span class="shabnam elzami">ارتباط در پیام رسان ها</span>
                        <br>
                        <div class="uk-text-center">
                            <a href="" target="_blank"><img src="<?php echo $root_path; ?>asset/img/telegram.png" width="32" height="32"></a>
                            <a href="" target="_blank"><img src="<?php echo $root_path; ?>asset/img/whatsapp.png" width="32" height="32"></a>
                        </div>


                    </div>
                </div>

            </div>
        </section>
        <?php add_footer($root_path); ?>
    </div>
</body>

</html>
