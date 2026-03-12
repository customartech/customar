<?php
use PHPMailer\PHPMailer\PHPMailer;
function reg_mail_form($link,$login,$pass) {
    $reg_mail_form="<div>
    <center><table align='center' border='0' cellpadding='0' cellspacing='0' height='100%' width='100%'><tr><td align='center' valign='top'>
    <table align='center' border='0' cellpadding='0' cellspacing='0' width='600'><tr><td align='left' valign='top'>
    <table border='0' cellpadding='0' cellspacing='0' width='600'><tr><td valign='top' style='padding-top:9px;padding-bottom:24px'></td>
    </tr></table></td></tr><tr><td align='left' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600'>
    <tr><td valign='top' style='padding-top:9px;padding-bottom:9px'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td valign='top'>
    <table align='left' border='0' cellpadding='0' cellspacing='0' width='600'>
    <tbody><tr><td style='padding-top:9px;padding-left:18px;padding-bottom:9px;padding-right:18px'>
    <table border='0' cellpadding='18' cellspacing='0' width='100%' style='background-color:#ffffff'>
    <tbody><tr><td valign='top' style='font-family:&#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;text-align:left;padding:36px;word-break:break-word'>
    <div style='text-align:center;margin-bottom:36px'>
    <img align='none' src='http://codelex.az/logo.png' style='width:226px;margin:0' width='226' height='58' alt='codelex'>
    </div><div style='text-align:left;word-wrap:break-word'>codelex-yə qoşulduğunuz üçün təşəkkür edirik. Qeydiyyatı tamamlamaq üçün sizə düzgün məktub göndərməyimizi təsdiq edin.
    <br><br>Təsdiq etmək üçün keçidə basın. Əgər məktub düzgün göndərilməyibsə heç nə etməyin:
    <br><a href='http://".$_SERVER['HTTP_HOST']."/tesdiq_$link' target='_blank' rel='noopener noreferrer'>Aktiv et</a><br>
    <p>İstifadəçi adınız: $login<br>
    <p>Parol: $pass<br>
    <br><br>Təşəkkür edirik!<br>codelex komandası
    <div style='font-size:0.7em;padding:0px;font-family:&#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;text-align:right;color:#777777;line-height:14px;margin-top:36px'>© 2017 <a href='http://codelex' target='_blank' rel='noopener noreferrer'>codelex</a>
    <br>
    </div></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></table></td></tr></table></td></tr></table></center></div>";
    return $reg_mail_form;
}
if($_POST['Submit']) {
    if(($_POST['name'])and($_POST['comp'])and($_POST['email'])and($_POST['password'])){
        if($_POST['password']==$_POST['tpassword']){

            $username = addslashes($_POST['email']);
            $pass = addslashes($_POST['password']);
            $hash = 'lehlulu';
            $pass2encrypt = $hash.$pass;
            $password = md5($pass2encrypt);

            $db_link->where ("email", $username);
            $db_link->where ("pass", $password);
            $userinfo = $db_link->getOne ("qeydiyyat");
            if($db_link->count>0) { 
                $RegError="Bu mail artıq qeydiyyatdan keçib";
            }else{    
                $name=html_entity_decode($_POST['name']);
                $comp=html_entity_decode($_POST['comp']);
                $tel=$_POST['tel'];
                $email=$_POST['email'];
                $pass = $_POST['password'];
                $hash = 'lehlulu';
                $pass2encrypt = $hash.$pass;
                $password = md5($pass2encrypt);
                $aktive_token=md5(yazi(15));
                $update_user_data = array(
                    'ad' => $name,                        
                    'soyad' => $comp,                        
                    'email' => $email, 
                    'pass' => $password,
                    'tel' => $tel,
                    'aktive_token' => $aktive_token,
                    'pass' => $password
                );
                $RegError="Qeydiyyat uğurla tamamlandı";
                $newid=$db_link->insert('qeydiyyat', $update_user_data);
                if($newid){
                    require 'PHPMailer/src/Exception.php';
                    require 'PHPMailer/src/PHPMailer.php';
                    require 'PHPMailer/src/SMTP.php';

                    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
                    try {
                        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                        /*$mail->isSMTP();                                      // Set mailer to use SMTP
                        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        $mail->Username = 'mtarlan@gmail.com';                 // SMTP username
                        $mail->Password = 'Tarlan!(&(Ugur';                           // SMTP password
                        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;  */                                   // TCP port to connect to

                        $mail->isSendmail();                                   
                        $mail->CharSet = 'utf-8';                                   // TCP port to connect to

                        //Recipients
                        $mail->setFrom('mtarlan@gmail.com', 'codelex');
                        $mail->addAddress($email, 'Registration');     // Add a recipient

                        //Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'Registration';
                        $mail->Body = reg_mail_form($aktive_token.".html",$email,$pass);

                        $mail->send();
                        //echo 'Message has been sent';

                        $successss= "E-poçt ünvanınıza təsdiq keçidi olan bir e-poçt göndərdik.<br> Qeydiyyatdan keçmə prosesini başa çatdırmaq üçün təsdiqləmə linkinə vurun.<br>
                        Bir təsdiq e-poçtunu almamışsanız, spam qovluğunuzu yoxlayın.<br> Ayrıca, qeydiyyatdan keçid formunda etibarlı bir e-poçt ünvanı girdiğinizi doğrulayın.<br>
                        Sizə kömək lazımdırsa, bizə müraciət edin.";


                    } catch (Exception $e) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    }        
                }   
                //print '<script>document.location.href="/login.html";</script>'; 
                //exit;
            }    
        }else{
            $RegError="Şifrələri düzgün yazın";
        }

    }else{
        $RegError="Bütün bölmələri doldurun"; 
    }

}else{
    $RegError="";
}

?>
