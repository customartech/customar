<?php
if (!$security_test) exit;
use PHPMailer\PHPMailer\PHPMailer;

function reg_mail_form($user, $email, $tel, $message)
{
    $reg_mail_form = "<div>
    <center><table align='center' border='0' cellpadding='0' cellspacing='0' height='100%' width='100%'><tr><td align='center' valign='top'>
    <table align='center' border='0' cellpadding='0' cellspacing='0' width='600'><tr><td align='left' valign='top'>
    <table border='0' cellpadding='0' cellspacing='0' width='600'><tr><td valign='top' style='padding-top:9px;padding-bottom:24px'></td>
    </tr></table></td></tr><tr><td align='left' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600'>
    <tr><td valign='top' style='padding-top:9px;padding-bottom:9px'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td valign='top'>
    <table align='left' border='0' cellpadding='0' cellspacing='0' width='600'>
    <tbody><tr><td style='padding-top:9px;padding-left:18px;padding-bottom:9px;padding-right:18px'>
    <table border='0' cellpadding='18' cellspacing='0' width='100%' style='background-color:#ffffff'>
    <tbody><tr><td valign='top' style='font-family:&#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;text-align:left;padding:36px;word-break:break-word'>
    <div style='text-align:left;word-wrap:break-word'>Customar.tech saytından müraciət. 
    <br><br>Ad $user.
    <br><br>Email $email
    <br><br>Telefon $tel
    <br><br>Qeyd $message
    <div style='font-size:0.7em;padding:0px;font-family:&#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;text-align:right;color:#777777;line-height:14px;margin-top:36px'>© 2019 <a href='http://caspian.az' target='_blank' rel='noopener noreferrer'>caspian</a>
    <br>
    </div></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></table></td></tr></table></td></tr></table></center></div>";
    return $reg_mail_form;
}

$successss = "";
$captcha = "";

if (isset($_POST['Submit'])) {
    if(isset($_POST['g-recaptcha-response'])){
        $captcha=$_POST['g-recaptcha-response'];
    }
    if(!GoogleRecaptcha($captcha)){
        $_SESSION['loggedin']=false;
        $RegError = "Bütün bölmələri doldurun";
    }else{    

        if (($_POST['username']) and ($_POST['email']) and ($_POST['message'])) {
            require 'PHPMailer/src/Exception.php';
            require 'PHPMailer/src/PHPMailer.php';
            require 'PHPMailer/src/SMTP.php';
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            try {
                $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                /*$mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'mail.caspian.az';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'info@Customar.az.az';                 // SMTP username
                $mail->Password = 'g[V_(Ls6M=CY';                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587; */                                   // TCP port to connect to

                //$mail->isSendmail();                                   
                $mail->CharSet = 'utf-8';                                   // TCP port to connect to

                //Recipients
                $mail->setFrom('info@customar.tech', 'Customar');
                $mail->addAddress('info@customar.tech', 'Customar');     // Add a recipient

                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Customar əlaqə';
                $mail->Body = reg_mail_form($_POST['username'], $_POST['email'], $_POST['tel'], $_POST['message']);

                $mail->send();
                //echo 'Message has been sent';

                $successss = "Sorğunuz göndərildi.<br>";


            } catch (Exception $e) {
                $RegError = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $RegError = "Bütün bölmələri doldurun";
        }
    }

} else {
    $RegError = "";
}
?>



<html>
<head>
<script src="https://www.google.com/recaptcha/api.js" async defer type="text/javascript"></script>    
</head>
<body>
<article class="ptf-page ptf-page--contact">
    <section>
        <!--Spacer-->
        <div class="ptf-spacer" style=" --ptf-xxl: 10rem; --ptf-md: 5rem;"></div>
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-10">
                    <!--Animated Block-->
                    <div class="ptf-animated-block" data-aos="fade" data-aos-delay="0">
                        <h1 class="large-heading"><?php print $db_link->where("id", 3)->getValue("category", "name_" . $lang); ?></h1>
                        <!--Spacer-->
                        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 2.5rem;"></div>
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-3 twitter" href="#" target="_blank" rel="noopener noreferrer" aria-label="Twitter" title="Twitter"><i class="socicon-twitter"></i></a>
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-3 facebook" href="https://www.facebook.com/customartech/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook"><i class="socicon-facebook"></i></a>
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-3 instagram" href="https://www.instagram.com/customartech/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><i class="socicon-instagram"></i></a>
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-3 pinterest" href="#" target="_blank" rel="noopener noreferrer" aria-label="Pinterest" title="Pinterest"><i class="socicon-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!--Spacer-->
        <div class="ptf-spacer" style=" --ptf-xxl: 6.25rem; --ptf-md: 3.125rem;"></div>
    </section>
    <section>
        <div class="container-xxl">
            <div class="row">
                <div class="col-lg-4">
                    <!--Animated Block-->
                    <div class="ptf-animated-block" data-aos="fade" data-aos-delay="0">
                        <h5 class="fz-14 text-uppercase has-3-color fw-normal"><?php print $qeyd_address; ?></h5>
                        <!--Spacer-->
                        <div class="ptf-spacer" style=" --ptf-xxl: 1.25rem;"></div>
                        <p class="fz-20 lh-1p5 has-black-color"><?php print $recebli; ?></p>
                    </div>
                    <!--Spacer-->
                    <div class="ptf-spacer" style=" --ptf-xxl: 2.1875rem;"></div>
                    <!--Animated Block-->
                    <div class="ptf-animated-block" data-aos="fade" data-aos-delay="100">
                        <h5 class="fz-14 text-uppercase has-3-color fw-normal">Email</h5>
                        <!--Spacer-->
                        <div class="ptf-spacer" style=" --ptf-xxl: 1.25rem;"></div>
                        <p class="fz-20 lh-1p5 has-black-color"><a href="mailto:info@customar.tech">info@customar.tech<br></a></p>
                    </div>
                    <!--Spacer-->
                    <div class="ptf-spacer" style=" --ptf-xxl: 2.1875rem;"></div>
                    <!--Animated Block-->
                    <div class="ptf-animated-block" data-aos="fade" data-aos-delay="200">
                        <h5 class="fz-14 text-uppercase has-3-color fw-normal">Phone</h5>
                        <!--Spacer-->
                        <div class="ptf-spacer" style=" --ptf-xxl: 1.25rem;"></div>
                        <p class="fz-20 lh-1p5 has-black-color"><a href="tel:+994555801188">+99455 580 11 88</a></p>
                    </div>
                    <!--Spacer-->
                    <div class="ptf-spacer" style=" --ptf-lg: 4.375rem; --ptf-md: 2.1875rem;"></div>
                </div>
                <div class="col-lg-8">
                    <?php
                    if ($successss) print "<div class='alert alert-success' role='alert'>$successss</div>";
                    if ($RegError) print "<div class='alert alert-danger' role='alert'>$RegError</div>";
                    ?>
                    <?php
                    if (!$successss) {
                        ?>


                        <!--Animated Block-->
                        <div class="ptf-animated-block" data-aos="fade" data-aos-delay="300">
                            <?php
                            $contact_heading = 'Tell us about your project and goals.';
                            if ($lang === 'az') {
                                $contact_heading = 'Layihəniz və məqsədləriniz haqqında bizə danışın.';
                            } elseif ($lang === 'ru') {
                                $contact_heading = 'Расскажите нам о вашем проекте и целях.';
                            }
                            ?>
                            <h5 class="fz-14 text-uppercase has-3-color fw-normal"><?php print $contact_heading; ?></h5>
                            <!--Spacer-->
                            <div class="ptf-spacer" style=" --ptf-xxl: 3.125rem;"></div>
                            <form action="" method="POST" enctype="multipart/form-data">

                                <div class="ptf-form-group">
                                    <label data-number="01"><?php print $cl_namesurname; ?></label>
                                    <input required class="is-large" type="text" name="username">
                                </div>
                                <div class="ptf-form-group">
                                    <label data-number="02">Email</label>
                                    <input required class="is-large" type="text" name="email">
                                </div>
                                <div class="ptf-form-group">
                                    <label data-number="03"><?php print $movzu; ?></label>
                                    <input required class="is-large" type="text">
                                </div>
                                <div class="ptf-form-group">
                                    <label data-number="04"><?php print $ctext; ?></label>
                                    <textarea required name="message" class="is-large" rows="1"></textarea>
                                </div>                                
                                <!--Spacer-->
                                <div class="ptf-spacer" style=" --ptf-xxl: 2.5rem;"></div>
                                <div class="ptf-form-group">
                                    <label class="ptf-checkbox" for="terms">
                                        <div class="g-recaptcha" data-sitekey="6LdoQQoTAAAAAAgisMNXjhvX_0EUclC8hlipimec"></div>
                                    </label>
                                </div>
                                <!--Spacer-->
                                <div class="ptf-spacer" style=" --ptf-xxl: 5.625rem;"></div>
                                <button  name="Submit" class="ptf-submit-button" value="Submit">
                                    <?php print $csend; ?><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 17 17">
                                        <path d="M16 .997V10h-1V2.703L4.683 13l-.707-.708L14.291 1.997H6.975v-1H16z" />
                                    </svg></button>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <!--Spacer-->
        <div class="ptf-spacer" style=" --ptf-xxl: 10rem; --ptf-md: 5rem;"></div>
    </section>
</article>
</body>
</html>









