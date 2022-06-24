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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به سوالات متداول',$_SESSION['bitex_username']);
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>سوالات متداول</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <script src="<?php echo $root_path; ?>asset/js/terms.js?version=<?php echo $version; ?>"></script>
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_home_navbar($root_path,$user_login); ?>
        <?php add_home_toolbar($root_path,$user_login); ?>

        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-@s home-content" uk-grid="">
                <div class="uk-flex uk-flex-column uk-flex-around terms">
                    <div class="uk-text-center terms-main-title">
                        <p>سوالات متداول</p>
                    </div>
                    <div class="uk-align-right terms-text">
                        <p>صرافی آسان تابع قوانین جمهوری اسلامی ایران بوده و بستری برای تبادل دارایی های دیجیتال مانند بیت کوین، وبمانی و تتر با ریال می باشد. هیچ گونه تبادل ارزی اعم از خرید و فروش دلار یا سایر ارزهای کاغذی، در این سامانه صورت نمی گیرد.</p>
                    </div>
                </div>
                <div>
                    <ul class="uk-list list">
                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>چه نوع ارزهای دیجیتالی در صرافی آسان قابل خرید یا فروش می باشد؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    در حال حاضر تنها ارزهای دیجیتال بیت کوین ، تتر و وبمانی در صرافی آسان ارائه می گردد.
                                </p>

                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>نحوه احراز هویت مشتریان چگونه است؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    تمامی مشتریان جهت خرید از ما در سامانه باید مراحل احراز هویت شامل ارسال مدارک شناسایی و سلفی با کارت شناسایی و تعهدنامه دستنویس، تائید حساب بانکی و شماره همراه را انجام دهند. احراز هویت برای هر مشتری تنها یک بار انجام می گردد.انجام احراز هویت برای خریداران اجباری می باشد.
                                    <br>
                                    جهت انجام سفارشات فروش به ما تنها ثبت و تایید حساب بانکی مشتری کفایت می نماید.
                                </p>
                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div> آیا برای فروش ارزدیجیتال به صرافی آسان احراز هویت لازم است؟ </div>
                            </div>
                            <div class="body">
                                <p>
                                    جهت فروش ارز دیجیتال به صرافی آسان توسط مشتری، احراز هویت الزامی نیست.مشتری باید اطلاعات حساب بانکی خود را در سامانه ثبت نماید.حساب بانکی باید متعلق به مشتری باشد.
                                </p>

                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>هزینه استفاده از خدمات صرافی آسان چه مقدار می باشد؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    در حال حاضر سامانه برای هیچ کدام از خدمات خود کارمزدی دریافت نمی نماید.درآمد سامانه از مابه التفاوت خرید و فروش ارز دیجیتال به و یا از مشتریان حاصل می گردد.بدیهی است کارمزدهای سرویس دهندگان ثالت مانند شبکه بلاکچین و شاپرک بر عهده مشتری می باشد.
                                </p>

                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>آیا برای حجم معاملات مشتریان در روز محدودیتی وجود دارد؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    خیر، در صورت وجود موجودی کافی در سامانه هیچ محدودیتی در حجم معاملات مشتریان وجود ندارد.
                                </p>

                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>چرا صرافی آسان نماد اعتماد ندارد؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    متاسفانه طبق اعلام مرکز توسعه تجارت الکترونیکی در حال حاضر و تا زمان تدوین قوانین حوزه ارزهای دیجیتال، به سایتهای فعال در زمینه مبادلات ارزهای دیجیتال، ای نماد تعلق نمی گیرد.
                                </p>
                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>پس از خرید بیت کوین یا وبمانی یا تتر، چه زمانی ارزدیجیتال به حساب من واریز می‌شود؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    در صورت تایید پرداخت ریالی مشتری در سامانه به صورت لحظه ای، انتقال ارزدیجیتال بلافاصله به صورت خودکار انجام می گیرد.انتقال وبمانی به صورت لحظه ای می باشد و سایر ارزهای دیجیتال تابع ترافیک شبکه انتقال می باشند.
                                </p>
                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>پس از فروش بیت کوین یا وبمانی یا تتر، چه زمانی وجه ریالی معادل آن به حساب من واریز می‌شود؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    در صورت تایید پرداخت ارزی مشتری در سامانه به صورت لحظه ای، انتقال ریال در ساعات کاری همان روز انجام می گیرد و مطابق با سیکل پرداخت شبکه شاپرک به حساب مشتری واریز می گردد.
                                </p>
                            </div>
                        </li>
                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>چه راهکاری برای جلوگیری از انجام خریدهای مشکوک به کلاهبرداری وجود دارد؟</div>
                            </div>
                            <div class="body">
                                <p>
                                    اگرچه مسئولیت سلامت معاملات طبق قوانین سامانه بر عهده مشتری می باشد، لیکن سامانه صرافی آسان به گونه ای طراحی گردیده است که با استفاده از الگوریتم های هوش مصنوعی و معاملاتی، تا حد بسیار زیادی قادر به تشخیص معاملات نا سالم می باشد. این گونه معاملات به صورت خودکار به واحد پشتیبانی گزارش داده میشود و پس از تایید توسط کارشناسان سامانه انجام می شوند. همچنین حساب کاربری مشتریانی که مرتکب تخلف گردند بسته خواهد شد.
                                </p>
                            </div>
                        </li>


                    </ul>
                </div>

            </div>
        </section>
        <?php add_footer($root_path); ?>
    </div>
</body>

</html>
