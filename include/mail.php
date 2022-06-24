<?php
include_once("jdf.php");
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($from,$to,$username,$password,$subject,$body,$attachments) {
    //Load Composer's autoloader
    require '../mailer/autoload.php';

    $mail = new PHPMailer(true);                          // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = '';                         // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = '';             // SMTP username
        $mail->Password = '';                     // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('', '');
        $mail->addAddress($to, '');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject =  $subject; //'Here is the subject';
        $mail->Body    =  $body; //'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = $body; //'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return 'Message has been sent';
        } 
    catch (Exception $e) {
        return 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
    }
}

function confirm_code_body($code) {
    $detail = 'کاربر گرامی؛';
    $detail .= '<br>';
    $detail .= 'کد تایید ایمیل شما ';
    $detail .= $code;
    $detail .= ' می باشد.';
    $detail .= '<br>';
    $detail .= 'با تشکر';
    
    $header = 'کد تایید آدرس ایمیل';
    $body = esaraafi_email_body($header,$detail);
    return $body;
}

function send_mail_with_path($from,$to,$username,$password,$subject,$body,$attachments,$path) {
    //Load Composer's autoloader
    require $path.'mailer/autoload.php';

    $mail = new PHPMailer(true);                          // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = '';                         // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = '';             // SMTP username
        $mail->Password = '';                     // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('', '');
        $mail->addAddress($to, '');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject =  $subject; //'Here is the subject';
        $mail->Body    =  $body; //'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = $body; //'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return 'Message has been sent';
        } 
    catch (Exception $e) {
        return 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
    }
}


function esaraafi_email_body($header,$detail) {
    $body = '<body style="margin: 0; padding: 0; direction: rtl">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 10px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                    <tr>
                        <td align="center" bgcolor="#70bbd9" style="padding: 40px 0 30px 0; color: #153643; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif; background-color: #fff;">
                            <img src="https://esaraafi.ir/asset/img/email_logo.png" alt="esaraafi.ir" width="300" height="230" style="display: block;" />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-family: tahoma, sans-serif; font-size: 10px;">
                                        <h1>'.$header.'</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 30px 0; color: #153643; font-family: tahoma, sans-serif; font-size: 16px; line-height: 20px;">'.$detail.'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#080b21" style="padding: 30px 30px 30px 30px;">
                            <div>
                                <div style="color: #ffffff; font-family: tahoma, sans-serif; font-size: 14px; margin-bottom: 20px;" width="75%">
                                    <p>خدمات ارز دیجیتال آسان یک سامانه تحت وب می باشد که در حوزه خدمات رمزارزها فعالیت می نماید.تلاش این مجموعه در ارائه بهترین نرخ خدمات در سریعترین زمان ممکن می باشد.</p>
                                </div>
                                <div style="font-family: tahoma, sans-serif; font-size: 14px;color: #dbde34;text-align: center;">
                                    <label >ما را در شبکه های اجتماعی دنبال کنید</label>
                                    <div style="margin-top: 5px;">
                                        <a href="https://www.instagram.com/esaraafi/"><img src="https://esaraafi.ir/asset/img/instagram.png" width="32" height="32" alt="instagram"></a>
                                        <a href="https://t.me/esaraafi"><img src="https://esaraafi.ir/asset/img/telegram.png" width="32" height="32" alt="telegram"></a>
                                    </div>
                                    <div style="font-family: tahoma, sans-serif; font-size: 14px;color: #fff;text-align: center;margin-top: 10px;">                                        
                                        <cite>خدمات ارز دیجیتال آسان<i> '.jdate("Y").' ® </i></cite>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';
    return $body;
}

function new_ticket_from_user_body($username,$ticket_id,$subject,$catgory,$date,$time,$description){
    return '<p>Ticket Info:</p><br>
    <p><strong>Username:</strong>'.$username.'</p><br>
    <p><strong>Ticket Number:</strong>'.$ticket_id.'</p><br>
    <p><strong>Subject:</strong>'.$subject.'</p><br>
    <p><strong>Category:</strong>'.$catgory.'</p><br>
    <p><strong>Date & Time:</strong>'.$date.' '.$time.'</p><br>
    <p><strong>Ticket Body:</strong>'.$description.'</p><br>
    <p><br>    
    <p>Thanks</p><p>Support Team</p>';
}

function send_mail_for_ticket($from,$to,$subject,$body,$cc,$atachment,$ticket_id) {
    //Load Composer's autoloader
    require '../mailer/autoload.php';

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'esaraafi.ir';                         // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'no-reply@esaraafi.ir';             // SMTP username
        $mail->Password = '6#%v0P(GlWGa';                     // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        //Recipients
        $mail->setFrom('no-reply@esaraafi.ir', 'E-Saraafi');
        foreach($to as $row) {
            $mail->addAddress($row['email'], '');     // Add a recipient        
        }

        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        foreach($cc as $row) {
            $mail->addCC($row['email']);        
        }

        //$mail->addBCC('bcc@example.com');

        //Attachments
        foreach($atachment as $row) {
            $mail->addAttachment('../tickets-files/'.$ticket_id.'/'.$row['file_name']);        
        }
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject =  $subject; //'Here is the subject';
        $mail->Body    =  $body; //'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = $body; //'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return 'Message has been sent';
    } catch (Exception $e) {
        return 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
    }
}


