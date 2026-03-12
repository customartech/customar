<?php
use PHPMailer\PHPMailer\PHPMailer;
function is_logged_in(){
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['project'] == 'admin'){
        return true;
    }else{
        return false;  
    }
}

function chekb_yemek($cid,$category_id,$db_link){
    //$db_link->where ('sub_id', $cid);
    $cat_menyus = $db_link->get('yemekler');
    $cids = explode(",", $category_id);
    foreach ($cat_menyus as $line) {
        print "<div class='checkbox col-lg-3'><label>";
        $id = $line["id"];
        $ceki= $line["ceki"];
        $ad = stripslashes($line["title_az"]);
        if (in_array($id, $cids))
            print "<input name='yemekler".$cid."[]' type='checkbox' value='$id' checked>";
        else
            print "<input name='yemekler".$cid."[]' type='checkbox' value='$id'>";
        print "$ad ($ceki) </label></div>";    
    }
}

function combo_solution($category_id,$db_link){
    $cat_menyus = $db_link->get('services_solution');
    print "<select name='solution' id='select_solution' class='form-control'>";
    print "<option value='0'> - - - - - - - - </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["title_az"]);
        $services_industry_id = $line["services_industry_id"];
        if($category_id==$id)
            print "<option id='$services_industry_id' value='$id' selected='selected'>$ad</option>";
        else
            print "<option id='$services_industry_id' value='$id'>$ad</option>";
    }
    print "</select>";
}
function combo_industry($category_id,$db_link){
    $cat_menyus = $db_link->get('services_industry');
    print "<select name='industry' id='select_industry' class='form-control'>";
    print "<option value='0'> - - - - - - - - </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["title_az"]);
        $services_service_id = $line["services_service_id"];
        if($category_id==$id)
            print "<option id='$services_service_id' value='$id' selected='selected'>$ad</option>";
        else
            print "<option id='$services_service_id' value='$id'>$ad</option>";
    }
    print "</select>";
}

function combo_service($category_id,$db_link){
    $cat_menyus = $db_link->get('services_service');
    print "<select name='service' id='select_service' class='form-control'>";
    print "<option value='0'> - - - - - - - - </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["title_az"]);
        if($category_id==$id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
    print "</select>";
}

function combo_menyu($cid,$category_id,$db_link){
    $db_link->where ('sub_id', $cid);
    $db_link->where ('status', 'active');
    $db_link->orderby ('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["name_az"]);
        if($category_id==$id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
}

function combo_files_category($cid,$category_id,$db_link){
    //$db_link->where ('sub_id', $cid);
    $db_link->where ('status', 'active');
    $db_link->orderby ('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["name_az"]);
        if($category_id==$id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
}


function send_mail($subject,$type,$video_name,$email1,$email2,$email3,$messa){
    global $db_link;
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'mail.bax.tv';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'noreply@bax.tv';                 // SMTP username
        $mail->Password = 'Vpb!a015';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->CharSet = 'utf-8';

        //Recipients
        $mail->setFrom('noreply@bax.tv', 'Bax.Tv');
        $mail->addAddress('noreply@bax.tv', 'Bax.Tv');
        if($email1) $mail->addBCC($email1, $type);     // Add a recipient
        if($email2) $mail->addBCC($email2, $type);     // Add a recipient
        foreach ($email3 as $emails) {
            $chemail=$db_link->where ('id', $emails)->getValue ("channels", "email");
            if($chemail) $mail->addBCC($chemail, $type);
        }

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body  = $messa;
        $mail->send();
    } catch (Exception $e) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } 
}

function combo_channel($cid,$category_id,$db_link){
    $db_link->where ('global_flag', 1,">");
    $db_link->orderby ('name', 'asc');
    $cat_menyus = $db_link->get('channels');
    $cat_menyus = $db_link->get('channels');
    print "<select name='channel_reklam' id='channel_reklam' class='form-control'>";
    print "<option value='0'> - - - - - </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["name"]);
        if($category_id==$id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
    print "</select>";
}

function combo_reklam_user($cid,$category_id,$db_link){
    $cat_menyus = $db_link->get('reklam_user');
    print "<select name='channel_reklam' id='channel_reklam' class='form-control'>";
    print "<option value='0'> - - - - - </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["name"]);
        if($category_id==$id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
    print "</select>";
}

function chek_menyu($cid,$category_id,$db_link){
    $db_link->where ('sub_id', $cid);
    $db_link->where ('status', 'active');
    $db_link->orderby ('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    $cids = explode(",", $category_id);
    foreach ($cat_menyus as $line) {
        print "<div class='col-lg-2'><div class='checkbox'><label><label class='checkbox'>";
        $id = $line["id"];
        $ad = stripslashes($line["name_az"]);
        if (in_array($id, $cids))
            print "<input name='category' type='radio' value='$id' checked>";
        else
            print "<input name='category' type='radio' value='$id'>";
        print "<span class='arrow'></label></span>$ad </label></div></div>";    
    }
}

function chekb_menyu($cid,$category_id,$db_link){
    $db_link->where ('sub_id', $cid);
    $db_link->where ('status', 'active');
    $db_link->orderby ('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    $cids = explode(",", $category_id);
    foreach ($cat_menyus as $line) {
        print "<div class='checkbox'><label>";
        $id = $line["id"];
        $ad = stripslashes($line["name_az"]);
        if (in_array($id, $cids))
            print "<input name='category[]' type='checkbox' value='$id' checked>";
        else
            print "<input name='category[]' type='checkbox' value='$id'>";
        print "$ad </label></div>";    
    }
}

function chek_channel($cid,$channel_id,$db_link){
    //$db_link->where ('sub_id', $cid);
    $db_link->where ('global_flag', 1);
    $db_link->orderby ('name', 'asc');
    $cat_menyus = $db_link->get('channels');
    $cids = explode(",", $channel_id);
    foreach ($cat_menyus as $line) {
        print "<div class='checkbox'><label>";
        $id = $line["id"];
        $ad = stripslashes($line["name"]);
        if (in_array($id, $cids))
            print "<input name='channels[]' type='checkbox' value='$id' checked>";
        else
            print "<input name='channels[]' type='checkbox' value='$id'>";
        print "$ad </label></div>";    
    }
}

function curlDOwnload($path,$link) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);

    $file = fopen($path, "w+");
    fwrite($file, $data);
    fclose($file);

    echo "\n $path file selected \n";
}

function getUrlContent($url,$type){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $xmlStr = curl_exec($ch);
    curl_close($ch);
    $xml = simplexml_load_string($xmlStr);
    if($type=='redirect'){
        if($xml->redirect)
            return $xml->redirect; 
    }
    if($type=='status'){
        if($xml->RC){
            if($xml->RC=='000') return "Successful";  
            if($xml->RC=='101') return "Decline, expired card";  
            if($xml->RC=='119') return "Decline, transaction not permitted to cardholder";  
            if($xml->RC=='100') return "Decline (general, no comments)";  
        }else{
            return $xml->code;  
        }
    }
}

//echo get_remote_data('http://example.com/');                                   //simple request
//echo get_remote_data('http://example.com/', "var2=something&var3=blabla" );    //POST request                                         

//====================docummented version(100% same - but with explanations): ====================
function get_remote_data($url, $post_paramtrs=false,$return_full_array=false)    {
    $c = curl_init();curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    //if parameters were passed to this function, then transform into POST method.. (if you need GET request, then simply change the passed URL)
    if($post_paramtrs){curl_setopt($c, CURLOPT_POST,TRUE);    curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&".$post_paramtrs );}
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);                  
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0"); 
    curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
    //We'd better to use the above command, because the following command gave some weird STATUS results..
    //$header[0]= $user_agent="User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0";  $header[]="Cookie:CookieName1=Value;"; $header[]="Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";  $header[]="Cache-Control: max-age=0"; $header[]="Connection: keep-alive"; $header[]="Keep-Alive: 300"; $header[]="Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; $header[] = "Accept-Language: en-us,en;q=0.5"; $header[] = "Pragma: ";  curl_setopt($c, CURLOPT_HEADER, true);     curl_setopt($c, CURLOPT_HTTPHEADER, $header);

    curl_setopt($c, CURLOPT_MAXREDIRS, 10); 
    //if SAFE_MODE or OPEN_BASEDIR is set,then FollowLocation cant be used.. so...
    $follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;  if ($follow_allowed){curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);}
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
    curl_setopt($c, CURLOPT_REFERER, $url);    
    curl_setopt($c, CURLOPT_TIMEOUT, 60);
    curl_setopt($c, CURLOPT_AUTOREFERER, true);  
    curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
    $data=curl_exec($c);$status=curl_getinfo($c);curl_close($c);

    preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si',  $status['url'],$link);    
    //correct assets URLs(i.e. retrieved url is: http://site.com/DIR/SUBDIR/page.html... then href="./image.JPG" becomes href="http://site.com/DIR/SUBDIR/image.JPG", but  href="/image.JPG" needs to become href="http://site.com/image.JPG")

    //inside all links(except starting with HTTP,javascript:,HTTPS,//,/ ) insert that current DIRECTORY url (href="./image.JPG" becomes href="http://site.com/DIR/SUBDIR/image.JPG")
    $data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/|\/)).*?)(\'|\")/si','$1=$2'.$link[0].'$3$4$5', $data);     
    //inside all links(except starting with HTTP,javascript:,HTTPS,//)    insert that DOMAIN url (href="/image.JPG" becomes href="http://site.com/image.JPG")
    $data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/)).*?)(\'|\")/si','$1=$2'.$link[1].'://'.$link[3].'$3$4$5', $data);   
    // if redirected, then get that redirected page
    if($status['http_code']==301 || $status['http_code']==302) { 
        //if we FOLLOWLOCATION was not allowed, then re-get REDIRECTED URL
        //p.s. WE dont need "else", because if FOLLOWLOCATION was allowed, then we wouldnt have come to this place, because 301 could already auto-followed by curl  :)
        if (!$follow_allowed){
            //if REDIRECT URL is found in HEADER
            if(empty($redirURL)){if(!empty($status['redirect_url'])){$redirURL=$status['redirect_url'];}}
            //if REDIRECT URL is found in RESPONSE
            if(empty($redirURL)){preg_match('/(Location:|URI:)(.*?)(\r|\n)/si', $data, $m); if (!empty($m[2])){ $redirURL=$m[2]; } }
            //if REDIRECT URL is found in OUTPUT
            if(empty($redirURL)){preg_match('/moved\s\<a(.*?)href\=\"(.*?)\"(.*?)here\<\/a\>/si',$data,$m); if (!empty($m[1])){ $redirURL=$m[1]; } }
            //if URL found, then re-use this function again, for the found url
            if(!empty($redirURL)){$t=debug_backtrace(); return call_user_func( $t[0]["function"], trim($redirURL), $post_paramtrs);}
        }
    }
    // if not redirected,and nor "status 200" page, then error..
    elseif ( $status['http_code'] != 200 ) { $data =  "ERRORCODE22 with $url<br/><br/>Last status codes:".json_encode($status)."<br/><br/>Last data got:$data";}
    return ( $return_full_array ? array('data'=>$data,'info'=>$status) : $data);
}

function curl_get($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($curl);
    curl_close($curl);
    return $return;
}

function vimeo_info($vid,$width=640) {
    $oembed_endpoint = 'http://vimeo.com/api/oembed';
    $video_url = 'http://vimeo.com/'.$vid;
    $json_url = $oembed_endpoint . '.json?url=' . rawurlencode($video_url) . '&width='.$width;
    $xml_url = $oembed_endpoint . '.xml?url=' . rawurlencode($video_url) . '&width='.$width;
    $oembed = simplexml_load_string(curl_get($xml_url));
    return $oembed;
} 

function VatsParse($url,$type){
    if($url){
        $xml=simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA) or die("Error: Cannot create object");
        //print $xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> MediaFiles[0] -> MediaFile;
        //if($type==video)
        $_SESSION['VatsVideo']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> MediaFiles[0] -> MediaFile;
        //if($type==Impression0)
        $_SESSION['VatsUri']=$xml -> Ad[0] -> InLine[0] -> Impression[0];
        //if($type==Impression1)
        $_SESSION['VatsLink']=$xml -> Ad[0] -> InLine[0] -> Impression[1];
        //if($type==start)
        $_SESSION['VatsStart']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[0];
        //if($type==midpoint)
        $_SESSION['VatsMidpoint']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[1];
        //if($type==firstquartile)
        $_SESSION['VatsFirstquartile']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[2];
        //if($type==thirdquartile)
        $_SESSION['VatsThirdquartile']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[3];
        //if($type==complete)
        $_SESSION['VatsComplete']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[4];
        //if($type==mute)
        $_SESSION['VatsMute']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[5];
        //if($type==pause)
        $_SESSION['VatsPause']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[6];
        //if($type==replay)
        $_SESSION['VatsReplay']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[7];
        //if($type==fullscreen)
        $_SESSION['VatsFullscreen']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[8];
        //if($type==stop)
        $_SESSION['VatsStop']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[9];
        //if($type==unmute)
        $_SESSION['VatsUnmute']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[10];
        //if($type==resume)
        $_SESSION['VatsResume']=$xml -> Ad[0] -> InLine[0] -> Creatives[0] -> Creative -> Linear  -> TrackingEvents[0] -> Tracking[11];
    }
}

function isMobile() {
    return preg_match("/(android|iPhone|iphone|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function user_agent_type(){
    $tablet_browser = 0;
    $mobile_browser = 0;
    $device = '';

    if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
        $device = "ipad";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
        $device = "iphone";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
        $device = "blackberry";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
        $device = "android";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'windows') ) {
        $device = "windows";
    }

    if (preg_match('/(tablet|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $tablet_browser++;
    }

    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|ipad|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $mobile_browser++;
    }

    if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        $mobile_browser++;
    }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda ','xda-');

    if (in_array($mobile_ua,$mobile_agents)) {
        $mobile_browser++;
    }

    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
        $mobile_browser++;
        //Check for tablets on opera mini alternative headers
        $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
        //if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
        if (preg_match('/(tablet|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
            $tablet_browser++;
        }
    }


    /*if( $device ) {
    return $device; 
    } return false; {
    return false;
    }   */

    if ($device =='iphone') {
        return 'iphone';
    }
    if ($tablet_browser > 0) {
        return 'tablet';
    }
    else if ($mobile_browser > 0) {
        return 'mobile';
    }
    else {
        return 'desktop';
    } 
}

function alphaID($in, $to_num = false, $pad_up = false, $pass_key = "L2e8H0l5u3l2u"){
    /*
    // Input //
    $number_in = 2188847690240;
    $alpha_in  = "SpQXn7Cb";

    // Execute //
    $alpha_out  = alphaID($number_in, false, 8);
    $number_out = alphaID($alpha_in, true, 8);        
    */

    $out   =   '';
    $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base  = strlen($index);
    if ($pass_key !== null) {
        for ($n = 0; $n < strlen($index); $n++) {
            $i[] = substr($index, $n, 1);
        }
        $pass_hash = hash('sha256',$pass_key);
        $pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);
        for ($n = 0; $n < strlen($index); $n++) {
            $p[] =  substr($pass_hash, $n, 1);
        }
        array_multisort($p, SORT_DESC, $i);
        $index = implode($i);
    }
    if ($to_num) {
        $len = strlen($in) - 1;
        for ($t = $len; $t >= 0; $t--) {
            $bcp = bcpow($base, $len - $t);
            $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
        }
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $out -= pow($base, $pad_up);
            }
        }
    } else {
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $in += pow($base, $pad_up);
            }
        }
        for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a   = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in  = $in - ($a * $bcp);
        }
    }
    return $out;
}    

Class Encryption
{
    // DECLARE THE REQUIRED VARIABLES
    public $ENC_METHOD = "AES-256-CBC"; // THE ENCRYPTION METHOD.
    public $ENC_KEY = "SOME_RANDOM_KEY"; // ENCRYPTION KEY
    public $ENC_IV = "SOME_RANDOM_IV"; // ENCRYPTION IV.
    public $ENC_SALT = "xS$"; // THE SALT FOR PASSWORD ENCRYPTION ONLY.
    // DECLARE  REQUIRED VARIABLES TO CLASS CONSTRUCTOR
    function __construct($METHOD = NULL, $KEY = NULL, $IV = NULL, $SALT = NULL)
    {
        try
        {
            // Setting up the Encryption Method when needed.
            $this->ENC_METHOD = (isset($METHOD) && !empty($METHOD) && $METHOD != NULL) ?
            $METHOD : $this->ENC_METHOD;
            // Setting up the Encryption Key when needed.
            $this->ENC_KEY = (isset($KEY) && !empty($KEY) && $KEY != NULL) ?
            $KEY : $this->ENC_KEY;
            // Setting up the Encryption IV when needed.
            $this->ENC_IV = (isset($IV) && !empty($IV) && $IV != NULL) ?
            $IV : $this->ENC_IV;
            // Setting up the Encryption IV when needed.
            $this->ENC_SALT = (isset($SALT) && !empty($SALT) && $SALT != NULL) ?
            $SALT : $this->ENC_SALT;
        }
        catch (Exception $e)
        {
            return "Caught exception: ".$e->getMessage();
        }
    }
    // THIS FUNCTION WILL ENCRYPT THE PASSED STRING
    public function Encrypt($string)
    {
        try
        {
            $output = false;
            $key = hash('sha256', $this->ENC_KEY);
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $this->ENC_IV), 0, 16);
            $output = openssl_encrypt($string, $this->ENC_METHOD, $key, 0, $iv);
            $output = base64_encode($output);
            return $output;
        }
        catch (Exception $e)
        {
            return "Caught exception: ".$e->getMessage();
        }
    }
    // THIS FUNCTION WILL DECRYPT THE ENCRYPTED STRING.
    public function Decrypt($string)
    {
        try
        {
            $output = false;
            // hash
            $key = hash('sha256', $this->ENC_KEY);
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $this->ENC_IV), 0, 16);
            $output = openssl_decrypt(base64_decode($string), $this->ENC_METHOD, $key, 0, $iv);
            return $output;
        }
        catch (Exception $e)
        {
            return "Caught exception: ".$e->getMessage();
        }
    }
    // THIS FUNCTION FOR PASSWORDS ONLY, BECAUSE IT CANNOT BE DECRYPTED IN FUTURE.
    public function EncryptPassword($Input)
    {
        try
        {
            if (!isset($Input) || $Input == null || empty($Input)) { return false;}
            // GENERATE AN ENCRYPTED PASSWORD SALT
            $SALT = $this->Encrypt($this->ENC_SALT);
            $SALT = md5($SALT);
            // PERFORM MD5 ENCRYPTION ON PASSWORD SALT.
            // ENCRYPT PASSWORD
            $Input = md5($this->Encrypt(md5($Input)));
            $Input = $this->Encrypt($Input);
            $Input =  md5($Input);
            // PERFORM ANOTHER ENCRYPTION FOR THE ENCRYPTED PASSWORD + SALT.
            $Encrypted = $this->Encrypt($SALT).$this->Encrypt($Input);
            $Encrypted = sha1($Encrypted.$SALT);
            // RETURN THE ENCRYPTED PASSWORD AS MD5
            return md5($Encrypted);
        }
        catch (Exception $e)
        {
            return "Caught exception: ".$e->getMessage();
        }
    }
}


?>
