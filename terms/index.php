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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به قوانین',$_SESSION['bitex_username']);
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>قوانین</title>
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
                        <p>قوانین و شرایط استفاده از خدمات</p>
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
                                <div>تعاریف</div>
                            </div>
                            <div class="body">
                                <p>
                                    سامانه: سیستم خودکار خرید و فروش ارز دیجیتال بر روی بستر وب که توسط صرافی آسان به آدرس https://www.esaraafi.ir ارائه شده است.
                                    <br>
                                    مشتری: یک شخص حقیقی یا حقوقی که با قبول قوانین سامانه در هنگام ثبت نام اقدام به خرید و فروش ارز دیجیتال در سامانه می نماید.
                                    <br>
                                    ارز دیجیتال: نوعی از ارز که تنها در داد و ستدهای بازارهای مجازی استفاده شده و تعریف فیزیکی ندارد.
                                    <br>
                                    فرم: به کلیه صفحات وب در این سامانه که مشتری در آنها اقدام به ورود اطلاعات می نماید و یا خروجی اطلاعات از سامانه را دریافت می نماید اطلاق می گردد.
                                </p>

                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>تعهدات و حقوق مشتری</div>
                            </div>
                            <div class="body">
                                <p>
                                    مشتری موظف به مطالعه و پذیرش کامل مفاد قوانین سامانه می باشد.ثبت نام در سامانه به معنای پذیرش قوانین از طرف مشتری می باشد.
                                    <br>
                                    مشتری متعهد میگردد که تمامی فعالیتهایش در سامانه در چارچوب قوانین جمهوری اسلامی بوده و هیچ گونه فعالیتی خارج از این چارچوب انجام نخواهد داد.
                                    <br>
                                    احراز هویت برای استفاده از خدمات سامانه ضروری می باشد و مشتری موظف است اطلاعات صحیح خود را در اختیار سامانه قرار دهد. بدیهی است در صورت وجود هرگونه تخلف در احراز هویت، مسئولیت به عهده‌ی مشتری بوده و سامانه حق توقف ارائه خدمات به مشتری و ارجاع موارد تخلف به مراجع ذی صلاح را خواهد داشت.
                                    <br>
                                    مشتری متعهد می گردد که از خدمات سامانه تنها برای خود استفاده نموده و مسئولیت استفاده از خدمات سامانه برای شخص دیگر که فرآیند احراز هویت را طی نکرده باشد به عهده مشتری خواهد بود.
                                    <br>
                                    مسئولیت حفظ و نگهداری ایمن گذرواژه مشتری بر عهده وی بوده و سامانه مسئولیتی در خصوص گذرواژه های آسان و ناایمن ندارد.سامانه به شدت توصیه می نماید که مشتری از گذرواژه های قوی و ورود دو مرحله ای استفاده نماید.
                                    <br>
                                    مشتری می‌پذیرد که به جز در موارد مورد تعهد سامانه حق هیچ گونه داعیه، طلب و شکایت از سامانه، مدیران، کارمندان و افراد مرتبط با این سامانه را نخواهد داشت.
                                    <br>
                                    اگر سامانه تحت هر عنوان اشتباهاً یا من غیر حق، وجوه یا ارز دیجیتال را به حساب مشتری منظور یا در محاسبات خود هر نوع اشتباهی نماید، هر زمان مجاز و مختار است راساً و مستقلاً و بدون انجام هیچ گونه تشریفات اداری و قضائی و دریافت اجازه کتبی از مشتری در رفع اشتباه و برداشت از حساب‌های وی اقدام نماید و تشخیص سامانه نسبت به وقوع اشتباه یا پرداخت بدون حق و لزوم برداشت از حساب معتبر خواهد بود و مشتری حق هرگونه اعتراض و ادعایی را در خصوص نحوه عملکرد سامانه از هر جهت از خود ساقط می نماید.
                                    <br>
                                    مسئولیت ارائه‌ی آدرس صحیح کیف پول به عهده‌ی مشتری می باشد. در صورت بروز هر گونه مشکل اعم از اشتباه در ورود آدرس صحیح، نقص آدرس، مشکلات کیف پول مقصد و بلوکه شدن دارایی‌های مشتری در کیف پول مقصد، هیچ گونه مسئولیتی به عهده ی سامانه نخواهد بود.
                                </p>
                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>تعهدات و حقوق سامانه</div>
                            </div>
                            <div class="body">
                                <p>
                                    سامانه این تعهد را به مشتری می دهد که دارایی های مشتری نزد خود را به امانت و با بالاترین پروتکل های امنیتی ممکن، حفظ نماید. در صورت ایجاد خسارت به دارایی های مشتری که نقص امنیتی سامانه موجب آن باشد، سامانه متعهد به جبران خسارت مشتری می باشد.
                                    <br>
                                    سامانه متعهد می گردد واریزهای درخواستی مشتری را در اسرع وقت با توجه به قوانین انتقال وجوه بین بانکی به انجام برساند. برای اطلاعات بیشتر، به قوانین انتقال وجه بین بانکی ( پایا ، ساتنا ) مراجعه فرمایید.
                                    <br>
                                    سامانه می تواند در هر زمان شرایط و قوانین عملکرد خویش را به روز رسانی نماید.
                                </p>

                            </div>
                        </li>

                        <li>
                            <div class="header">
                                <span uk-icon="plus"></span>
                                <div>کارمزد</div>
                            </div>
                            <div class="body">
                                <p>
                                   در حال حاضر سامانه برای هیچ کدام از خدمات خود کارمزدی دریافت نمی نماید.درآمد سامانه از مابه التفاوت خرید و فروش ارز دیجیتال به و یا از مشتریان حاصل می گردد.بدیهی است کارمزدهای سرویس دهندگان ثالت مانند شبکه بلاکچین و شاپرک بر عهده مشتری می باشد. 
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
