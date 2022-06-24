<?php
if(!isset($_SESSION))
    session_start();
include_once('dal.php');
include_once('jdf.php');



$url = "";
$version = "1.56";
$no_reply_mail_address = '';
$no_reply_mail_password = ''; 
$to_mail = '';



$ipaddress = $_SERVER['REMOTE_ADDR'];
$page = "http://".$_SERVER['HTTP_HOST']."".$_SERVER['PHP_SELF'];
$referrer = "";
if(isset($_SERVER['HTTP_REFERER']))
     $referrer = $_SERVER['HTTP_REFERER'];
else
    $referrer = "Direct link.";
    
$useragent = $_SERVER['HTTP_USER_AGENT'];
function session_secure() {
    // Make sure we have a canary set
    if (!isset($_SESSION['canary'])) {
        session_regenerate_id(true);
        $_SESSION['canary'] = [
           'birth' => time(),
           'IP' => $_SERVER['REMOTE_ADDR']
        ];
    }
    if ($_SESSION['canary']['IP'] !== $_SERVER['REMOTE_ADDR']) {
        session_regenerate_id(true);
        // Delete everything:
        unset($_SESSION['bitex_sessionid']);
        $_SESSION['canary'] = [
            'birth' => time(),
            'IP' => $_SERVER['REMOTE_ADDR']
        ];
    }
    // Regenerate session ID every ten minutes:
    if ($_SESSION['canary']['birth'] < time() - 600) {
        session_regenerate_id(true);
        $_SESSION['canary']['birth'] = time();
    }
}

function get_visitor_info($ipaddress,$page,$referrer,$useragent){
    $visitor_info = array();
    $visitor_info['ip_address'] = $ipaddress;
    $visitor_info['current_page'] = $page;
    $visitor_info['referrer_page'] = $referrer;
    $visitor_info['browser'] = $useragent;
    $visitor_info['visit_date'] = date('Y-m-d');          
    $visitor_info['visit_time'] = date('H:i:s');
    
    insert('visitor',$visitor_info);
}

function insert_user_action($ip_address,$user_id,$act_msg,$act_desc){
    $act_info = array();
    $act_info['ip_address'] = $ip_address;
    $act_info['user_id'] = $user_id;
    $act_info['act_msg'] = $act_msg;
    $act_info['act_desc'] = $act_desc;
    $act_info['act_date'] = jdate('Y-m-d','','','','en');          
    $act_info['act_time'] = jdate('H:i:s','','','','en');
    
    insert('user_action_log',$act_info);
}


function validate_input($data){
    $data = str_replace("'"," ",$data);
    $data = str_replace('"',' ',$data);
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;
    }

/* Update 97-04-31 by Mehdi Ghassemi for get visitor and user ip */  
function get_user_ip(){
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}
/* end Update 97-04-31  */  


function add_asset($path){   
    global $version; 
    echo '<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="'.$path.'apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="'.$path.'favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="'.$path.'favicon-16x16.png">
    <link rel="manifest" href="'.$path.'site.webmanifest">
    <link rel="mask-icon" href="'.$path.'safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" type="text/css" href="'.$path.'asset/css/fonts.css">
    <link rel="stylesheet" type="text/css" href="'.$path.'asset/css/uikit-rtl.min.css">    
        
    <script type="application/javascript" src="'.$path.'asset/js/jquery-3.3.1.min.js"></script>            
    <script type="application/javascript" src="'.$path.'asset/js/uikit.min.js"></script>
    <script type="application/javascript" src="'.$path.'asset/js/uikit-icons.min.js"></script>
    <script type="application/javascript" src="'.$path.'asset/js/general.js?version='.$version.'"></script>';
}

function add_asset_ltr($path){   
    global $version; 
    echo '<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="'.$path.'apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="'.$path.'favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="'.$path.'favicon-16x16.png">
    <link rel="manifest" href="'.$path.'site.webmanifest">
    <link rel="mask-icon" href="'.$path.'safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" type="text/css" href="'.$path.'asset/css/fonts.css">
    <link rel="stylesheet" type="text/css" href="'.$path.'asset/css/uikit.min.css">    
        
    <script type="application/javascript" src="'.$path.'asset/js/jquery-3.3.1.min.js"></script>            
    <script type="application/javascript" src="'.$path.'asset/js/uikit.min.js"></script>
    <script type="application/javascript" src="'.$path.'asset/js/uikit-icons.min.js"></script>
    <script type="application/javascript" src="'.$path.'asset/js/general.js?version='.$version.'"></script>';
}

function get_persian_numbers($English_Number){
     return $Persian_Number = str_replace(
         array('0','1','2','3','4','5','6','7','8','9'),
         array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'),
         $English_Number);
}

function get_english_numbers($Persian_Number){
     return $English_Number = str_replace(        
         array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'),
         array('0','1','2','3','4','5','6','7','8','9'),
         $Persian_Number);
}


function searchForId($value, $array,$header) {
   foreach ($array as $key => $val) {
       if ($val[$header] === $value) {
           return $key;
       }
   }
   return null;
}

function miladi_month_to_farsi() {
    $m_day = date("F");
    $f_day = "";
    switch($m_day) {
        case 'January': {
            $f_day = 'ژانویه';
            break;
        }
        case 'February': {
            $f_day = 'فوریه';
            break;
        }
        case 'March': {
            $f_day = 'مارس';
            break;
        }
        case 'April': {
            $f_day = 'آوریل';
            break;
        }
        case 'May': {
            $f_day = 'می';
            break;
        }
        case 'June': {
            $f_day = 'ژوئن';
            break;
        }
        case 'July': {
            $f_day = 'ژوئیه';
            break;
        }
        case 'August': {
            $f_day = 'اوت';
            break;
        }
        case 'September': {
            $f_day = 'سپتامبر';
            break;
        }
        case 'October': {
            $f_day = 'اکتبر';
            break;
        }
        case 'November': {
            $f_day = 'نوامبر';
            break;
        }
        case 'December': {
            $f_day = 'دسامبر';
            break;
        }            
    }
    return $f_day;
}

function random_code($length){
    
    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "012346789abcdfghjkmnpqrtvwxyzABCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
            $length = $maxlength;
}

// set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, $maxlength-1), 1);

    // have we already used this character in $password?
    if (!strstr($password, $char)) {
    // no, so it's OK to add it onto the end of whatever we've already got...
            $password .= $char;
            // ... and increase the counter by one
            $i++;
}
}

// done!
return $password;
}

function get_last_version(){
    $query = "SELECT * FROM tbl_setting WHERE id = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]["last_version"]; 
}

function get_last_update(){
    $query = "SELECT * FROM tbl_setting WHERE id = 1";
    $table = array();
    $table = get_all_select_by_query($query);
    return $table[0]["update_date"];
}

function add_home_navbar($root_path,$user_login) {
    echo '<nav class="uk-navbar-container uk-margin" uk-navbar="mode: click">
                <div class="uk-navbar-right">
                    <a class="uk-navbar-item uk-margin-left" href="'.$root_path.'"><img class="logo-img"  src="'.$root_path.'asset/img/esaraafi_logo.png"></a>
                </div>';                
    echo '<div class="uk-navbar-left">
    
    
    <div class="menu-3line">
        <a href="#offcanvas-nav" uk-toggle uk-icon="icon: menu;ratio: 2"></a> 
    </div>
    
    <div id="offcanvas-nav" uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar">
        <ul class="uk-nav uk-nav-default">
        <li class="uk-nav-header"><a href="'.$root_path.'"><img src="'.$root_path.'asset/img/toolbar/home.png" alt="home">خانه</li>';
    if(!$user_login) {
        echo '<li class="uk-nav-header">
              <a href="'.$root_path.'login/"><img src="'.$root_path.'asset/img/toolbar/signin.png" alt="signin">ورود</a>   
                  </li>
                  <li class="uk-nav-header">
                    <a href="'.$root_path.'signup/"><img src="'.$root_path.'asset/img/toolbar/signup.png" alt="signup">ثبت نام</a>   
                  </li>';
    } else {
        echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/"><img src="'.$root_path.'asset/img/toolbar/dashboard.png" alt="dashboard">داشبورد</a>   
                  </li>';        
    }
    echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'terms/"><img src="'.$root_path.'asset/img/toolbar/rules.png" alt="rules">قوانین</a>   
                  </li>
                  <li class="uk-nav-header">
                    <a href="'.$root_path.'faq/"><img src="'.$root_path.'asset/img/toolbar/faq.png" alt="faq">سوالات متداول</a>   
                  </li>';
    if($user_login) {
        echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'logout/"><img src="'.$root_path.'asset/img/toolbar/logout.png" alt="logout">خروج</a>   
               </li>';    
    }
    echo '</ul>
        </div>
    </div>';
    if(!$user_login) { 
        echo '<div class="uk-margin-large-left login-btn-div">
        <a href="'.$root_path.'login/" class="uk-button uk-button-default login-btn">ورود / ثبت نام</a> 
    </div>';
    }
    echo '</nav>';
}

function add_home_navbar_en($root_path,$user_login) {
    echo '<nav class="uk-navbar-container uk-margin" uk-navbar="mode: click">
                <div class="uk-navbar-left">
                    <a class="uk-navbar-item uk-logo" href="'.$root_path.'">Easy Exchange</a>
                </div>';                
    echo '<div class="uk-navbar-right">
    <a class="uk-navbar-item uk-margin-left" href="'.$root_path.'"><img class="logo-img" src="'.$root_path.'asset/img/esaraafi_logo.png"></a>
    <div class="menu-3line">
        <a href="#offcanvas-nav" uk-toggle uk-icon="icon: menu;ratio: 2"></a> 
    </div>
    <div id="offcanvas-nav" uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar">
        <ul class="uk-nav uk-nav-default">
        <li class="uk-nav-header"><a href="'.$root_path.'"><img src="'.$root_path.'asset/img/toolbar/home.png" alt="home">Home</li>';
    if(!$user_login) {
        echo '<li class="uk-nav-header">
              <a href="'.$root_path.'login/"><img src="'.$root_path.'asset/img/toolbar/signin.png" alt="signin">Signin</a>   
                  </li>
                  <li class="uk-nav-header">
                    <a href="'.$root_path.'signup/"><img src="'.$root_path.'asset/img/toolbar/signup.png" alt="signup">Signup</a>   
                  </li>';
    } else {
        echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/"><img src="'.$root_path.'asset/img/toolbar/dashboard.png" alt="dashboard">Dashboard</a>   
                  </li>';        
    }
    echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'terms/"><img src="'.$root_path.'asset/img/toolbar/rules.png" alt="rules">Terms</a>   
                  </li>
                  <li class="uk-nav-header">
                    <a href="'.$root_path.'faq/"><img src="'.$root_path.'asset/img/toolbar/faq.png" alt="faq">FAQ</a>   
                  </li>';
    if($user_login) {
        echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'logout/"><img src="'.$root_path.'asset/img/toolbar/logout.png" alt="logout">Logout</a>   
               </li>';    
    }
    echo '</ul>
        </div>
    </div> 
    </nav>';
}

function add_home_toolbar($root_path,$user_login) {
    echo '<div class="uk-form-stacked uk-grid-small uk-flex-around uk-child-width-1-1 uk-child-width-1-@s" uk-grid="">
            <div class="uk-width-1-1@m card-toolbar-home">
                <div class="uk-card-body uk-flex uk-flex-row uk-flex-around card-toolbar-body">
                  <div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'"><p>خانه</p></a>   
                  </div>';
    if(!$user_login) {
        echo '<div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'login/"><p>ناحیه کاربری</p></a>   
                  </div>';
    } else {
        echo '<div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'dashboard/"><p>ناحیه کاربری</p></a>   
                  </div>';        
    }
    echo '<div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'blog/"><p>مقالات</p></a>   
                  </div>
                  <div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'terms/"><p>قوانین</p></a>   
                  </div>
                  <div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'faq/"><p>سوالات متداول</p></a>   
                  </div>
                  <div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'help/"><p>راهنما</p></a>   
                  </div>
                  <div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'aboutus/"><p>درباره ما</p></a>   
                  </div>
                  <div class="uk-text-center menu-item-div">
                    <a href="'.$root_path.'contact/"><p>تماس با ما</p></a>   
                  </div>';    
    echo '</div>    
            </div>          
        </div>';
}

function add_home_toolbar_en($root_path,$user_login) {
    echo '<div class="uk-form-stacked uk-grid-small uk-flex-around uk-child-width-1-1 uk-child-width-1-@s" uk-grid="">
            <div class="uk-card uk-card-default uk-width-1-1@m card-toolbar-home">
                <div class="uk-card-body uk-flex uk-flex-row uk-flex-around card-toolbar-body">
                  <div class="uk-text-center">
                    <a href="'.$root_path.'"><img src="'.$root_path.'asset/img/toolbar/home.png" alt="home"><p>Home</p></a>   
                  </div>';
    if(!$user_login) {
        echo '<div class="uk-text-center">
                    <a href="'.$root_path.'login/"><img src="'.$root_path.'asset/img/toolbar/signin.png" alt="signin"><p>Signin</p></a>   
                  </div>
                  <div class="uk-text-center">
                    <a href="'.$root_path.'signup/"><img src="'.$root_path.'asset/img/toolbar/signup.png" alt="signup"><p>Signup</p></a>   
                  </div>';
    } else {
        echo '<div class="uk-text-center">
                    <a href="'.$root_path.'dashboard/"><img src="'.$root_path.'asset/img/toolbar/dashboard.png" alt="dashboard"><p>Dashboard</p></a>   
                  </div>';        
    }
    echo '<div class="uk-text-center">
                    <a href="'.$root_path.'terms/"><img src="'.$root_path.'asset/img/toolbar/rules.png" alt="rules"><p>Terms</p></a>   
                  </div>
                  <div class="uk-text-center">
                    <a href="'.$root_path.'faq/"><img src="'.$root_path.'asset/img/toolbar/faq.png" alt="faq"><p>FAQ</p></a>   
                  </div>';
    if($user_login) {
        echo '<div class="uk-text-center">
                    <a href="'.$root_path.'logout/"><img src="'.$root_path.'asset/img/toolbar/logout.png" alt="logout"><p>Logout</p></a>   
               </div>';    
    }
    echo '<div class="uk-text-center uk-flex uk-flex-column">
                    <img class="lang-img-en" src="'.$root_path.'asset/img/toolbar/language.png" alt="logout">
                    <select id="language" class="uk-select lang-select">
                    <option value="1">فارسی</option>
                    <option value="2" selected>English</option>
                    </select>
               </div>
               </div>
            </div>
        </div>';
}

function add_dashboard_navbar($root_path,$username) {    
    echo '<nav class="uk-navbar-container uk-margin" uk-navbar>
                <div class="uk-navbar-right">
                    <a class="uk-navbar-item uk-margin-left" href="'.$root_path.'"><img class="logo-img"  src="'.$root_path.'asset/img/esaraafi_logo.png"></a>
                </div>';    
    echo '<div class="uk-navbar-left">    
    <div class="menu-3line">
        <a href="#offcanvas-nav" uk-toggle uk-icon="icon: menu;ratio: 2"></a> 
    </div>
    <div id="offcanvas-nav" uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar">
        <ul class="uk-nav uk-nav-default">
        <li class="uk-nav-header"><a href="'.$root_path.'"><img src="'.$root_path.'asset/img/toolbar/home.png" alt="home">خانه</li>';
    echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/"><img src="'.$root_path.'asset/img/toolbar/dashboard.png" alt="dashboard">داشبورد</a>   
                  </li>
        <li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/identity/index.php?p=identity"><img src="'.$root_path.'asset/img/toolbar/identity.png" alt="identity">احراز هویت</a>   
         </li>
         <li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/identity/index.php?p=bank"><img src="'.$root_path.'asset/img/toolbar/banking.png" alt="banking">تنظیمات بانکی
                    </a>
        </li>
         <li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/identity/"><img src="'.$root_path.'asset/img/toolbar/security.png" alt="banking">تنظیمات امنیتی
                    </a>
        </li>
        <li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/wallet/"><img src="'.$root_path.'asset/img/toolbar/wallet.png" alt="wallet">کیف پول
                    </a>
        </li>
         <li class="uk-nav-header">
                    <a href="'.$root_path.'dashboard/support/"><img src="'.$root_path.'asset/img/toolbar/support.png" alt="wallet">پشتیبانی
                    </a>
        </li>';
    
    
    echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'terms/"><img src="'.$root_path.'asset/img/toolbar/rules.png" alt="rules">قوانین</a>   
                  </li>
                  <li class="uk-nav-header">
                    <a href="'.$root_path.'faq/"><img src="'.$root_path.'asset/img/toolbar/faq.png" alt="faq">سوالات متداول</a>   
                  </li>';    
    echo '<li class="uk-nav-header">
                    <a href="'.$root_path.'logout/"><img src="'.$root_path.'asset/img/toolbar/logout.png" alt="logout">خروج</a>   
               </li>';    
   
    echo '</ul>
        </div>
    </div> 
     <div class="uk-margin-large-left">
        <button class="uk-button uk-button-default profile-btn"><img class="profile-img" src="'.$root_path.'asset/img/unknown.png"></button>
        <div class="profile-dropdown" uk-dropdown="mode: click">
          <ul class="uk-nav uk-dropdown-nav">
            <li class="uk-active uk-text-center menu-item"><span>'.$username.'</span></li>
            
            <li class="uk-nav-divider"></li>
            <li><a class="menu-item" href="'.$root_path.'dashboard/identity/">پروفایل</a></li>
            <li><a class="menu-item" href="'.$root_path.'dashboard/wallet/">کیف پول</a></li>
            <li class="uk-nav-divider"></li>
            <li><a class="menu-item" href="'.$root_path.'logout/">خروج </a></li>
          </ul>        
        </div>
    </div>
          </nav>';
}

function add_dashboard_toolbar($root_path) {
    echo '<div class="uk-form-stacked uk-grid-small uk-flex-around uk-child-width-1-1 uk-child-width-1-@s" uk-grid="">
            <div class="uk-width-1-1@m card-toolbar-dashboard">
                <div class="uk-card-body uk-flex uk-flex-row uk-flex-around card-toolbar-body">
                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'">
                            <p>خانه</p>
                        </a>
                    </div>

                   <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'dashboard/">
                            <p>داشبورد</p>
                        </a>
                    </div>
                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'dashboard/identity/index.php?p=identity">
                            <p>احراز هویت</p>
                        </a>
                    </div>

                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'dashboard/identity/index.php?p=bank">
                            <p>تنظیمات بانکی</p>
                        </a>
                    </div>
                    
                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'dashboard/identity/">
                            <p>تنظیمات امنیتی</p>
                        </a>
                    </div>
                    
                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'dashboard/wallet/">
                            <p>کیف پول</p>
                        </a>
                    </div>
                    
                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'dashboard/support/">
                            <p>پشتیبانی</p>
                        </a>
                    </div>

                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'terms/">
                            <p>قوانین</p>
                        </a>
                    </div>
                    <div class="uk-text-center menu-item-div">
                        <a href="'.$root_path.'faq/">
                            <p>سوالات متداول</p>
                        </a>
                    </div>                    
                </div>
            </div>
        </div>';
}

function add_sidebar($path,$active){
    echo '<div id="mySidenav" class="sidenav">
            <ul class="uk-nav uk-nav-default">
                <li class="li_home">
                    <a class="sidenav_a" href="'.$path.'">
                   <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-home"></i></span>
                   <span class="sp_tag">Home</span>
                  </a>
                </li>
                <li class="li_home ';
    if($active === "dashboard")
        echo 'active';
    echo '">
           <a id="dashboard" class="sidenav_a" href="'.$path.'dashboard/">
                  <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-tachometer-alt"></i></i></span>
                  <span class="sp_tag">Dashboard</span>
                 </a>
                </li>

                <li class="li_home ';
    if($active === "network")
        echo 'active';
    echo '"> 
            <a id="network" class="sidenav_a" href="'.$path.'dashboard/network/">
                <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-network-wired"></i></span>
                <span class="sp_tag">Network</span>
                </a>
                </li>

                <li class="li_home ';
    if($active === "financial")
        echo 'active';
    echo '"> 
            <a id="financial" class="sidenav_a" href="'.$path.'dashboard/finance/">
                <span class="uk-margin-small-right" name="side_icon"><i class="far fa-money-bill-alt"></i></span>
                <span class="sp_tag">Financial</span>
                </a>
                </li>

                <li class="li_home ';
    if($active === "faq")
        echo 'active';
    echo '">
            <a class="sidenav_a" href="'.$path.'dashboard/faq/">
                <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-question"></i></span>
                <span class="sp_tag" style="margin-left: 18px;">FAQ</span>
                </a>
                </li>

                <li class="li_home ';
    if($active === "support")
        echo 'active';
    echo '">    
            <a class="sidenav_a" href="'.$path.'dashboard/support/">
                <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-life-ring"></i></span>
                <span class="sp_tag" style="margin-left: 11px;">Support</span>
                </a>
                </li>


                <li class="li_home ';
    if($active === "setting")
        echo 'active';
    echo '"> 
                <a class="sidenav_a" href="'.$path.'dashboard/setting/">
                <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-cog"></i></span>
                <span class="sp_tag" style="margin-left: 11px;">Setting</span>
                </a>
                </li>

                <li class="li_home ';
    if($active === "logout")
        echo 'active';
    echo '">
                    <a class="sidenav_a" href="'.$path.'include/logout.php">
                <span class="uk-margin-small-right" name="side_icon"><i class="fas fa-power-off"></i></span>
                <span class="sp_tag" style="margin-left: 11px;">Logout</span>
                </a>
                </li>                
                <li class="uk-nav-divider"></li>
            </ul>
        </div>';
    
}

function add_sidebar2($path,$active) {
    $new_ticket_count = get_new_ticket_count($_SESSION['user_id']);
    echo  '<div id="mySidenav" class="sidenav">
    <img src="'.$path.'asset/img/logo-dashboard.png" class="logo-dashboard">
       <ul class="uk-nav-default uk-nav-parent-icon rhf_ul" uk-nav>
                    <li><a href="'.$path.'"><span class="uk-margin-small-right" uk-icon="icon: home"></span>Home</a></li>
                    <li class="uk-nav-divider"></li>
                    <li ';
    if($active === "dashboard")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'dashboard/"><span class="uk-margin-small-right" uk-icon="icon: thumbnails"></span>Dashboard</a></li>
                    <li class="uk-parent';
    if($active === "financial")
        echo '  uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon:  credit-card"></span>Financial</a>
         <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'dashboard/finance/">Invensment</a></li>
              <li><a href="'.$path.'dashboard/history/">History</a></li>
              <li><a href="'.$path.'dashboard/withdrawal/">Withdrawal</a></li>
         </ul>
    
    </li>                    
                    <li class="uk-parent';
    if($active === "network")
        echo ' uk-active"';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon: server"></span>Network</a>
        <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'dashboard/network/">Binary</a></li>
              <li><a href="'.$path.'dashboard/unilevel/">Unilevel</a></li>
              <li><a href="'.$path.'dashboard/nominees/">My nominees</a></li>
         </ul>
    </li>
    <li ';
    if($active === "faq")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'dashboard/faq/"><span class="uk-margin-small-right" uk-icon="icon: question"></span>FAQ</a></li>
                    <li ';
    if($active === "support")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'dashboard/support/"><span class="uk-margin-small-right" uk-icon="icon: mail"></span>Support';
    if($new_ticket_count > 0) {
        echo '&nbsp;&nbsp;&nbsp;<span id="new_ticket_counter" class="new-ticket-counter">&nbsp;'.$new_ticket_count.'&nbsp;</span>';
    }
    echo '</a></li>
                    <li class="uk-parent';
    if($active === "setting")
        echo ' uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon: settings"></span>Setting</a>
           <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'dashboard/profile/">Profile</a></li>
              <li><a href="'.$path.'dashboard/wallet/">Wallet</a></li>              
         </ul>
    
    
    
    </li>
                    <li class="uk-nav-divider"></li>
                    <li><a href="'.$path.'include/logout.php"><span class="uk-margin-small-right" uk-icon="icon:  sign-out"></span> Logout</a></li>
                </ul>
        </div>';
}

function add_admin_sidebar($path,$active) {
    $new_ticket_count = get_new_ticket_count_for_admin();
    echo  '<div id="mySidenav" class="sidenav">
       <ul class="uk-nav-default uk-nav-parent-icon rhf_ul" uk-nav>
                    <li><a href="'.$path.'"><span class="uk-margin-small-right" uk-icon="icon: home"></span>Home</a></li>
                    <li class="uk-nav-divider"></li>
                    <li ';
    if($active === "dashboard")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'admin-panel/"><span class="uk-margin-small-right" uk-icon="icon: thumbnails"></span>Dashboard</a></li>
                    <li class="uk-parent';
    if($active === "financial")
        echo '  uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon:  credit-card"></span>Financial</a>
         <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'admin-panel/finance/">Invensment</a></li>
              <li><a href="'.$path.'admin-panel/history/">History</a></li>
              <li><a href="'.$path.'admin-panel/withdrawal/">Withdrawal</a></li>
              <li><a href="'.$path.'admin-panel/profit/">Profit</a></li>
         </ul>
    
    </li>                    
                    <li ';
    if($active === "network")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'admin-panel/network/"><span class="uk-margin-small-right" uk-icon="icon: server"></span>Network</a></li>
                   <li ';
    if($active === "support")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'admin-panel/support/"><span class="uk-margin-small-right" uk-icon="icon: mail"></span>Support';
    if($new_ticket_count > 0) {
        echo '&nbsp;&nbsp;&nbsp;<span id="new_ticket_counter" class="new-ticket-counter">&nbsp;'.$new_ticket_count.'&nbsp;</span>';
    }
    echo '</a></li>
                    <li class="uk-parent';
    if($active === "setting")
        echo ' uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon: settings"></span>Setting</a>
           <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'admin-panel/profile/">Profile</a></li>
              <li><a href="'.$path.'admin-panel/system/">System</a></li>
              <li><a href="'.$path.'admin-panel/wallet/">Wallet</a></li>              
         </ul>
    </li>
                    <li class="uk-nav-divider"></li>
                    <li><a href="'.$path.'include/logout.php"><span class="uk-margin-small-right" uk-icon="icon:  sign-out"></span> Logout</a></li>
                </ul>
        </div>';
}

function add_navbar($path,$active) {
    $new_ticket_count = get_new_ticket_count($_SESSION['user_id']);
    echo '<nav class="uk-navbar-container navbar" uk-navbar>
            <div class="uk-navbar-left">
                <ul class="uk-navbar-nav">
                    <li class="logo_nav">                    
                        <span>Welcome <strong>'.$_SESSION['farzin_username'].'</strong></span>
                    </li>
                </ul>
                <a href="#" class="uk-navbar-toggle uk-hidden@s" uk-navbar-toggle-icon uk-toggle="target: #sidenav"></a>
            </div>            
        </nav>
        <div id="sidenav" uk-offcanvas="flip: true" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
    <img src="'.$path.'asset/img/logo-dashboard.png" class="logo-dashboard">
        <ul class="uk-nav-default uk-nav-parent-icon rhf_ul" uk-nav>
                    <li><a href="'.$path.'"><span class="uk-margin-small-right" uk-icon="icon: home"></span>Home</a></li>
                    <li class="uk-nav-divider"></li>
                    <li ';
    if($active === "dashboard")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'dashboard/"><span class="uk-margin-small-right" uk-icon="icon: thumbnails"></span>Dashboard</a></li>
                    <li class="uk-parent';
    if($active === "financial")
        echo '  uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon:  credit-card"></span>Financial</a>
         <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'dashboard/finance/">Invensment</a></li>
              <li><a href="'.$path.'dashboard/history/">History</a></li>
              <li><a href="'.$path.'dashboard/withdrawal/">Withdrawal</a></li>
         </ul>
    
    </li>                    
                    <li class="uk-parent';
    if($active === "network")
        echo ' uk-active"';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon: server"></span>Network</a>
    <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'dashboard/network/">Binary</a></li>
              <li><a href="'.$path.'dashboard/unilevel/">Unilevel</a></li>
              <li><a href="'.$path.'dashboard/nominees/">My nominees</a></li>
         </ul>
    
    
    </li>
                    <li ';
    if($active === "faq")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'dashboard/faq/"><span class="uk-margin-small-right" uk-icon="icon: question"></span>FAQ</a></li>
                    <li ';
    if($active === "support")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'dashboard/support/"><span class="uk-margin-small-right" uk-icon="icon: mail"></span>Support</a></li>
                   <li class="uk-parent';
    if($active === "setting")
        echo ' uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon: settings"></span>Setting</a>
           <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'dashboard/profile/">Profile</a></li>
              <li><a href="'.$path.'dashboard/wallet/">Wallet</a></li>              
         </ul>
    </li>
                    <li class="uk-nav-divider"></li>
                    <li><a href="'.$path.'include/logout.php"><span class="uk-margin-small-right" uk-icon="icon:  sign-out"></span> Logout</a></li>
                </ul>
    </div>
</div>';
}

function add_admin_navbar($path,$active) { 
    $new_ticket_count = get_new_ticket_count_for_admin();
    echo '<nav class="uk-navbar-container navbar" uk-navbar>
            <div class="uk-navbar-left">
                <ul class="uk-navbar-nav">
                    <li class="logo_nav">
                        <!-- <img data-src="'.$path.'asset/img/TARINSHOW name.png" alt="" uk-img class="img_logo_nav">-->
                    </li>
                </ul>
                <a href="#" class="uk-navbar-toggle uk-hidden@s" uk-navbar-toggle-icon uk-toggle="target: #sidenav"></a>
            </div>            
        </nav>
        <div id="sidenav" uk-offcanvas="flip: true" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
        <ul class="uk-nav-default uk-nav-parent-icon rhf_ul" uk-nav>
                    <li><a href="'.$path.'"><span class="uk-margin-small-right" uk-icon="icon: home"></span>Home</a></li>
                    <li class="uk-nav-divider"></li>
                    <li ';
    if($active === "dashboard")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'admin-panel/"><span class="uk-margin-small-right" uk-icon="icon: thumbnails"></span>Dashboard</a></li>
                    <li class="uk-parent';
    if($active === "financial")
        echo '  uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon:  credit-card"></span>Financial</a>
         <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'admin-panel/finance/">Invensment</a></li>
              <li><a href="'.$path.'admin-panel/history/">History</a></li>
              <li><a href="'.$path.'admin-panel/withdrawal/">Withdrawal</a></li>
              <li><a href="'.$path.'admin-panel/profit/">Profit</a></li>
         </ul>
    
    </li>                    
                    <li ';
    if($active === "network")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'admin-panel/network/"><span class="uk-margin-small-right" uk-icon="icon: server"></span>Network</a></li>                    
                    <li ';
    if($active === "support")
        echo 'class="uk-active"';
    echo '><a href="'.$path.'admin-panel/support/"><span class="uk-margin-small-right" uk-icon="icon: mail"></span>Support';
    if($new_ticket_count > 0) {
        echo '&nbsp;&nbsp;&nbsp;<span id="new_ticket_counter" class="new-ticket-counter">&nbsp;'.$new_ticket_count.'&nbsp;</span>';
    }
    echo '</a></li>
                   <li class="uk-parent';
    if($active === "setting")
        echo ' uk-active';
    echo '"><a><span class="uk-margin-small-right" uk-icon="icon: settings"></span>Setting</a>
           <ul class="uk-nav-sub" role="menu">
              <li><a href="'.$path.'admin-panel/profile/">Profile</a></li>
              <li><a href="'.$path.'admin-panel/system/">System</a></li>
              <li><a href="'.$path.'admin-panel/wallet/">Wallet</a></li>              
         </ul>
    </li>
                    <li class="uk-nav-divider"></li>
                    <li><a href="'.$path.'include/logout.php"><span class="uk-margin-small-right" uk-icon="icon:  sign-out"></span> Logout</a></li>
                </ul>
    </div>
</div>';
}

function add_info_modal() {
    echo '<div id="info" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-header">
                <h2 id="info_title" class="uk-modal-title order-lable"></h2>
            </div>
            <div class="uk-modal-body">
                <div>
                    <p id="info_msg" class="shabnam elzami"></p>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-center">
                <button class="uk-button uk-modal-close cancel_btn" type="button">بستن</button>
            </div>
        </div>
    </div>';
}

function add_sms_verify_modal($root_path) {
    echo '<div id="modal-sms-verify" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <div class="uk-card uk-padding">
                    <div class="uk-first-column login-box">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                            <div class="uk-text-center">
                                <img class="uk-margin-top" src="'.$root_path.'asset/img/sms.png" width="64">
                                <div class="top_image uk-margin-top">
                                    <h2 class="shabnam elzami">تایید کد ارسالی</h2>
                                </div>
                                <div class="data-input-login">
                                    <div class="uk-flex uk-flex-column">
                                        <p class="calculate-title">لطفا کد ارسالی به شماره همراه خود را در کادر زیر وارد نمایید.</p>
                                        <input type="text" class="uk-input order-select-input direction-ltr input-font uk-text-center" id="sms_code" value="" placeholder="123 456" maxlength="6"> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <button id="submit_sms_verify" type="submit" class="uk-button uk-button-small uk-margin-top pay_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> بررسی صحت کد </button>
                        <br>
                        <div class="alert-danger" role="alert">
                            <span class="shabnam elzami">کد اشتباه است</span>
                        </div>
                        <br><a id="send_sms_again" class="shabnam elzami">ارسال مجدد کد تایید</a>
                        <div class="alert-info" role="alert">
                            <span class="shabnam elzami">کد تایید با موفقیت ارسال گردید</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

function add_ga_2fa_create_modal($root_path,$ga_code) {
    echo '<div id="modal-ga-create" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <div class="uk-card uk-padding">
                    <div class="uk-first-column">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                            <div class="uk-text-center">
                                <img class="uk-margin-top" src="'.$root_path.'asset/img/2fa.jpg" width="64">
                                <div class="uk-margin-top">
                                    <h2 class="shabnam elzami"> فعال سازی Google Authenticator</h2>
                                </div>
                                <div>
                                    <div class="uk-flex uk-flex-column">
                                        <p class="calculate-title">لطفا کد تایید ایجاد شده توسط نرم افزار <strong> Google Authenticator </strong>را وارد نمایید.</p>
                                        <input type="text" class="uk-input order-select-input direction-ltr input-font uk-text-center" id="g_code" value="" placeholder="123 456" maxlength="6">
                                    </div>
                                    <div class="uk-card-footer uk-text-center">
                                        <button id="submit_first_ga_verify" type="submit" class="uk-button uk-button-small uk-margin-top pay_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> بررسی صحت کد </button>
                                        <br>
                                        <div class="alert-danger" role="alert">
                                            <span class="shabnam elzami">کد اشتباه است</span>
                                        </div>';
    if($ga_code == null) { 
        echo '<br>
            <p class="calculate-title">کد QR زیر را با اَپ Google Authenticator گوشی خود اسکن نمایید.</p>
                                <div class="uk-text-center">
                                        <img id="ga_qrcode"  src="" alt="QR Code">
                            </div>
                                        <br><br>
                                        <div class="uk-text-center">
                                            <h3 class="shabnam elzami">اَپ ورود دو مرحله ای Google Authenticator</h3>
                                            <p class="calculate-title">اگر این اَپ را بر روی گوشی خود ندارید می توانید از طریق لینک های زیر نصب نمایید.</p>
                                            <div class="uk-flex uk-flex-row">
                                                <a class="uk-button uk-button-small" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en">
                                                    <img src="'.$root_path.'asset/img/android.png">
                                                </a>
                                                <a class="uk-button uk-button-small" href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">
                                                    <img src="'.$root_path.'asset/img/iphone.png">
                                                </a>
                                            </div>
                                        </div>';
        }
    echo '</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>';
}

function add_ga_check_modal($root_path) {
    echo '<div id="modal-ga-check" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <div class="uk-card uk-padding">
                    <div class="uk-first-column">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                            <div class="uk-text-center">
                                <img class="uk-margin-top" src="'.$root_path.'asset/img/2fa.jpg" width="64">
                                <div class="uk-margin-top">
                                    <h2 class="shabnam elzami"> Google Authenticator</h2>
                                </div>
                                <div>
                                    <div class="uk-flex uk-flex-column">
                                        <p class="calculate-title">لطفا کد تایید ایجاد شده توسط نرم افزار <strong> Google Authenticator </strong>را وارد نمایید.</p>
                                        <input type="text" class="uk-input order-select-input direction-ltr input-font uk-text-center" id="g_code_v" value="" placeholder="123 456" maxlength="6">
                                    </div>
                                    <div class="uk-card-footer uk-text-center">
                                        <button id="submit_ga_verify" type="submit" class="uk-button uk-button-small uk-margin-top pay_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> بررسی صحت کد </button>
                                        <br>
                                        <div class="alert-danger" role="alert">
                                            <span class="shabnam elzami">کد اشتباه است</span>
                                        </div>                                      
                                        <br>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

function add_delete_modal() {
    echo '<div id="del-modal" uk-modal>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h2 class="uk-modal-title order-lable">پیام سیستم</h2>
            </div>
            <div class="uk-modal-body">
                <input id="vid2" hidden type="text" value="">
                <div>
                    <p class="shabnam elzami">آیا از کنسل نمودن این فاکتور مطمئن می باشید؟</p>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-center">
                <button class="uk-button uk-modal-close cancel_btn" type="button">خیر</button>
                <button id="submit_delete_order" class="uk-button uk-modal-close pay_btn" type="button"><i class="fa fa-spinner fa-spin spinner-onload"></i> بله</button>
            </div>
        </div>
    </div>';
}

function add_pay_modal() {
    echo '<div id="pay-modal" uk-modal>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h2 class="uk-modal-title order-lable">پیام سیستم</h2>
            </div>
            <div class="uk-modal-body">
                <input id="pid2" hidden type="text" value="">
                <div class="uk-text-justify">
                    <p class="shabnam uk-icon-navy">آیا از پرداخت این فاکتور مطمئن می باشید؟
                    <br><br><span class="shabnam elzami">توجه:</span>
                    با کلیک بر روی دکمه "بله، پرداخت می نمایم" فاکتور شما مجدد محاسبه می گردد و ممکن است تغییر اندکی در مبلغ آن ایجاد شود.
                    <br><span class="shabnam elzami">توجه:</span>
                    بعد از کلیک بر روی دکمه "بله، پرداخت می نمایم" با توجه به نوع فاکتور و ارز دیجیتال آن به درگاه متناسب منتقل شده و پس از تکمیل پروسه پرداخت مجدد به همین صفحه باز خواهید گشت.این انتقال ممکن است کمی زمان ببرد.لطفاَ صبور باشد.                    
                    </p>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-center">
                <button class="uk-button uk-modal-close cancel_btn" type="button">خیر</button>
                <button id="submit_pay_order" class="uk-button uk-modal-close pay_btn" type="button"><i class="fa fa-spinner fa-spin spinner-onload"></i> بله، پرداخت می نمایم</button>
            </div>
        </div>
    </div>';
}

function add_footer($root_path) {
    echo '<section class="footer-section">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s counter" uk-grid="">
                <div class="uk-card uk-card-default uk-child-width-1-1 uk-child-width-1-1@s">
                    <div class="uk-card-header uk-text-center">
                        <a href="#home" uk-scroll><span uk-icon="chevron-up"></span></a>
                    </div>
                    <div class="uk-card-body footer">
                        <div class="uk-flex uk-flex-row uk-flex-around">
                            <div class="about_div">
                                <p>خدمات ارز دیجیتال آسان یک سامانه تحت وب می باشد که در حوزه خدمات رمزارزها فعالیت می نماید.تلاش این مجموعه در ارائه بهترین نرخ خدمات در سریعترین زمان ممکن می باشد.</p>
                            </div>
                            <div class="uk-text-center">
                                <img class="footer_img" src="'.$root_path.'asset/img/footer-rock.png">

                            </div>
                            <div class="uk-flex uk-flex-column">
                                <ul class="uk-list">
                                    <li><a href="'.$root_path.'">صفحه اصلی</a></li>
                                    <li><a href="'.$root_path.'privacy/">حریم خصوصی</a></li>
                                    <li><a href="'.$root_path.'terms/">قوانین</a></li>
                                    <li><a href="'.$root_path.'faq/">سوالات متداول</a></li>
                                    <li><a href="'.$root_path.'help/">راهنما</a></li>
                                    <li><a href="'.$root_path.'aboutus/">درباره ما</a></li>
                                    <li><a href="'.$root_path.'contact/">تماس با ما</a></li>
                                </ul>
                            </div>

                            <div class="uk-flex uk-flex-column uk-text-center">
                                <label class="footer_fallow-us">ما را در شبکه های اجتماعی دنبال کنید</label>
                                <div class="uk-flex uk-flex-row uk-flex-center">
                                    <a href="https://www.instagram.com/esaraafi/"><img src="'.$root_path.'asset/img/instagram.png" width="32" height="32" alt="instagram"></a>
                                    <a href="https://t.me/esaraafi"><img src="'.$root_path.'asset/img/telegram.png" width="32" height="32" alt="telegram"></a>
                                </div>
                                <br><br><br>
                                <div>
                                <div class="uk-flex uk-flex-row uk-flex-center">
                                <a href="https://www.megastock.ru/" target="_blank"><img src="'.$root_path.'asset/img/wmacept.png"> </a>
                                
<a href="http://passport.wmtransfer.com/asp/certview.asp?wmid=807524534649" target="_blank">
<img src="'.$root_path.'asset/img/wmverified.png" border="0">
</a>

                                </div>
                                </div>

                            </div>


                        </div>

                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <cite>تمامی حقوق مادی و معنوی این سامانه برای خدمات ارز دیجیتال آسان محفوظ می باشد.</cite><br>
                        <cite>خدمات ارز دیجیتال آسان<i class="rights"> ® </i></cite>

                    </div>
                </div>
            </div>
        </section>';
}

function add_footer_en($root_path) {
    echo '<section class="footer-section">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s counter" uk-grid="">
                <div class="uk-card uk-card-default uk-child-width-1-1 uk-child-width-1-1@s">
                    <div class="uk-card-header uk-text-center">
                        <a href="#home" uk-scroll><span uk-icon="chevron-up"></span></a>
                    </div>
                    <div class="uk-card-body footer">
                        <div class="uk-flex uk-flex-row uk-flex-around">
                            <div class="about_div">
                                <p>Easy Exchange is a digital currency exchange that operates in the field of exchange of cryptocurrency.Our goal is provide the best transfer rate in the fastest time possible.</p>
                            </div>
                            <div class="uk-text-center">
                                <img class="footer_img" src="'.$root_path.'asset/img/footer-rock.png">

                            </div>
                            <div class="uk-flex uk-flex-column">
                                <ul class="uk-list">
                                    <li><a href="'.$root_path.'">Home</a></li>
                                    <li><a href="'.$root_path.'privacy/">Privacy</a></li>
                                    <li><a href="'.$root_path.'terms/">Terms and Conditions</a></li>
                                    <li><a href="'.$root_path.'faq/">FAQ</a></li>
                                    <li><a href="'.$root_path.'help/">Help</a></li>
                                    <li><a href="'.$root_path.'aboutus/">About us</a></li>
                                    <li><a href="'.$root_path.'contact/">Contact</a></li>
                                </ul>
                            </div>

                            <div class="uk-flex uk-flex-column uk-text-center">
                                <label class="footer_fallow-us">Fallow us on social media</label>
                                <div class="uk-flex uk-flex-row uk-flex-center">
                                    <a href="https://www.instagram.com/esaraafi/"><img src="'.$root_path.'asset/img/instagram.png" width="32" height="32" alt="instagram"></a>
                                    <a href="https://t.me/esaraafi"><img src="'.$root_path.'asset/img/telegram.png" width="32" height="32" alt="telegram"></a>
                                </div>
                                <br><br><br>
                                <div>
                                <div class="uk-flex uk-flex-row uk-flex-center">
                                <a href="https://www.megastock.ru/" target="_blank"><img src="'.$root_path.'asset/img/wmacept.png"> </a>
                                <!-- webmoney attestation label#21FC2A50-D6CD-4B8D-A227-BF381CD222F7 begin -->
<a href="http://passport.wmtransfer.com/asp/certview.asp?wmid=807524534649" target="_blank">
<img src="'.$root_path.'asset/img/wmverified.png">
</a>
<!-- webmoney attestation label#21FC2A50-D6CD-4B8D-A227-BF381CD222F7 end -->
                                </div>
                                </div>

                            </div>


                        </div>

                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <cite>All right recieved.Copyright 2020</cite><br>
                        <cite>Easy Exchange<i class="rights"> ® </i></cite>

                    </div>
                </div>
            </div>
        </section>';
}

function reset_password_modal() {
    echo '<div id="modal-sms-verify_rp" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <div class="uk-card uk-padding">
                    <div class="uk-first-column login-box">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                            <div class="uk-text-center">
                                <div class="top_image uk-margin-top">
                                    <h2 class="shabnam elzami">بازیابی کلمه عبور</h2>
                                </div>
                                <div id="enter">
                                    <div class="row">
                                        <div><label class="uk-form-label shabnam">شماره همراه:</label>
                                            <div class="uk-form-controls amount-div">
                                                <input id="mobile_rp" class="uk-input" type="text" lang="en" style="direction: ltr;" value="">
                                            </div>
                                            <span id="mobile_rp_err" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                     <br>
                                    <div class="row">
                                        <div><label class="uk-form-label shabnam">آدرس ایمیل:</label>
                                            <div class="uk-form-controls amount-div">
                                                <input id="email_rp" class="uk-input" type="text" lang="en" style="direction: ltr;" value="">
                                            </div>
                                            <span id="email_rp_err" class="shabnam elzami"></span>
                                        </div>
                                    </div>
                                    <br>
                                    <button id="send_sms_rp" type="submit" class="uk-button uk-button-small uk-margin-top pay_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> ارسال کد تایید </button>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

function add_sms_verify_reset_modal($root_path) {
    echo '<div id="modal-sms-verify_reset" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <div class="uk-card uk-padding">
                    <div class="uk-first-column login-box">
                        <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-1-1@s" uk-grid="">
                            <div class="uk-text-center">
                                <img class="uk-margin-top" src="'.$root_path.'asset/img/sms.png" width="64">
                                <div class="top_image uk-margin-top">
                                    <h2 class="shabnam elzami">تایید کد ارسالی</h2>
                                </div>
                                <div class="data-input-login">
                                    <div class="uk-flex uk-flex-column">
                                        <p class="calculate-title">لطفا کد ارسالی به شماره همراه خود را در کادر زیر وارد نمایید.</p>
                                        <input type="text" class="uk-input order-select-input direction-ltr input-font uk-text-center" id="sms_code_rp" value="" placeholder="123 456" maxlength="6"> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-footer uk-text-center">
                        <button id="submit_sms_verify_rp" type="submit" class="uk-button uk-button-small uk-margin-top pay_btn"><i class="fa fa-spinner fa-spin spinner-onload"></i> بررسی صحت کد </button>
                        <br>
                        <div class="alert-danger" role="alert">
                            <span class="shabnam elzami">کد اشتباه است</span>
                        </div>
                        <br>
                        <div class="alert-info" role="alert">
                            <span class="shabnam elzami">کد تایید با موفقیت ارسال گردید</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

?>