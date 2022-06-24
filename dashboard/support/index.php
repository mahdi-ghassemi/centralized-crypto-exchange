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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به پشتیبانی',$_SESSION['bitex_username']);
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

$user_tickets = array();
$user_tickets = get_user_root_tickets($_SESSION['user_id']);

$new_ticket_count = get_new_ticket_count($_SESSION['user_id']);



?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>پشتیبانی</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/datatables.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <script src="<?php echo $root_path; ?>asset/js/support.js?version=<?php echo $version; ?>"></script>
    <script src="<?php echo $root_path; ?>asset/js/datatables.min.js"></script>

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-2-@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-2@m card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/support.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">پشتیبانی</h3>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-text-center">
                            <span class="shabnam">سوالات و مشکلات خود را می توانید در تمام ساعات شبانه روز از طریق ارسال تیکت و یا کانال های ارتباطی زیر با ما در میان بگذارید.</span>
                        </div>
                        <br><br>
                        <span class="uk-icon-navy" uk-icon="receiver"></span>
                        <span class="shabnam elzami">شماره تماس (پاسخ گویی در ساعات اداری) </span>
                        <br>
                        <div class="uk-text-center">
                            <span class="shabnam">09018193375</span>
                        </div>
                        <br><br>
                        <span class="uk-icon-navy" uk-icon="mail"></span>
                        <span class="shabnam elzami">ایمیل پشتیبانی</span>
                        <br>
                        <div class="uk-text-center">
                            <span class="shabnam">support[at]esaraafi[dot]ir</span>
                        </div>
                        <br><br>
                        <span class="uk-icon-navy" uk-icon="social"></span>
                        <span class="shabnam elzami">پشتیبانی در پیام رسان ها</span>
                        <br>
                        <div class="uk-text-center">
                            <a href="https://t.me/esaraafi_support" target="_blank"><img src="<?php echo $root_path; ?>asset/img/telegram.png" width="32" height="32"></a>
                            <a href="https://wa.me/989018193375" target="_blank"><img src="<?php echo $root_path; ?>asset/img/whatsapp.png" width="32" height="32"></a>
                        </div>
                        <br>
                        <hr>
                        <div class="uk-text-center">
                            <button id="new_ticket" class="uk-button new_order_btn">ایجاد تیکت</button>
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
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/support.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تاریخچه تیکت ها </h3>
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
                                        <th class="uk-table-expand">عنوان</th>
                                        <th class="uk-table-expand">تاریخ</th>
                                        <th>وضعیت</th>
                                        <th>موضوع</th>
                                        <th>پاسخ ها</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($user_tickets as $ticket) { ?>
                                    <?php $table = get_tickets_by_root_id_from_admin_unread($ticket['id']); ?>
                                    <tr <?php if(count($table)) echo 'class="unread-ticket"'; ?>>
                                        <td>
                                            <?php echo $ticket['subject']; ?>
                                            <?php if(count($table)) { ?>
                                            <span class="new-ticket-span">&nbsp;جدید&nbsp;</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo $ticket['create_time'].' '.$ticket['create_date']; ?>
                                        </td>
                                        <td>
                                            <?php if($ticket['status'] === "1") echo '<span class="uk-label uk-label-success">باز</span>'; elseif($ticket['status'] === "2") echo '<span class="uk-label uk-label-danger">بسته</span>'; ?>
                                        </td>
                                        <td>
                                            <?php echo $ticket['type_title']; ?>
                                        </td>
                                        <td>
                                            <?php echo get_ticket_replies($ticket['id']); ?>
                                        </td>
                                        <td>
                                            <button id="<?php echo $ticket['id']; ?>" type="button" class="uk-button uk-button-primary uk-button-small orange-btn" onclick="view(this)">مشاهده</button>
                                        </td>
                                    </tr>
                                    <?php }  ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php add_footer($root_path); ?>
    </div>
</body>

</html>
