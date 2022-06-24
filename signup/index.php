<?php
if (!session_id()) {    
    session_start();
}

$root_path = "../";

include_once($root_path.'include/global.php');
include_once($root_path.'include/jdf.php');
include_once($root_path.'include/bitex.php');

session_secure();
get_visitor_info($ipaddress,$page,$referrer,$useragent);
$user_login = false;
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true; 
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به صفحه ثبت نام',null);
}

if($user_login) {
    header("Location: ".$root_path."/dashboard/");
    exit();
}

$ref_code = "";
if(isset($_GET['ref']) && !empty($_GET['ref'])){
    $ref_code = validate_input($_GET['ref']);
    if(isset($_SESSION['ref_code']))
        unset($_SESSION['ref_code']);
    $_SESSION['ref_code'] = $ref_code;
}

?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>ثبت نام</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/securimage.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">

    <script src="<?php echo $root_path; ?>asset/js/signup.js?version=<?php echo $version; ?>"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LfvkuUUAAAAACCwG88aj2K-jrCaEoKdd83vbgSb"></script>
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_home_navbar($root_path,$user_login); ?>
        <?php add_home_toolbar($root_path,$user_login); ?>
        <div class="uk-form-stacked uk-grid-small uk-flex-around uk-child-width-1-1 uk-child-width-1-1@s home-content" uk-grid="">
            <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                <div class="uk-card-header">
                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                        <div class="uk-width-auto">
                            <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/signup.png" alt="signup">
                        </div>
                        <div class="uk-width-expand">
                            <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">ثبت نام</h3>
                            <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                    <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="uk-card-body uk-flex uk-flex-column">

                    <!-- <div>
                            <label class="uk-form-label shabnam"> آدرس ایمیل یا شماره همراه <span class="elzami">(الزامی)</span></label>
                            <div class="uk-form-controls amount-div">
                                <input id="mobile" class="uk-input" type="text" lang="en" style="direction: ltr;" value="" title="نمونه: 09120000000" uk-tooltip="">
                            </div>
                            <span id="mobile_err" class="shabnam elzami"></span>
                        </div>-->
                    <div class="center">
                        <label class="uk-form-label shabnam"> آدرس ایمیل <span class="elzami">(الزامی)</span></label>
                        <div class="uk-form-controls amount-div center">
                            <input id="email" class="uk-input" type="text" lang="en" style="direction: ltr;" value="">
                        </div>
                    </div>
                    <span id="email_err" class="shabnam elzami"></span>

                    <br>

                    <div class="center">
                        <label class="uk-form-label shabnam">کلمه عبور <span class="elzami">(الزامی)</span></label>
                        <div class="uk-form-controls amount-div center">
                            <input id="password" class="uk-input" type="password" lang="en" style="direction: ltr;" value="">
                        </div>
                    </div>
                    <span id="password_err" class="shabnam elzami"></span>
                    <br>

                    <div class="center">
                        <label class="uk-form-label shabnam"> تکرار کلمه عبور <span class="elzami">(الزامی)</span></label>
                        <div class="uk-form-controls amount-div center">
                            <input id="passwordrep" class="uk-input" type="password" lang="en" style="direction: ltr;" value="">
                        </div>
                    </div>
                    <span id="passwordrep_err" class="shabnam elzami"></span>
                    <br>

                    <div class="center">
                        <div class="uk-form-controls shabnam rules center">
                            <label><input id="rules" name="rules" type="checkbox" class="uk-checkbox">قوانین سایت را می پذیرم</label>

                        </div>
                    </div>
                    <span id="rules_err" class="shabnam elzami"></span>
                    <br><br>

                    <div class="center">
                        <?php
      // show captcha HTML using Securimage::getCaptchaHtml()
      require_once $root_path.'include/securimage.php';
      $options = array();
      $options['input_name']             = 'ct_captcha'; // change name of input element for form post
      $options['input_text']             = 'حاصل عبارت ریاضی بالا را وارد نمایید:'; // change text of input element for form post
      $options['disable_flash_fallback'] = false; // allow flash fallback

      if (!empty($_SESSION['ctform']['captcha_error'])) {
        // error html to show in captcha output
        $options['error_html'] = $_SESSION['ctform']['captcha_error'];
      }

      echo "<div id='captcha_container_1' class='center'>\n";
      echo Securimage::getCaptchaHtml($options);
      echo "\n</div>\n";
    ?>

                    </div>
                    <span id="captcha_code_err" class="shabnam elzami"></span>


                    <input id="refer" type="text" value="<?php echo $ref_code; ?>" hidden>
                </div>
                <div class="uk-card-footer uk-text-center">
                    <button id="submit" class="uk-button uk-button-primary buy-btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ثبت نام</button>
                </div>
                <input id="token" type="hidden" name="token">
            </div>
        </div>
    </div>
    <?php add_footer($root_path);
          add_info_modal(); ?>
</body>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6LfvkuUUAAAAACCwG88aj2K-jrCaEoKdd83vbgSb', {
            action: 'homepage'
        }).then(function(token) {
            document.getElementById("token").value = token;
        });
    });

</script>

</html>
