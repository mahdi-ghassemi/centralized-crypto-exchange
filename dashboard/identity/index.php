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
get_visitor_info($ipaddress,$page,$referrer,$useragent);
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به احراز هویت',$_SESSION['bitex_username']);
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
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];

$mobile_confirm = $user_info[0]['mobile_confirm'];
$email_confirm = $user_info[0]['email_confirm'];
$tow_fa = $user_info[0]['tow_fa'];
$mobile_2af = $user_info[0]['mobile_2af'];
$ga_2af = $user_info[0]['ga_2af'];
$ga_code = $user_info[0]['ga_code'];
$mobile_number = $user_info[0]['mobile'];
$email = $user_info[0]['email'];
$identity_confirm = $user_info[0]['identity_confirm'];
$selfi_confirm = $user_info[0]['selfi_confirm'];

$bank_list = array();
$bank_list = get_bank_list();

$sec_ques_list = array();
$sec_ques_list = get_security_question_list();

$bank_account_info = get_user_bank_account_by_user_id($_SESSION['user_id']);
$btn = "1";
if(isset($_GET['p']) && !empty($_GET['p'])) {
    $p = validate_input($_GET['p']);
    if($p === "identity")
        $btn = "2";
    if($p === "bank")
        $btn = "3";
    if($p === "mobile")
        $btn = "4";
}


?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php add_asset($root_path); ?>
    <title>احراز هویت</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $root_path; ?>asset/css/persian-datepicker.css">
    <link href="<?php echo $root_path; ?>asset/css/jquery.dm-uploader.min.css" rel="stylesheet">
    <link href="<?php echo $root_path; ?>asset/css/uploader-laptop.css?version=<?php echo $version; ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/dataTables.uikit.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/sumoselect.css">

    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/persian-date.min.js"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/persian-datepicker.min.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/jquery.dm-uploader.min.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/uploader-ui.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/uploader-config.js?version"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/jquery.dataTables.min.js"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/dataTables.uikit.min.js"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/dataTables.buttons.min.js"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/buttons.html5.min.js"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/jquery.sumoselect.min.js"></script>
    <script type="application/javascript" src="<?php echo $root_path; ?>asset/js/persian.min.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/identity.js?version=<?php echo $version; ?>"></script>

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>
        <div class="uk-form-stacked  uk-child-width-1-1 uk-child-width-1-1@s uk-text-center identity-menu">
            <div class="uk-text-center btn-identity-div">
                <button id="securtiy" class="uk-button uk-button-small uk-margin-left orange-btn">تنظیمات امنیتی</button>
                <button id="identity" class="uk-button uk-button-small uk-margin-left orange-btn">ارسال مدارک هویتی</button>
                <button id="bank" class="uk-button uk-button-small uk-margin-left orange-btn">اطلاعات حساب بانکی</button>
                <button id="mobile_conf" class="uk-button uk-button-small uk-margin-left orange-btn">تایید شماره همراه</button>
                <button id="email_conf" class="uk-button uk-button-small uk-margin-left orange-btn">تایید آدرس ایمیل</button>
            </div>
        </div>
        <input id="btn" type="text" value="<?php echo $btn; ?>" hidden>

        <div id="content" class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s">
            <div id="sub_content" class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-2@s" uk-grid="">

                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="../../asset/img/change_password.png" alt="change password">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تغییر کلمه عبور</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div>
                            <label class="uk-form-label">کلمه عبور فعلی: </label>
                            <div class="uk-form-controls">
                                <input id="password" class="uk-input order-select-input direction-ltr input-font" type="password" value="" oninput="check_error(this)">
                            </div>
                            <span id="password_err" class="shabnam elzami"></span>
                        </div>
                        <div>
                            <label class="uk-form-label">کلمه عبور جدید: </label>
                            <div class="uk-form-controls">
                                <input id="new_password" class="uk-input order-select-input direction-ltr input-font" type="password" value="" oninput="check_error(this)">
                            </div>
                            <span id="new_password_err" class="shabnam elzami"></span>
                        </div>
                        <div>
                            <label class="uk-form-label">تکرار کلمه عبور جدید: </label>
                            <div class="uk-form-controls">
                                <input id="re_new_password" class="uk-input order-select-input direction-ltr input-font" type="password" value="" oninput="check_error(this)">
                            </div>
                            <span id="re_new_password_err" class="shabnam elzami"></span>
                        </div>

                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <button id="pass_chang_btn" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ذخیره تغییر کلمه عبور</button>
                    </div>
                </div>
                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="../../asset/img/2fa.png" alt="2fa">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">ورود دو مرحله ای</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto" uk-grid="">

                                <div>
                                    <label class="lable-checkbox">
                                        <input class="uk-checkbox" type="checkbox" id="2fa_chb" name="2fa_chb" <?php if($tow_fa == "1") echo 'checked';?>>ورود دو مرحله ای فعال شود </label>
                                </div>
                                <div id="2fa_opt" <?php if($tow_fa == "0") echo 'class="disabled_div"';?>>
                                   <?php if($mobile_confirm === "1") { ?>
                                    <label class="lable-checkbox"><input class="uk-radio" id="mobile_2af" name="2fa_choice" type="radio" <?php if($mobile_2af == "1") echo 'checked'; ?>>ارسال پیامک به شماره همراه</label>
                                    <?php } ?>

                                    <label class="lable-checkbox"><input class="uk-radio" id="ga_2af" name="2fa_choice" type="radio" <?php if($ga_2af == "1") echo 'checked'; ?>>استفاده از Google Authenticator</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <button id="towfa_chang_btn" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ذخیره تغییرات ورود دو مرحله ای</button>
                    </div>
                </div>
                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="../../asset/img/security_question.png" alt="security question">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">سوال محرمانه</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div>
                            <span class="shabnam elzami">در صورت بروز مشکل برای حساب کاربری شما، با استفاده از این امکان می توانید حساب خود را بازیابی نمایید.</span>
                        </div>
                        <br>
                        <div>
                            <label class="uk-form-label">یک سوال را انتخاب نمایید: </label>
                            <div class="SumoSelect sumo_somename" tabindex="0" role="button" aria-expanded="true">
                                <select id="sec_ques_id" class="testselect2 SumoUnder" tabindex="-1">
                                    <?php foreach($sec_ques_list as $q){ ?>
                                    <option value="<?php echo $q['id']; ?>"><?php echo $q['title']; ?></option>
                                    <?php } ?>
                                </select>
                                <label><i></i></label>
                            </div>
                        </div>
                        <br>
                        <div>
                            <label class="uk-form-label">پاسخ خود را وارد نمایید: </label>
                            <div class="uk-form-controls">
                                <input id="sec_answ" class="uk-input order-select-input input-font" type="text" value="">
                            </div>
                            <span id="sec_answ_err" class="shabnam elzami"></span>
                        </div>
                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <button id="sec_ques_btn" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ثبت سوال امنیتی</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="content2" class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s">
            <div class="uk-form-stacked uk-padding uk-child-width-1-1 uk-child-width-1-1@s">
                <?php if($identity_confirm === "0") { ?>
                <div class="uk-alert-primary alert" uk-alert=""> جهت انجام سفارشات خرید و فروش از صرافی آسان هویت شما باید تایید گردد. جهت تایید هویت، ابتدا مشخصات هویتی خود مطابق با کارت ملی هوشمند خود را در بخش مشخصات فردی وارد نموده و دکمه ثبت مشخصات هویتی را کلیک نمایید.
                </div>
                <?php } ?>
                <?php if($selfi_confirm === "0") { ?>
                <div class="uk-alert-primary alert" uk-alert=""> تصاویر مربوط به کارت هوشمند ملی و عکس سلفی به همراه کارت ملی و تعهد نامه را مطابق با الگوی نمونه بارگذاری و ارسال نمایید.
                </div>
                <div class="uk-alert-primary alert" uk-alert="">چنانچه شماره همراه یا آدرس ایمیل شما تایید گردیده باشد، نتیجه تایید هویت شما پس از کنترل توسط کارشناسان در اسرع وقت از طریق پیامک یا ایمیل به اطلاع شما خواهد رسید.
                </div>
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: اطلاعات تایید شده قابل ویرایش نمی باشد.
                </div>
                <?php } ?>
                <div class="uk-card uk-card-default uk-width-1-1@m card-setting">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/identity.png" alt="identity">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title"> مشخصات هویتی</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-5@s" uk-grid="">
                            <div>
                                <label class="uk-form-label">نام: </label>
                                <div class="uk-form-controls">
                                    <input id="firstname" class="uk-input order-select-input input-font" type="text" value="<?php echo $user_info[0]['firstname']; ?>" placeholder=" به فارسی وارد نمایید">
                                </div>
                                <span id="firstname_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">نام خانوادگی: </label>
                                <div class="uk-form-controls">
                                    <input id="lastname" class="uk-input order-select-input input-font" type="text" value="<?php echo $user_info[0]['lastname']; ?>" placeholder=" به فارسی وارد نمایید">
                                </div>
                                <span id="lastname_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">نام پدر: </label>
                                <div class="uk-form-controls">
                                    <input id="fathername" class="uk-input order-select-input input-font" type="text" placeholder="به فارسی وارد نمایید" value="<?php echo $user_info[0]['fathername']; ?>">
                                </div>
                                <span id="fathername_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">کد ملی: </label>
                                <div class="uk-form-controls">
                                    <input id="code_meli" class="uk-input order-select-input input-font" type="text" placeholder="به صورت عدد" value="<?php echo $user_info[0]['code_meli']; ?>">
                                </div>
                                <span id="code_meli_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">تاریخ تولد: </label>
                                <div class="uk-form-controls">
                                    <input id="birthdate" class="uk-input order-select-input input-font" type="text" readonly name="birthdate" value="<?php echo $user_info[0]['birthday']; ?>" placeholder="در این کادر کلیک نمایید">
                                </div>
                                <span id="birthdate_err" class="shabnam elzami"></span>
                            </div>
                        </div>
                    </div>
                    <?php if($user_info[0]['identity_confirm'] == "0") { ?>
                    <div id="footer" class="uk-card-footer setting-footer">
                        <div class="row">
                            <div class="uk-text-center">
                                <button id="profile_save_btn" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ثبت مشخصات هویتی</button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>


            </div>
            <?php if($selfi_confirm === "0") { ?>
            <div class="uk-form-stacked uk-padding uk-child-width-1-1 uk-child-width-1-2@s uk-flex uk-flex-row uk-flex-around flex-col">

                <div class="uk-card uk-card-default uk-width-1-1@m card-id">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/id_card.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">نمونه تصویر کارت هوشمند ملی</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body uk-flex uk-flex-column uk-flex-right">
                        <div class="uk-text-center">
                            <img src="../../asset/img/id_card2.jpg">
                        </div>
                    </div>
                </div>
                <div class="uk-card uk-card-default uk-width-1-1@m card-id">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/selfi.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">نمونه تصویر سلفی </h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body uk-flex uk-flex-column uk-flex-right">
                        <div class="uk-text-center">
                            <img src="../../asset/img/selfi3.png">
                        </div>                        
                    </div>
                </div>


            </div>

            <div class="uk-form-stacked uk-padding uk-child-width-1-1 uk-child-width-1-1@s">
                <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a> حداکثر حجم برای ارسال هر فایل 32 مگابایت می باشد.تصاویر با پسوند jpg و jpeg و png قابل بارگذاری می باشند.
                </div>
                <div class="uk-alert-primary alert" uk-alert=""><a class="uk-alert-close" uk-close></a>بعد از انتخاب فایل یا فایلهای خود و اضافه شدن آنها به کادر لیست فایلها، دکمه شروع بارگذاری را کلیک نمایید.در صورت بارگذاری موفق پیام موفقیت آمیز بودن بارگذاری نمایش داده خواهد شد.
                </div>
                <!-- Our markup, the important part here! -->
                <div id="drag-and-drop-zone-tarjome" class="dm-uploader">
                    <h3 class="ts-drag-title"> فایل تصویر مدارک را به این کادر کشیده و رها کنید یا روی انتخاب فایل کلیک نمایید</h3>
                    <div class="uk-button uk-button-small pay_btn btn">
                        <span>انتخاب فایل</span>
                        <input type="file" title='برای اضافه نمودن فایل کلیک کنید' />
                    </div>
                </div>
                <br>
                <div class="uk-flex uk-flex-row uk-flex-between flex-col">
                    <div class="div-upload-btn">
                        <a href="#" class="uk-button uk-button-primary uk-button-small new_order_btn" id="btnApiStart-tarjome">
                            <span uk-icon="icon: upload"></span> شروع بارگذاری
                        </a>
                        <a href="#" class="uk-button uk-button-danger uk-button-small cancel_btn" id="btnApiCancel-tarjome">
                            <span uk-icon="icon:  ban"></span> توقف بارگذاری
                        </a>
                    </div>

                    <div class="div-upload-file-list">
                        <div class="card h-100">
                            <div class="card-header">لیست فایلها</div>
                            <ul class="ts-dir-ltr" id="files-tarjome">

                            </ul>
                        </div>
                    </div>

                </div>



                <!-- File item template -->
                <script type="text/html" id="files-template-tarjome">
                    <li class="media">
                        <div class="media-body mb-1">
                            <p class="mb-2">
                                <strong>%%filename%%</strong> - <span class="text-muted">در انتظار بارگذاری</span>
                            </p>
                            <div class="progress mb-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <hr class="mt-1 mb-1" />
                        </div>
                    </li>

                </script>

                <!-- Debug item template -->
                <script type="text/html" id="debug-template-tarjome">
                    <li class="list-group-item text-%%color%%"><strong>%%date%%</strong>: %%message%%</li>

                </script>
            </div>
            <?php } ?>

        </div>

        <div id="content3" class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s">
            <div class="uk-form-stacked uk-padding uk-child-width-1-1 uk-child-width-1-1@s">
                <div class="uk-alert-primary alert" uk-alert=""> کارت بانکی باید حتماً به نام شما باشد.صرافی آسان از خرید با کارتهای بانکی که تایید نشده باشند جلوگیری به عمل آورده و مبلغ خرید به حساب خریدار برگشت داده خواهد شد.ورود شماره شبا و شماره حساب برای کارت بانکی الزامی نمی باشد.
                </div>
                <div class="uk-card uk-card-default uk-width-1-1@m card-setting">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="42" src="<?php echo $root_path; ?>asset/img/card_plus.png" alt="bank info">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title"> اضافه نمودن حساب بانکی جدید</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-5@s" uk-grid="">
                            <div>
                                <label class="uk-form-label">شماره کارت: <span class="elzami">(الزامی)</span></label>
                                <div class="uk-form-controls">
                                    <input id="card_number" class="uk-input order-select-input input-font" type="text" value="" placeholder="بدون فاصله وارد شود">
                                </div>
                                <span id="card_number_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">شماره حساب: </label>
                                <div class="uk-form-controls">
                                    <input id="acc_number" class="uk-input order-select-input input-font" type="text" value="" placeholder="بدون فاصله وارد شود">
                                </div>
                                <span id="acc_number_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">شماره شبا: </label>
                                <div class="uk-form-controls">
                                    <input id="shaba" class="uk-input order-select-input input-font" type="text" value="" placeholder=" بدون IR وارد شود">
                                </div>
                                <span id="shaba_err" class="shabnam elzami"></span>
                            </div>
                            <div>
                                <label class="uk-form-label">بانک صادر کننده: </label>
                                <div class="SumoSelect sumo_somename" tabindex="0" role="button" aria-expanded="true">
                                    <select id="bank_id" class="testselect2 SumoUnder" tabindex="-1">
                                        <option value=""></option>
                                        <?php foreach($bank_list as $bank){ ?>
                                        <option value="<?php echo $bank['id']; ?>"><?php echo $bank['title']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label><i></i></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="footer" class="uk-card-footer setting-footer">
                        <div class="row">
                            <div class="uk-text-center">
                                <button id="bank_save_btn" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ثبت اطلاعات حساب بانکی </button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="uk-card uk-card-default uk-width-1-1@m card-setting">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="42" src="<?php echo $root_path; ?>asset/img/card_bank.png" alt="bank card">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title"> اطلاعات حساب بانکی</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <table id="orders" class="uk-table uk-table-hover uk-table-divider">
                            <thead>
                                <tr>
                                    <th>شماره کارت</th>
                                    <th>شماره حساب</th>
                                    <th>شماره شبا</th>
                                    <th>بانک</th>
                                    <th>وضعیت</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bank_account_info as $row) { 
                                $status = "";
                                if($row['status'] == "0")
                                    $status = 'درحال بررسی';
                                if($row['status'] == "2")
                                    $status = 'تایید شده';
                                if($row['status'] == "1")
                                    $status = 'رد شده';
                                if($row['status'] == "3")
                                    $status = 'مسدود شده';
                                
                                ?>
                                <tr class="font-yekan">
                                    <td><span><?php echo $row['card_number']; ?></span></td>
                                    <td><span><?php echo $row['acc_number']; ?></span></td>
                                    <td><span><?php echo $row['shaba']; ?></span></td>
                                    <td><span><?php echo $row['bank_name']; ?></span></td>
                                    <td><span><?php if($row['status'] == "0") echo '<i class="fa fa-spinner fa-spin"></i> '; ?> <?php echo $status; ?></span></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div id="content4" class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s">
            <div id="sub_content" class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-2@s" uk-grid="">

                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="48" height="70" src="../../asset/img/mobile_check.png" alt="mobile check">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تایید شماره همراه</h3>
                            </div>
                        </div>
                    </div>
                    <?php if($mobile_confirm == "0") { ?>
                    <div id="mobile_confirm_div" class="uk-card-body">
                        <div>
                            <label class="uk-form-label">شماره همراه: </label>
                            <div class="uk-form-controls">
                                <input id="mobile" class="uk-input order-select-input input-font uk-text-center" type="text" value="<?php echo $mobile_number;?>">
                            </div>
                            <span id="mobile_err" class="shabnam elzami"></span>
                        </div>
                        <hr>
                        <div class="uk-text-center">
                            <button id="send_sms" class="uk-button uk-button-small pay_btn">دریافت کد تایید<i class="fa fa-spinner fa-spin spinner-onload"></i> </button>
                        </div>
                        <hr>
                        <div>
                            <label class="uk-form-label">کد تایید ارسال شده به شماره همراه: </label>
                            <div class="uk-form-controls uk-text-center">
                                <input id="mobile_confirm_code" class="uk-input order-select-input input-font uk-text-center" type="text" value="" placeholder="123456">
                            </div>
                        </div>

                    </div>
                    <div id="mobile_confirm_footer" class="uk-card-footer uk-text-center">
                        <button id="check_mobile_confirm_code" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> بررسی کد تایید </button>
                        <br>
                        <div class="alert-danger" role="alert">
                            <span class="shabnam elzami">کد اشتباه است</span>
                        </div>
                        <br><a id="send_sms_again_2" class="shabnam elzami">ارسال مجدد کد تایید</a>
                        <div class="alert-info" role="alert">
                            <span class="shabnam elzami">کد تایید با موفقیت ارسال گردید</span>
                        </div>
                    </div>
                    <?php } elseif($mobile_confirm == "1") { ?>
                    <div class="uk-card-body">
                        <div>                            
                            <p class="calculate-title">شماره همراه شما قبلا تایید گردیده است.</p>
                        </div>
                    </div>

                    <?php } ?>
                </div>
            </div>
        </div>

        <div id="content5" class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s">
            <div id="sub_content" class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-2@s" uk-grid="">

                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="../../asset/img/email_check.png" alt="email check">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تایید آدرس ایمیل</h3>
                            </div>
                        </div>
                    </div>
                    <?php if($email_confirm == "0") { ?>
                    <div id="email_confirm_div" class="uk-card-body">
                        <div>
                            <label class="uk-form-label">آدرس ایمیل: </label>
                            <div class="uk-form-controls">
                                <input id="email" class="uk-input order-select-input uk-text-center" type="text" value="<?php echo $email; ?>" readonly>
                            </div>
                            <span id="email_err" class="shabnam elzami"></span>
                        </div>
                        <hr>
                        <div class="uk-text-center">
                            <button id="send_email_code" class="uk-button uk-button-small pay_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ارسال کد تایید</button>
                        </div>
                        <hr>
                        <div>
                            <label class="uk-form-label">کد تایید ارسال شده به آدرس ایمیل: </label>
                            <div class="uk-form-controls">
                                <input id="email_confirm_code" class="uk-input order-select-input uk-text-center input-font" type="text" value="" placeholder="123456">
                            </div>
                        </div>

                    </div>
                    <div id="email_confirm_footer" class="uk-card-footer uk-text-center">
                        <button id="check_email_confirm_code" class="uk-button uk-button-small fosfor-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> بررسی کد </button>
                        <br>
                        <div class="alert-danger" role="alert">
                            <span class="shabnam elzami">کد اشتباه است</span>
                        </div>
                        <br><a id="send_email_again" class="shabnam elzami">ارسال مجدد کد تایید</a>
                        <div class="alert-info" role="alert">
                            <span class="shabnam elzami">کد تایید با موفقیت ارسال گردید</span>
                        </div>
                    </div>
                    <?php } elseif($email_confirm == "1") { ?>
                    <div class="uk-card-body">
                        <div>
                            <p class="calculate-title">آدرس ایمیل شما قبلا تایید گردیده است.</p>
                        </div>
                    </div>

                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <?php add_info_modal(); ?>
    <?php add_sms_verify_modal($root_path); ?>
    <?php add_ga_2fa_create_modal($root_path,$ga_code); ?>
    <?php add_ga_check_modal($root_path); ?>
</body>

</html>
