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
    insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ورود به پرداخت بیت کوین',$_SESSION['bitex_username']);
} else {
    header("location:".$root_path."login/");
    exit();
}

if(!isset($_SESSION['pay_info_order_id']) || !isset($_SESSION['pay_info_amount'])) {
    header("location:".$root_path."dashboard/");
    exit();    
}

$is_payment_exist = is_gourl_payment_exist($_SESSION['user_id'],$_SESSION['pay_info_order_id']);
if(!$is_payment_exist) {
    DEFINE("CRYPTOBOX_PHP_FILES_PATH", "../include/");        	// path to directory with files: cryptobox.class.php / cryptobox.callback.php / cryptobox.newpayment.php;         
                                                                // cryptobox.newpayment.php will be automatically call through ajax/php two times - payment received/confirmed
    DEFINE("CRYPTOBOX_IMG_FILES_PATH", "../asset/img/images/");        // path to directory with coin image files (directory 'images' by default)
    DEFINE("CRYPTOBOX_JS_FILES_PATH", "../asset/js/");			// path to directory with files: ajax.min.js/support.min.js


        // Change values below
        // --------------------------------------
    DEFINE("CRYPTOBOX_LANGUAGE_HTMLID", "alang");	// any value; customize - language selection list html id; change it to any other - for example 'aa';	default 'alang'
    DEFINE("CRYPTOBOX_COINS_HTMLID", "acoin");		// any value;  customize - coins selection list html id; change it to any other - for example 'bb';	default 'acoin'
    DEFINE("CRYPTOBOX_PREFIX_HTMLID", "acrypto_");	// any value; prefix for all html elements; change it to any other - for example 'cc';	default 'acrypto_'


        // Open Source Bitcoin Payment Library
        // ---------------------------------------------------------------
    require_once(CRYPTOBOX_PHP_FILES_PATH . "cryptobox.class.php" );



        /*********************************************************/
        /****  PAYMENT BOX CONFIGURATION VARIABLES  ****/
        /*********************************************************/

        // IMPORTANT: Please read description of options here - https://gourl.io/api-php.html#options

    $userID 			= $_SESSION['user_id'];        // place your registered userID or md5(userID) here (user1, user7, uo43DC, etc).
                                          // You can use php $_SESSION["userABC"] for store userID, amount, etc
                                          // You don't need to use userID for unregistered website visitors - $userID = "";
                                          // if userID is empty, system will autogenerate userID and save it in cookies
        $userFormat		= "SESSION";       // save userID in cookies (or you can use IPADDRESS, SESSION, MANUAL)
        $orderID		= $_SESSION['pay_info_order_id'];	  // invoice #000383
        $amountUSD		= "";			  // invoice amount - 0.12 USD; or you can use - $amountUSD = convert_currency_live("EUR", "USD", 22.37); // convert 22.37EUR to USD

        $period			= "2 HOURS";	  // one time payment, not expiry
        $def_language	= "en";			  // default Language in payment box
        $def_coin		= "bitcoin";      // default Coin in payment box



        // List of coins that you accept for payments
        //$coins = array('bitcoin', 'bitcoincash', 'bitcoinsv', 'litecoin', 'dogecoin', 'dash', 'speedcoin', 'reddcoin', 'potcoin', 'feathercoin', 'vertcoin', 'peercoin', 'monetaryunit', 'universalcurrency');
        $coins = array('bitcoin');  // for example, accept payments in bitcoin, bitcoincash, litecoin, 'dogecoin', dash, speedcoin 

        // Create record for each your coin - https://gourl.io/editrecord/coin_boxes/0 ; and get free gourl keys
        // It is not bitcoin wallet private keys! Place GoUrl Public/Private keys below for all coins which you accept

        $all_keys = array(	"bitcoin"   => array("public_key" => "",  
                                                "private_key" => "")); 

        // Re-test - all gourl public/private keys
        $def_coin = strtolower($def_coin);
        if (!in_array($def_coin, $coins)) $coins[] = $def_coin;  
        foreach($coins as $v)
        {
            if (!isset($all_keys[$v]["public_key"]) || !isset($all_keys[$v]["private_key"])) die("Please add your public/private keys for '$v' in \$all_keys variable");
            elseif (!strpos($all_keys[$v]["public_key"], "PUB"))  die("Invalid public key for '$v' in \$all_keys variable");
            elseif (!strpos($all_keys[$v]["private_key"], "PRV")) die("Invalid private key for '$v' in \$all_keys variable");
            elseif (strpos(CRYPTOBOX_PRIVATE_KEYS, $all_keys[$v]["private_key"]) === false) 
                    die("Please add your private key for '$v' in variable \$cryptobox_private_keys, file /lib/cryptobox.config.php.");
        }

        // Current selected coin by user
        $coinName = cryptobox_selcoin($coins, $def_coin);


        // Current Coin public/private keys
        $public_key  = $all_keys[$coinName]["public_key"];
        $private_key = $all_keys[$coinName]["private_key"];

        /** PAYMENT BOX **/
        $options = array(
            "public_key"  	=> $public_key,	    // your public key from gourl.io
            "private_key" 	=> $private_key,	// your private key from gourl.io
            "webdev_key"  	=> "", 			    // optional, gourl affiliate key
            "orderID"     	=> $orderID, 		// order id or product name
            "userID"      	=> $userID, 	    // unique identifier for every user
            "userFormat"  	=> $userFormat, 	// save userID in COOKIE, IPADDRESS, SESSION  or MANUAL
            "amount"   	  	=> $_SESSION['pay_info_amount'], // product price in btc/bch/bsv/ltc/doge/etc OR setup price in USD below
            "amountUSD"   	=> $amountUSD,	    // we use product price in USD
            "period"      	=> $period, 	// payment valid period
            "language"	  	=> $def_language    // text on EN - english, FR - french, etc
        );

        // Initialise Payment Class
        $box = new Cryptobox ($options);

        // coin name
        $coinName = $box->coin_name();

        if(isset($_SESSION['pay_info_amount']))
            unset($_SESSION['pay_info_amount']);
        if(isset($_SESSION['pay_info_order_id']))
            unset($_SESSION['pay_info_order_id']);
} else {
    $pay_info = get_gourl_payment_by_order_id($_SESSION['user_id'],$_SESSION['pay_info_order_id']);
    $status = $pay_info[0]['txConfirmed'];
}

$user_info = array();
$user_info = get_user_info_by_id($_SESSION['user_id']);
$username = $user_info[0]['username'];
if($user_info[0]['firstname'] != null)
    $username = $user_info[0]['firstname'].' '.$user_info[0]['lastname'];


?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>پرداخت بیت کوین</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">

    <script src="<?php echo CRYPTOBOX_JS_FILES_PATH; ?>support.min.js" crossorigin="anonymous"></script>
    <script src='<?php echo CRYPTOBOX_JS_FILES_PATH; ?>cryptobox.min.js' type='text/javascript'></script>

    <style>
        html {
            font-size: 14px;
        }

        @media (min-width: 768px) {
            html {
                font-size: 16px;
            }

            .tooltip-inner {
                max-width: 350px;
            }
        }

        .mncrpt .container {
            max-width: 980px;
        }

        .mncrpt .box-shadow {
            box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05);
        }

        img.radioimage-select {
            padding: 7px;
            border: solid 2px #ffffff;
            margin: 7px 1px;
            cursor: pointer;
            box-shadow: none;
        }

        img.radioimage-select:hover {
            border: solid 2px #a5c1e5;
        }

        img.radioimage-select.radioimage-checked {
            border: solid 2px #7db8d9;
            background-color: #f4f8fb;
        }

    </style>
</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>

        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-padding uk-child-width-1-1 uk-child-width-1-@s home-content" uk-grid="">
                <?php if(!$is_payment_exist) {  ?>
                <div class="uk-alert-primary alert" uk-alert="">لطفاً مبلغ مشخص شده در کادر پرداخت زیر را به آدرسی که مشخص شده ارسال نمایید.</div>
                <div class="uk-alert-primary alert" uk-alert="">در صورت تمایل می توانید صفحه را ببندید یا به صفحه داشبورد برگردید، پس از پرداخت و تایید آن توسط شبکه بلاکچین، فاکتور شما به صورت خودکار تایید می گردد.</div>
                <div class="uk-alert-primary alert" uk-alert=""> برای جلوگیری از مشکل دابل اسپندینگ ، پرداخت شما باید 6 تاییدیه از شبکه بلاک چین دریافت نماید تا وضعیت سفارش شما تایید پرداخت مشتری گردد.این پروسه ممکن است حدود 1 ساعت طول بکشد.لطفاً صبور باشید. همچنین بعد از دریافت تایید اول از شبکه بلاک چین سفارش شما به وضعیت تایید اول شبکه تغییر وضعیت خواهد داد.</div>
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: در هنگام پرداخت ، کارمزد انتقال بیت کوین از کیف پول خود را به مبلغ مشخص شده اضافه نمایید، چنانچه مبلغ واریزی با مبلغ مشخص شده در کادر زیر یکسان نباشد پرداخت شما برگشت خواهد خورد.</div>
                <?php       
    // Display payment box 	
    echo $box->display_cryptobox(false, 560, 230, "border-radius:15px;border:1px solid #eee;padding:3px 6px;margin:10px;",
                    "display:inline-block;max-width:580px;padding:15px 20px;border:1px solid #eee;margin:7px;line-height:25px;");
    ?>

                <?php } else {  
    if($status == "0") { 
        
         DEFINE("CRYPTOBOX_PHP_FILES_PATH", "../include/");       
         DEFINE("CRYPTOBOX_IMG_FILES_PATH", "../asset/img/images/");     
         DEFINE("CRYPTOBOX_JS_FILES_PATH", "../asset/js/");


        
        DEFINE("CRYPTOBOX_LANGUAGE_HTMLID", "alang");	
        DEFINE("CRYPTOBOX_COINS_HTMLID", "acoin");		
        DEFINE("CRYPTOBOX_PREFIX_HTMLID", "acrypto_");

        require_once(CRYPTOBOX_PHP_FILES_PATH . "cryptobox.class.php" );

        $userID 		= $_SESSION['user_id'];        
        $userFormat		= "SESSION";      
        $orderID		= $pay_info[0]['orderID'];	
        $amountUSD		= "";
        $period			= "2 HOURS";	  
        $def_language	= "en";			  
        $def_coin		= "bitcoin"; 
        
        $coins = array('bitcoin');  

        $all_keys = array(	"bitcoin"   => array("public_key" => "",  
                                                "private_key" => "")); 

        
        $def_coin = strtolower($def_coin);
        if (!in_array($def_coin, $coins)) $coins[] = $def_coin;  
        foreach($coins as $v)
        {
            if (!isset($all_keys[$v]["public_key"]) || !isset($all_keys[$v]["private_key"])) die("Please add your public/private keys for '$v' in \$all_keys variable");
            elseif (!strpos($all_keys[$v]["public_key"], "PUB"))  die("Invalid public key for '$v' in \$all_keys variable");
            elseif (!strpos($all_keys[$v]["private_key"], "PRV")) die("Invalid private key for '$v' in \$all_keys variable");
            elseif (strpos(CRYPTOBOX_PRIVATE_KEYS, $all_keys[$v]["private_key"]) === false) 
                    die("Please add your private key for '$v' in variable \$cryptobox_private_keys, file /lib/cryptobox.config.php.");
        }

        
        $coinName = cryptobox_selcoin($coins, $def_coin);


        
        $public_key  = $all_keys[$coinName]["public_key"];
        $private_key = $all_keys[$coinName]["private_key"];

        
        $options = array(
            "public_key"  	=> $public_key,	    
            "private_key" 	=> $private_key,	
            "webdev_key"  	=> "", 			    
            "orderID"     	=> $orderID, 		
            "userID"      	=> $userID, 	    
            "userFormat"  	=> $userFormat, 	
            "amount"   	  	=> $pay_info[0]['amount'], 
            "amountUSD"   	=> $amountUSD,	    
            "period"      	=> $period, 	
            "language"	  	=> $def_language    
        );
        $box = new Cryptobox ($options);        
        $coinName = $box->coin_name();       
    ?>
                <div class="uk-alert-primary alert" uk-alert="">لطفاً مبلغ مشخص شده در کادر پرداخت زیر را به آدرسی که مشخص شده ارسال نمایید.</div>
                <div class="uk-alert-primary alert" uk-alert="">در صورت تمایل می توانید صفحه را ببندید یا به صفحه داشبورد برگردید، پس از پرداخت و تایید آن توسط شبکه بلاکچین، فاکتور شما به صورت خودکار تایید می گردد.</div>
                <div class="uk-alert-primary alert" uk-alert=""> برای جلوگیری از مشکل دابل اسپندینگ ، پرداخت شما باید 6 تاییدیه از شبکه بلاک چین دریافت نماید تا وضعیت سفارش شما تایید پرداخت مشتری گردد.این پروسه ممکن است حدود 1 ساعت طول بکشد.لطفاً صبور باشید. همچنین بعد از دریافت تایید اول از شبکه بلاک چین سفارش شما به وضعیت تایید اول شبکه تغییر وضعیت خواهد داد.</div>
                <div class="uk-alert-primary alert-warrning" uk-alert="">توجه مهم: در هنگام پرداخت ، کارمزد انتقال بیت کوین از کیف پول خود را به مبلغ مشخص شده اضافه نمایید، چنانچه مبلغ واریزی با مبلغ مشخص شده در کادر زیر یکسان نباشد پرداخت شما برگشت خواهد خورد.</div>
                <?php       
    // Display payment box 	
    echo $box->display_cryptobox(false, 560, 230, "border-radius:15px;border:1px solid #eee;padding:3px 6px;margin:10px;",
                    "display:inline-block;max-width:580px;padding:15px 20px;border:1px solid #eee;margin:7px;line-height:25px;");
    ?>


                <?php } else {?>
                <div class="uk-alert-primary alert" uk-alert="">این سفارش قبلاً پرداخت گردیده است.</div>
                <?php } } ?>



            </div>
        </section>
        <?php add_footer($root_path); ?>
    </div>
</body>

</html>
