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
if(isset($_SESSION['bitex_sessionid']) && isset($_SESSION['user_id']) && $_SESSION['bitex_sessionid'] === md5($_SESSION['user_id'])) {
    $user_login = true;    
}

if(!$user_login) {
    header("Location: ".$root_path.'login/');
    exit();
}

if(!isset($_SESSION['access_level_id'])){
    header("Location: ".$root_path.'login/');
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
$wallet_info = array();

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $wallet_address = validate_input($_GET['id']);
    $wallet_info = get_wallet_by_address_user_id($wallet_address,$_SESSION['user_id']);
    if(count($wallet_info)){
        $wallet_id = $wallet_info[0]['id'];
        $transactions = get_wallet_transactions($wallet_id);                  
    }
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>    
    <?php add_asset($root_path); ?>
    <title>تراکنش ها</title>

    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/home.css?version=<?php echo $version; ?>">
    <link rel="stylesheet" href="<?php echo $root_path; ?>asset/css/datatables.min.css">    

    <script src="<?php echo $root_path; ?>asset/js/transactions.js?version=<?php echo $version; ?>"></script>

    <script src="<?php echo $root_path; ?>asset/js/datatables.min.js"></script>    

</head>

<body>
    <div class="uk-container uk-container-expand uk-width-1-1">
        <?php add_dashboard_navbar($root_path,$username); ?>
        <?php add_dashboard_toolbar($root_path); ?>
        <section id="home">
            <div class="uk-form-stacked uk-grid-small uk-child-width-1-1 uk-child-width-2-@s home-content" uk-grid="">
                <div class="uk-card uk-card-default uk-width-1-1@m card-wallet">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-auto">
                                <img width="64" height="64" src="<?php echo $root_path; ?>asset/img/transactions.png">
                            </div>

                            <div class="uk-width-expand">
                                <?php if(count($wallet_info)) { ?>
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تراکنش های <?php echo $wallet_info[0]['alias']; ?></h3>
                                <?php } else { ?>
                                <h3 class="uk-card-title uk-margin-remove-bottom calculate-title">تاریخچه تراکنش ها</h3>
                                <?php } ?>
                                <p class="uk-text-meta uk-margin-remove-top"><span class="tm-date-jalali">
                                        <?php $out=jdate("l,  j F Y"); echo $out; ?></span>
                                </p>
                            </div>
                        </div>


                    </div>
                    <div class="uk-card-body">
                        <?php if(count($wallet_info)) { ?>

                        <table id="orders" class="uk-table uk-table-hover uk-table-striped uk-table-divider">
                            <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>تاریخ</th>
                                    <th>ساعت</th>
                                    <th>مبلغ</th>
                                    <th>مانده</th>
                                    <th>کد تراکنش</th>
                                    <th>توضیحات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $radif = 0; $balance = 0; $sign = ""; foreach($transactions as $row) { $radif++; ?>

                                <?php if($row['bed_bes'] == "1"){
                                            $sign = "+";
                                            $balance = $balance + (int)$row['amount'];
                                      } else {
                                            $sign = "-";
                                            $balance = $balance - (int)$row['amount'];
                                      }
                                ?>
                                <tr <?php if($sign === "+") echo 'class="green"'; else 
                                        echo 'class="red"'; ?>>
                                    <td><?php echo $radif; ?></td>
                                    <td><?php echo $row['tr_date']; ?></td>
                                    <td><?php echo $row['tr_time']; ?></td>
                                    <td><?php echo $row['amount'].$sign; ?></td>
                                    <td><?php echo $balance; ?></td>
                                    <?php if($row['symbol'] === 'BTC' || $row['symbol'] === 'USDT') { ?>
                                        <td><a href="https://www.blockchain.com/btc/tx/<?php echo $row['serial']; ?>" target="_blank"><?php echo $row['serial']; ?></a>
                                        </td> 
                                        <?php } else { ?>
                                        <td><span><?php echo $row['serial']; ?></span>
                                        <?php } ?>  
                                    <td><?php echo $row['description'] ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:left;color: crimson;">موجودی :&nbsp;</th>
                                    <th style="direction: ltr; color: crimson;"><?php echo $balance.' '.$wallet_info[0]['symbol']; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <?php } else { ?>
                        <p class="shabnam">متاسفانه سابقه کیف پول موجود نمی باشد.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>
        <?php add_footer($root_path); ?>
        <?php add_info_modal(); ?>
    </div>
</body>

</html>
