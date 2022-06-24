<?php
if (!session_id()) {    
    session_start();
}

$root_path = "../../";
include_once($root_path.'include/global.php');
include_once($root_path.'include/bitex.php');

session_secure();
$user_login = false;
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;    
}

$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];
$token = "";
$confirm_link = false;
if(isset($_GET['token']) && !empty($_GET['token'])) {
    $token = validate_input($_GET['token']);
    $get_withdraw = get_withdraw_confirm_info($token);
    if(count($get_withdraw)) { 
        $confirm_link = true;
        $datas = array();
        $datas['status'] = "1";
        if($get_withdraw[0]['status'] === "0")
            update_withdraw($get_withdraw[0]['id'],$datas);
    }        
}

?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>تایید برداشت</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php if($user_login) add_dashboard_navbar($root_path,$username); else add_home_navbar($root_path,$user_login); ?>
        <?php if($user_login) add_dashboard_toolbar($root_path); else add_home_toolbar($root_path,$user_login); ?>
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-2-@s home-content" uk-grid="">

                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                               <?php if($confirm_link) { ?>
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/withdrawal_ok.png">
                                <?php } else {  ?>
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/withdrawal_fail.png">
                                <?php } ?>
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تایید برداشت</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-text-center">
                           <?php if($confirm_link) { ?>
                            <span class="shabnam">درخواست برداشت شما با موفقیت ثبت و در صف پرداخت قرار گرفت.</span>
                           <?php } else { ?>
                            <span class="shabnam">لینک تایید برداشت اشتباه می باشد یا تاریخ آن منقضی گردیده است.</span>
                           <?php } ?>  
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php add_footer($root_path); ?>        
    </div>
</body>

</html>
