<?php
/*$video_count = $db_link->where("status", "available")->getValue("vimeo", "count(id)");
$video_conv_count = $db_link->where("status", "uploading")->getValue("vimeo", "count(id)");
$video_hide_count = $db_link->where("publish", 0)->getValue("vimeo", "count(id)");
$channel_count = $db_link->where("status", "active")->getValue("channels", "count(id)");

$bugun=$db_link->where("channel_id",0,"<>")->where("date(dateview)",date("Y-m-d"),"=")->getValue ("reklam_history", "count(id)");
$fullgun=$db_link->where("channel_id",0,"<>")->getValue ("channels_daily_reklam", "sum(plays)");
$fullgunAzn=round(round(((($fullgun+$bugun)*60)/1000),0)/100,2);
$odenilenAzn=round($db_link->getValue ("channels_payments", "sum(amount)"),2);
$fullcemAzn=round($fullgunAzn-$odenilenAzn,2);*/
?>
<div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
        <div class="card-box noradius noborder bg-default">
            <i class="fa fa-file-video-o  float-right text-white"></i>
            <h6 class="text-white text-uppercase m-b-20">Videolar</h6>
            <h1 class="m-b-20 text-white counter"><?php print $video_count;?></h1>
            <span class="text-white"> video</span>
        </div>
    </div>

    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
        <div class="card-box noradius noborder bg-warning">
            <i class="fa fa-pause float-right text-white"></i>
            <h6 class="text-white text-uppercase m-b-20">Deaktiv videolar</h6>
            <h1 class="m-b-20 text-white counter"><?php print $video_hide_count;?></h1>
            <span class="text-white"> video</span>
        </div>
    </div>

    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
        <div class="card-box noradius noborder bg-danger">
            <i class="fa fa-bell-o float-right text-white"></i>
            <h6 class="text-white text-uppercase m-b-20">Convertasiya</h6>
            <h1 class="m-b-20 text-white counter"><?php print $video_conv_count;?></h1>
            <span class="text-white"> video</span>
        </div>
    </div>

    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
        <div class="card-box noradius noborder bg-secondary">
            <i class="fa fa-user-o float-right text-white"></i>
            <h6 class="text-white text-uppercase m-b-20">İstİfadəçilər</h6>
            <h1 class="m-b-20 text-white counter"><?php print $channel_count;?></h1>
            <span class="text-white"> &nbsp;</span>
        </div>
    </div>    
    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
        <div class="card-box noradius noborder bg-dark">
            <i class="fa fa-shopping-cart float-right text-white"></i>
            <h6 class="text-white text-uppercase m-b-20">Borclar</h6>
            <h1 class="m-b-20 text-white counter"><?php print $fullcemAzn;?></h1>
            <span class="text-white">Ümumi: <?php print $fullgunAzn;?> <a href="index.php?menu=borclar"> Ətraflı bax</a></span>
        </div>
    </div>    
    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
        <div class="card-box noradius noborder bg-warning">
            <i class="fa fa-shopping-cart float-right text-white"></i>
            <h6 class="text-white text-uppercase m-b-20">Ödənİlmİşlər</h6>
            <h1 class="m-b-20 text-white counter"><?php print $odenilenAzn;?></h1>
            <span class="text-white"> <a href="index.php?menu=borclar&type=1"> Ətraflı bax</a></span>
        </div>
    </div>

</div>
