<?php
session_start();
$security_test=1;
include("config.php");
include("function.php");

if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

if(!($_SESSION['sessionid'])) $_SESSION['sessionid']=date('Ymdhis').yazi();
$sessionid=$_SESSION['sessionid'];

$vid = addslashes($_GET['vid']);
$vimeo_id = addslashes($_GET['vimeo_id']);

if($vid){
    ?>
    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>BAXTV ..::<?php print $vimeo_name;?>::..</title>
            <meta name="description" content="BAXTV <?php print $vimeo_name;?>">
            <meta name="author" content="TM">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="/css/resset.css">
            <link rel="stylesheet" href="/player/plyr.css">

        </head>
        <body>
            <div class='embed-container'>
                <div data-type="vimeo" data-video-id="<?php print $vimeo_id;?>"></div>
            </div>
            <script src="/player/plyr.js"></script>
            <script>
            plyr.setup();
            /*plyr.setup(
             controls ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume']
            ); */
            </script>
        </body> 
    </html>
    <?php
}
?>