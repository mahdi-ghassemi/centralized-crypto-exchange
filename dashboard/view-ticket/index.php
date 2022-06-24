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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به مشاهده تیکت',$_SESSION['bitex_username']);
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



$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];

$ticket_id = "";
$ticket_info = array();
if(isset($_SESSION['selected_ticket_id']) && !empty($_SESSION['selected_ticket_id'])) {
    $ticket_id = $_SESSION['selected_ticket_id'];  
    $ticket_info = get_user_tickets_by_root_id($ticket_id);
    update_ticket_readed($ticket_id);
    if(isset($_SESSION['ticket_temp_filename'])) 
        unset($_SESSION['ticket_temp_filename']);
} else {
    header("Location: ".$root_path."dashboard/support/");
    exit();    
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>مشاهده تیکت</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">


    <script src="<?php echo $root_path; ?>asset/js/ticket.js?version=<?php echo $version; ?>"></script>

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-@s home-content" uk-grid="">

                <?php foreach($ticket_info as $ticket) { 
                    $ticket_files = array();
                    $ticket_files = get_ticket_files($ticket['id']);?>
                <div class="uk-card uk-card-default card-new-ticket">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                               <?php if($ticket['sender'] === 's') { ?>
                                <img width="40" height="40" src="<?php echo $root_path; ?>asset/img/support.png">
                                <?php } else { ?>
                                <img width="40" height="40" src="<?php echo $root_path; ?>asset/img/unknown.png">
                                <?php } ?>
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">شماره تیکت : <?php echo $ticket['id']; ?></h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php echo $ticket['create_time'].' '.$ticket['create_date'];?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div>
                            <label class="uk-label shabnam-light">عنوان</label>
                            <input id="subject" type="text" value="<?php echo $ticket['subject']; ?>" class="uk-input" readonly>
                        </div>
                        <br>
                        <div>
                            <label class="uk-label shabnam-light">توضیحات</label>
                            <textarea id="description" class="uk-textarea"  style="height:auto;" readonly><?php echo $ticket['description']; ?></textarea>
                        </div>
                        <?php if(count($ticket_files)) { ?>
                        <br>
                        <div uk-lightbox>
                            <?php for($i = 0;$i < count($ticket_files);$i++) { ?>
                            <a class="shabnam-light" href="<?php echo $root_path.'tickets-files/'.$ticket['id'].'/'.$ticket_files[$i]['file_name']; ?>"><?php if($i === 0) echo 'فایلهای ضمیمه'; ?> </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <br>
                        <span class="shabnam">موضوع:</span>&nbsp;<span class="shabnam-light">&nbsp;&nbsp;<?php echo $ticket['type_title']; ?>&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php if($ticket['status'] === "1") { ?>
                        <span class="shabnam">وضعیت:</span>&nbsp;<span class="uk-label uk-label-success shabnam">&nbsp;&nbsp;باز&nbsp;&nbsp;</span>
                        <?php } elseif($ticket['status'] === "2") { ?>
                        <span class="shabnam">وضعیت:</span>&nbsp;<span class="uk-label uk-label-danger shabnam">&nbsp;&nbsp;بسته&nbsp;&nbsp;</span>
                        <?php } ?>
                        <?php if($ticket['status'] === "1" && $ticket['sender'] === "s") { ?>
                        <button id="re<?php echo $ticket['id']; ?>" class="uk-button uk-button-small orange-btn uk-margin-right" onclick="reply(this)">پاسخ به تیکت</button>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>



            </div>
        </section>
        <?php add_footer($root_path); 
              add_info_modal();
        ?>
    </div>
</body>

</html>
