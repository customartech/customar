<?php
header('Content-Type: text/html; charset=utf-8');
include_once("config.php");

$db_link = @mysql_connect($sqllocalhost, $sqluser, $sqlpasword) or die ();
@mysql_select_db ($dbname,$db_link) or die();
mysql_query ("set character_set_client='utf8'");
mysql_query ("set character_set_results='utf8'");
mysql_query ("set collation_connection='utf8_general_ci'");

use Vimeo\Vimeo;
use Vimeo\Exceptions\VimeoUploadException;
$config = require('../vimeo/init.php');

$sql = "SELECT id,vid FROM vimeo";
$result = mysql_query($sql,$db_link);
while ($vimeo = mysql_fetch_array($result)){
    $id = stripslashes($vimeo['id']);
    $vid = stripslashes($vimeo['vid']);
    print "<br>".$vid;

    if(isset($vid)){
        $lib = new Vimeo($client_id, $client_secret,$access_token);
        $video = $lib->request("/me/videos/$vid");
    }else{
        echo("Not find get id video");
    }

    $link = isset($video['body']['link'])?$video['body']['link']:"";  
    $name = isset($video['body']['name'])?$video['body']['name']:"";  
    $description = isset($video['body']['description'])?$video['body']['description']:"";  
    $status = isset($video['body']['status'])?$video['body']['status']:"";  
    $duration = isset($video['body']['duration'])?$video['body']['duration']:"";  
    $mp4link = isset($video['body']['files'][0]['link'])?$video['body']['files'][0]['link']:"";  
    $pictures = isset($video['body']['pictures']['sizes'][3]['link'])?$video['body']['pictures']['sizes'][3]['link']:"";
    $plays=isset($video['body']["stats"]["plays"])?$video['body']["stats"]["plays"]:"";  
    $create_date=date("Y-m-d H:i:s", strtotime($video['body']["created_time"]));  
    $update_date=date("Y-m-d H:i:s", strtotime($video['body']["modified_time"]));  
    list($url,$video_id)=explode('vimeo.com/',$link);

    print "<br>";
    print $sql_update="UPDATE `vimeo` SET `plays` = '$plays' WHERE `id` = '$id'";
    if($plays) $result1 = mysql_query($sql_update,$db_link);  
}
@mysql_free_result($result);
mysql_close($db_link);
?>