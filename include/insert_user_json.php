<?php
session_start();
header('Content-Type: application/json');

include_once('global.php');
include_once('bitex.php');
include_once('jdf.php');
include_once('mail.php');

$aResult = array();

if( !isset($_POST['arguments']) ) {
    $aResult['error'] = 'No function arguments!';
}


if( !isset($aResult['error']) ) {
    if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
        $aResult['error'] = 'Error in arguments!';
    }
    else {
        $email = validate_input($_POST['arguments'][0]);
        $password = validate_input($_POST['arguments'][1]);
        //$username = validate_input($_POST['arguments'][2]);
        $refer = validate_input($_POST['arguments'][2]);
        
        if(strlen($email) >= 80)
            $email = substr($email,0,80);
        if(strlen($password) >= 32)
            $password = substr($password,0,32);        
       
        $datas = array();        
        $datas['gender'] = '0';
        if($email === "0")
            $email = null;
        $datas['email'] = $email;
        $datas['username'] = $email;
        $datas['password'] = password_hash($password, PASSWORD_DEFAULT);
        
        $datas['create_date'] = jdate('Y-m-d','','','','en');
        $datas['create_time'] = jdate('H:i:s','','','','en');
        $datas['access_level_id'] = "1";
        $datas['status'] = "1";        
        $datas['email_confirm'] = "0";       
        $confirm_code = random_code(32);
        $refer_code_left = "";
        $refer_code_right = "";
        while($refer_code_left == ""){
            $refer_code_left = random_code(12);
            if(!is_refer_code_unique($refer_code_left))
                $refer_code_left = "";           
        }
        while($refer_code_right == ""){
            $refer_code_right = random_code(12);
            if(!is_refer_code_unique($refer_code_right))
                $refer_code_right = "";          
        }
        $datas['refer_code_left'] = $refer_code_left; 
        $datas['refer_code_right'] = $refer_code_right; 
        $datas['email_confirm_code'] = $confirm_code;
        $datas['ip_address'] = get_user_ip();
        
        /*$nurl = $url.'accounts/confirm-email/index.php?cc='.$confirm_code;
        $msg_body = confirm_email_body($nurl,$username,null,$confirm_code);
        $mail_result = send_mail($no_reply_mail_address,$email,'Confirm email address please',$msg_body);*/
        
        
        $last_userid = insert_new_user($datas);
        if ($last_userid > 0) {
           
            $_SESSION['bitex_sessionid'] = md5($last_userid);
            $_SESSION['user_id'] = $last_userid;
            $_SESSION['access_level_id'] = "1";
            $_SESSION['bitex_username'] = $email;
            $_SESSION['ip_address'] = $datas['ip_address'];
            insert_user_action($_SESSION['ip_address'],$_SESSION['user_id'],'ثبت نام',$email);
            $res = send_mail($no_reply_mail_address,$to_mail,$no_reply_mail_address,$no_reply_mail_password,'New user','userid: '.$_SESSION['user_id'].' username: '.$_SESSION['bitex_username'],null);
            $aResult['ok'] = "ok";
            
            //$wallet_id = create_default_wallet_for_user($last_userid,null,$username,null);
            /*$signup_bonus = get_signup_bonus();
            if($signup_bonus > 0) {
                $pay_info = array();
                $pay_info['user_id'] = $last_userid;
                $pay_info['wallet_in'] = $signup_bonus;
                $pay_info['wallet_out'] = "0";
                $pay_info['act_date'] = date('Y-m-d');
                $pay_info['act_time'] = date('H:i:s');
                $pay_info['babat_id'] = "2";
                $pay_info['description'] = "Signup Bonus";
                $pay_info['LMI_PAYMENT_NO'] = $LMI_PAYMENT_NO;
                $pay_info['LMI_SYS_INVS_NO'] = $LMI_SYS_INVS_NO;
                $pay_info['LMI_SYS_TRANS_NO'] = $LMI_SYS_TRANS_NO;
                $pay_info['LMI_SYS_TRANS_DATE'] = $LMI_SYS_TRANS_DATE;
                $transaction_id = insert_transaction($tp_info);
            }*/
            
/*            if($refer != "0"){                
                $ref_user_id = get_user_id_by_left_refer_code($refer);
                if($ref_user_id === "-1")
                    $ref_user_id = get_user_id_by_right_refer_code($refer);                            
                if($ref_user_id !== "-1"){
                    $orginal_parent_info = get_user_info_by_id($ref_user_id);
                    $binary_setting = $orginal_parent_info[0]['binary_setting'];
                    if($binary_setting === "1" || $binary_setting === "2") { //insert to left or right teaam
                        $last_parents = get_last_refer_id($orginal_parent_info[0]['id'],$binary_setting);
                        if(count($last_parents) > 0)  {
                            $ref_datas = array();
                            $ref_datas['orginal_parent_id'] = $orginal_parent_info[0]['id'];
                            $ref_datas['parent_user_id'] = $last_parents[0]['refer_user_id'];
                            $ref_datas['refer_user_id'] = $last_userid;
                            $ref_datas['left_right'] = $binary_setting;
                            $ref_datas['level'] = (string)((int)$last_parents[0]['level'] + 1);
                            $id_refer = insert_refer_user($ref_datas);                            
                        } else {
                            if(is_team_empty($ref_user_id,$binary_setting)) {
                                $ref_datas = array();
                                $ref_datas['orginal_parent_id'] = $orginal_parent_info[0]['id'];
                                $ref_datas['parent_user_id'] = $orginal_parent_info[0]['id'];
                                $ref_datas['refer_user_id'] = $last_userid;
                                $ref_datas['left_right'] = $binary_setting;
                                $ref_datas['level'] = "1";
                                $id_refer = insert_refer_user($ref_datas);                                
                            } else {
                                $orginal_parent_id = get_orginal_parent_id_by_ref_user_id($ref_user_id);
                                $last_parents = get_last_refer_id($orginal_parent_id,$binary_setting);
                                $ref_datas = array();
                                $ref_datas['orginal_parent_id'] = $ref_user_id;
                                $ref_datas['parent_user_id'] = $last_parents[0]['refer_user_id'];
                                $ref_datas['refer_user_id'] = $last_userid;
                                $ref_datas['left_right'] = $binary_setting;
                                $ref_datas['level'] = (string)((int)$last_parents[0]['level'] + 1);
                                $id_refer = insert_refer_user($ref_datas);
                            }
                            
                        }
                    }
                    if($binary_setting === "3") {
                        $ref_user_id_left = get_user_id_by_left_refer_code($refer);
                        $ref_user_id_right = get_user_id_by_right_refer_code($refer);
                        if($ref_user_id_left !== "-1") {
                            $last_parents = get_last_refer_id($orginal_parent_info[0]['id'],"1");
                            if(count($last_parents) > 0) {
                                $ref_datas = array();
                                $ref_datas['orginal_parent_id'] = $orginal_parent_info[0]['id'];
                                $ref_datas['parent_user_id'] = $last_parents[0]['refer_user_id'];
                                $ref_datas['refer_user_id'] = $last_userid;
                                $ref_datas['left_right'] = "1";
                                $ref_datas['level'] = (string)((int)$last_parents[0]['level'] + 1);
                                $id_refer = insert_refer_user($ref_datas);                            
                            } else {
                                if(is_team_empty($ref_user_id,"1")) {
                                    $ref_datas = array();
                                    $ref_datas['orginal_parent_id'] = $orginal_parent_info[0]['id'];
                                    $ref_datas['parent_user_id'] = $orginal_parent_info[0]['id'];
                                    $ref_datas['refer_user_id'] = $last_userid;
                                    $ref_datas['left_right'] = "1";
                                    $ref_datas['level'] = "1";
                                    $id_refer = insert_refer_user($ref_datas);                                    
                                } else {
                                    $orginal_parent_id = get_orginal_parent_id_by_ref_user_id($ref_user_id);
                                    $last_parents = get_last_refer_id($orginal_parent_id,"1");
                                    $ref_datas = array();
                                    $ref_datas['orginal_parent_id'] = $ref_user_id;
                                    $ref_datas['parent_user_id'] = $last_parents[0]['refer_user_id'];
                                    $ref_datas['refer_user_id'] = $last_userid;
                                    $ref_datas['left_right'] = "1";
                                    $ref_datas['level'] = (string)((int)$last_parents[0]['level'] + 1);
                                    $id_refer = insert_refer_user($ref_datas);
                                }
                                
                            }
                        }
                        if($ref_user_id_right !== "-1") {
                            $last_parents = get_last_refer_id($orginal_parent_info[0]['id'],"2");
                            if(count($last_parents) > 0) {
                                $ref_datas = array();
                                $ref_datas['orginal_parent_id'] = $orginal_parent_info[0]['id'];
                                $ref_datas['parent_user_id'] = $last_parents[0]['refer_user_id'];
                                $ref_datas['refer_user_id'] = $last_userid;
                                $ref_datas['left_right'] = "2";
                                $ref_datas['level'] = (string)((int)$last_parents[0]['level'] + 1);
                                $id_refer = insert_refer_user($ref_datas);                            
                            } else {
                                if(is_team_empty($ref_user_id,"2")) {
                                    $ref_datas = array();
                                    $ref_datas['orginal_parent_id'] = $orginal_parent_info[0]['id'];
                                    $ref_datas['parent_user_id'] = $orginal_parent_info[0]['id'];
                                    $ref_datas['refer_user_id'] = $last_userid;
                                    $ref_datas['left_right'] = "2";
                                    $ref_datas['level'] = "1";
                                    $id_refer = insert_refer_user($ref_datas);                                    
                                } else {
                                    $orginal_parent_id = get_orginal_parent_id_by_ref_user_id($ref_user_id);
                                    $last_parents = get_last_refer_id($orginal_parent_id,"2");
                                    $ref_datas = array();
                                    $ref_datas['orginal_parent_id'] = $ref_user_id;
                                    $ref_datas['parent_user_id'] = $last_parents[0]['refer_user_id'];
                                    $ref_datas['refer_user_id'] = $last_userid;
                                    $ref_datas['left_right'] = "2";
                                    $ref_datas['level'] = (string)((int)$last_parents[0]['level'] + 1);
                                    $id_refer = insert_refer_user($ref_datas);
                                }                                
                            }
                        }
                    }              
                }
                
            }*/
            
        } else {
            $aResult['error'] = 'insert error';           
        }        
    }
}
echo json_encode($aResult);  
?>