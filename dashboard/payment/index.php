<?php
if (!session_id()) {
    ini_set( 'session.cookie_httponly', 1 );
    ini_set( 'session.cookie_secure', 1 );
    session_start();
}

$root_path = "../../";



include_once($root_path.'include/global.php');
include_once($root_path.'include/jdf.php');
include_once($root_path.'include/bitex.php');

session_secure();

if( !isset($_SESSION['bitex_sessionid']) || !isset($_SESSION['user_id']) || $_SESSION['bitex_sessionid'] !== md5($_SESSION['user_id'])) {
    header("location:".$root_path."login/");
    exit(); 
}

if( !isset($_SESSION['pay_info_amount']) ) {
    header("location:".$root_path."dashboard/");
    exit();    
}

$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];


$params = array(
  'order_id' => $_SESSION['pay_info_order_id'],
  'amount' => $_SESSION['pay_info_amount_toman'] * 10 ,
  'name' => $user_info[0]['firstname'].' '.$user_info[0]['lastname'],
  'phone' => $user_info[0]['username'],
  'mail' => $user_info[0]['email'],
  'desc' => 'سفارش شماره '.' '.$_SESSION['pay_info_order_id'],
  'callback' => '',
  'reseller' => null,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, '');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
  'X-API-KEY: ',
  'X-SANDBOX: 0'
));

$result = curl_exec($ch);
curl_close($ch);

$json = json_decode($result);
$error = 0;
if($json->id != null && $json->error_code == null ) {
    $datas = array();
    $datas['order_id'] = $_SESSION['pay_info_order_id'];
    $datas['user_id'] = $_SESSION['user_id'];
    $datas['idpay_id'] = $json->id;
    $datas['amount'] = $_SESSION['pay_info_amount_toman'];
    $datas['create_date'] = jdate('Y-m-d','','','','en');
    $datas['create_time'] = jdate('H:i:s','','','','en');
    $datas['link'] = $json->link;
    $last_id = insert_idpay_payment($datas);
    if($last_id > 0) {
        header("Location: ".$json->link);
        exit();
    } else {
        $error = 1;
    } 
} else {
    $error = $json->error_code;    
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>در حال انتقال</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>

        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s dashboard-content" uk-grid="">
            <div class="uk-text-center uk-flex uk-flex-column">
               <span>در حال انتقال به درگاه بانکی...</span>
                <progress id="js-progressbar" class="uk-progress" value="10" max="100"></progress>

                <script>
                    UIkit.util.ready(function() {

                        var bar = document.getElementById('js-progressbar');

                        var animate = setInterval(function() {

                            bar.value += 10;

                            if (bar.value >= bar.max) {
                                clearInterval(animate);
                            }

                        }, 1000);

                    });

                </script>
            </div>


        </div>
        <?php add_footer($root_path); ?>
    </div>
    <?php echo add_info_modal(); ?>
</body>

</html>