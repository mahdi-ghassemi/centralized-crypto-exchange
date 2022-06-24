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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به تیکت',$_SESSION['bitex_username']);
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

$ticket_type = array();
$ticket_type = get_ticket_type();

if(isset($_SESSION['ticket_temp_filename'])) 
    unset($_SESSION['ticket_temp_filename']);


?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <?php add_asset($root_path); ?>
    <title>تیکت</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link href="<?php echo $root_path; ?>asset/css/jquery.dm-uploader.min.css" rel="stylesheet">
    <link href="<?php echo $root_path; ?>asset/css/uploader-laptop.css" rel="stylesheet">

    <script src="<?php echo $root_path; ?>asset/js/jquery.dm-uploader.min.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/uploader-ui.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/uploader-ticket-config.js"></script>
    <script src="<?php echo $root_path; ?>asset/js/ticket.js?version=<?php echo $version; ?>"></script>

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-@s home-content" uk-grid="">
                <div class="uk-alert-primary alert" uk-alert="">
                    <p>قبل از ارسال تیکت لطفاً <a href="<?php echo $root_path; ?>faq/">سوالات متداول</a> را مطالعه نمایید.</p>
                </div>
                <div class="uk-card uk-card-default card-new-ticket">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="40" height="40" src="<?php echo $root_path; ?>asset/img/new_ticket.png">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تیکت جدید</h3>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div>
                            <label class="uk-label shabnam-light">موضوع</label>
                            <select id="ticket_type" class="uk-select">
                                <?php foreach($ticket_type as $t_t) { ?>
                                <option value="<?php echo $t_t['id'];?>"><?php echo $t_t['title']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <br>
                        <div>
                            <label class="uk-label shabnam-light">عنوان</label>
                            <input id="subject" type="text" value="" class="uk-input">
                        </div>
                        <br>
                        <div>
                            <label class="uk-label shabnam-light">توضیحات</label>
                            <textarea id="description" class="uk-textarea" rows="10"></textarea>
                        </div>
                        <br>
                        <label class="uk-label shabnam-light">فایل ضمیمه</label>
                        <!-- Our markup, the important part here! -->
                        <div id="drag-and-drop-zone-tarjome" class="dm-uploader">
                            <h3 class="ts-drag-title"> فایل تصویر را به این کادر کشیده و رها کنید یا روی انتخاب فایل کلیک نمایید</h3>
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
                    <div class="uk-card-footer uk-text-center">
                        <button id="save_bottom" class="uk-button uk-button-primary uk-button-small new_order_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ارسال تیکت </button>
                    </div>
                </div>
                <div>
                    <span id="error1" style="color:red;"></span><br>
                    <span id="error2" style="color:red;"></span><br>
                </div>

            </div>
        </section>
        <?php add_footer($root_path); 
              add_info_modal();
        ?>
    </div>
</body>

</html>
