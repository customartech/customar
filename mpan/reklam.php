<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

/*if ($_GET['tip']==edit_reklam) $content_date=$reklam_info['content_date']; else $content_date=date('Y-m-d');  */
$tip='';
$category_id='';
$category_id=(int)$_GET['category_id'];
$tip=(string)$_GET['tip'];
$cid=(string)$_GET['cid'];

/*
$desired_dir="../uploads/reklam/$category_id/";
if(is_dir($desired_dir)==false){
mkdir($desired_dir, 0755);
} */

$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

if ($tip=='delete_reklam'){
    $id = addslashes($_GET['cid']);
    $db_link->where('id',$id)->delete('reklam'); 
    echo '<script>document.location.href="?menu=reklam&category_id='.$category_id.'";</script>';
}

if($tip=='unpublish') {
    $insert_data = array(
        'status' => 0
    );       

    $db_link->where('id',$cid)->update('reklam', $insert_data);
    echo '<script>document.location.href="index.php?menu=reklam&category_id='.$category_id.'";</script>';
}

if($tip=='publish') {
    $insert_data = array(
        'status' => 1
    );       

    $db_link->where('id',$cid)->update('reklam', $insert_data);
    echo '<script>document.location.href="index.php?menu=reklam&category_id='.$category_id.'";</script>';
}

if (empty($tip)){

    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header"> <?php print $tbl_category?>  </div>
                <div class="card-body">
                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <!-- <a href="?menu=reklam&tip=add_reklam&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>                            
                            <a href="?menu=reklam&tip=add_reklam_youtube&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new Youtube reklam</a>
                            <a href="?menu=reklam&tip=add_reklam_vast&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new Vast reklam</a>-->
                        </div>
                    </div>
                    <br>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listReklam">
                            <thead>
                                <tr>
                                    <th>VID</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Link</th> 
                                    <th>Ümumi baxış</th> 
                                    <th>Bugünki baxış</th> 
                                    <th>Günlük limit</th> 
                                    <th>Limit</th> 
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php
}


if ($tip=='edit_reklam'){
    $id = addslashes($_GET['cid']);
    $reklam_info = $db_link->where ('id', $id)->getOne('reklam');    
    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-default">
                <div class="card-header">
                    <?php print $tbl_category;?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form role="form" name="reklam_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $reklam_info['id']?>" />                    
                            <div class="row">
                                <div class="form-group col-lg-3">
                                    <label>Title</label><input class="form-control" type="text" name="name" size="107" value="<?php print stripslashes($reklam_info['name'])?>">
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>max_plays</label> <input class="form-control" type="text" name="max_plays" size="107" value="<?php print stripslashes($reklam_info['max_plays'])?>">
                                </div>
                                <div class="form-group col-lg-2">
                                    <label>max_daily_plays</label> <input class="form-control" type="text" name="max_daily_plays" size="107" value="<?php print stripslashes($reklam_info['max_daily_plays'])?>">
                                </div>
                                <div class="form-group col-lg-2">
                                    <label>view_country</label> <input class="form-control" type="text" name="view_country" size="107" value="<?php print stripslashes($reklam_info['view_country'])?>">
                                </div>
                                <div class="form-group col-lg-2">
                                    <label>Free</label> <input class="form-control" type="text" name="free" size="107" value="<?php print stripslashes($reklam_info['free'])?>">
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>disable_category</label> 
                                    <div class="form-group" style='overflow:scroll; height:300px;'>
                                        <?php
                                        chekb_menyu(1,$reklam_info['view_category'],$db_link);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>disable_channels</label> 
                                    <div class="form-group" style='overflow:scroll; height:300px;'>
                                        <?php
                                        chek_channel(1,$reklam_info['view_channels'],$db_link);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>User</label> 
                                    <div class="form-group">
                                        <?php
                                        combo_reklam_user($cid,$reklam_info['channel_id'],$db_link);
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label>Link</label> <input class="form-control" type="text" name="mp4link" size="107" value="<?php print stripslashes($reklam_info['mp4link'])?>">
                            </div>                            
                            <br>
                            <div class="form-group col-lg-12">
                                <center>
                                    <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                                </center>
                            </div>
                        </form>
                    </div>
                </div>          
            </div>
        </div>

        <?php
    } 
    elseif($_POST['edit']) {
        $category = implode(",", $_POST['category']);
        $channels = implode(",", $_POST['channels']);

        $update_data = array(
            'name' => $_POST['name'],
            'free' => $_POST['free'],
            'max_plays' => $_POST['max_plays'],
            'max_daily_plays' => $_POST['max_daily_plays'],
            'channel_id' => $_POST['channel_reklam'],
            'view_category' => $category,
            'view_country' => $_POST['view_country'],
           /* 'mp4link' => $_POST['mp4link'],*/
            'view_channels' => $channels
        );       

        $db_link->where('id', $id)->update('reklam', $update_data);
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=reklam&category_id='.$category_id.'";</script>';
    }
}
if ($tip=='add_reklam'){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="card-header">
                    <?php print $tbl_category['name_az']?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form role="form" name="reklam_edit" action="" method="post" enctype="multipart/form-data">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="az">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="name" size="107" value="<?php print stripslashes($reklam_info['name'])?>">
                                        <label>Description</label><textarea class="form-control" id="description" name="description" rows="15" cols="80"><?php print stripcslashes($reklam_info['description'])?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Video</label> <input class="form-control" type="file" name="video" size="42">
                            </div>
                            <br>
                            <center>
                                <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
        /* try {
        //include("vimeo_conn.php");
        $file_name=$_FILES['video']['name'];
        $uri = $lib->upload($_FILES['video']['tmp_name']);
        $video_data = $lib->request($uri);
        $link = '';
        if($video_data['status'] == 200) { 
        $link = $video_data['body']['link'];  
        $status = $video_data['body']['status'];  
        $duration = $video_data['body']['duration'];  
        $mp4link = $video_data['body']['files'][0]['link'];  
        $pictures = $video_data['body']['pictures']['sizes'][3]['link'];  
        list($url,$video_id)=explode('reklam.com/',$link);

        }   */
        /*$title=array('name' => 'hide','owner' =>  'hide','portrait' => 'hide');
        $buttons=array('like' => 0,'share' => 0,'embed' => 0,'fullscreen' => 0,'scaling' => 0,'watchlater' => 0);
        $logos=array('custom' => array('active' => 0));
        $embed=array('playbar' => 0,'volume' => 0,'buttons' => $buttons,'logos' => $logos,'title' => $title);
        $privacy=array('view' => 'disable','embed' => 'public','download' => 0,'add' => 0,'comments' => 'nobody');  //'embed' => 'whitelist'
        $lib->request($uri, array('name' => $_POST['name'],'description' => $_POST['description'],'embed' => $embed,'privacy' => $privacy), 'PATCH');*/
        /*    $lib->request($uri, array('name' => $_POST['name'],'description' => $_POST['description']), 'PATCH');
        }
        catch (reklamUploadException $e) {
        print 'Error uploading ' . $file_name . "\n";
        print 'Server reported: ' . $e->getMessage() . "\n";
        } */

        /*$insert_data = array(
        'category_id' => $category_id,
        'name' => $_POST['name'],
        'vid' => $video_id,
        'description' => $_POST['description'],
        'duration' => $duration,
        'link' => $link,
        'mp4link' => $mp4link,                        
        'pictures' => $pictures,
        'status' => $status
        );       

        if($video_id) $db_link->insertData('reklam',$insert_data); */

        //print $sql;
        echo '<script>document.location.href="?menu=reklam&category_id='.$category_id.'";</script>';
    }
}
if ($tip=='add_reklam_youtube'){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-default">
                <div class="card-header">
                    <?php print $tbl_category['name_az']?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        if(!$_POST['add_next']) {   
                            ?>
                            <form role="form" name="reklam_edit" action="" method="post" enctype="multipart/form-data">
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="az">
                                        <div class="form-group">
                                            <label>Youtube reklam Link</label><input class="form-control" type="text" name="youtubeLink" size="107">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <center>
                                    <input class="btn btn-primary" name="add_next" type="submit" id="edit" value="Grab"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                                </center>
                            </form>
                            <?php
                        }elseif($_POST['add_next']=="Grab") { 
                            include_once 'downloader/YouTubeDownloader.php';
                            include_once 'downloader/reklamDownloader.php';
                            include_once 'downloader/LinkHandler.php';

                            $url = $_POST['youtubeLink'];
                            $handler = new LinkHandler();
                            $downloader = $handler->getDownloader($url);
                            $downloader->setUrl($url);
                            if($downloader->hasVideo())
                            {
                                $YSelect .="<select name='video'>";
                                foreach($downloader->getVideoDownloadLink() as $cc => $name) {
                                    $YSelect .='<option value="' . $name['url'] . '">' . $name['title'] . ' ' . $name['quality'] . ' ' . $name['format'] . '</option>';
                                }
                                $YSelect .="</select>";
                            }
                            ?>
                            <form role="form" name="reklam_edit" action="" method="post" enctype="multipart/form-data">
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="az">
                                        <div class="form-group">
                                            <label>Video</label> <?php print $YSelect; ?>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <center>
                                    <input class="btn btn-primary" name="add_next" type="submit" id="edit" value="Next "> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                                </center>
                            </form>
                            <?php
                        }else{
                            $tempfile="tesmp".date('Ymdhis').".mp4";
                            curlDOwnload($tempfile,$_POST['video']);
                            ?>
                            <form role="form" name="reklam_edit" action="" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="youtubeVideo" value='<?php print $tempfile;?>'>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="az">
                                        <div class="form-group">
                                            <label>Title</label><input class="form-control" type="text" name="name" size="107" value="<?php print stripslashes($reklam_info['name'])?>">
                                            <label>Description</label><textarea class="form-control" id="description" name="description" rows="15" cols="80"><?php print stripcslashes($reklam_info['description'])?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <center>
                                    <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                                </center>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
        /*try {
        //include("vimeo_conn.php");
        $file_name=$_POST['youtubeVideo'];
        $uri = $lib->upload($file_name);
        $video_data = $lib->request($uri);
        $link = '';
        if($video_data['status'] == 200) { 
        $link = $video_data['body']['link'];  
        $status = $video_data['body']['status'];  
        $duration = $video_data['body']['duration'];  
        $mp4link = $video_data['body']['files'][0]['link'];  
        $pictures = $video_data['body']['pictures']['sizes'][3]['link'];  
        list($url,$video_id)=explode('reklam.com/',$link);

        } */
        /*$title=array('name' => 'hide','owner' =>  'hide','portrait' => 'hide');
        $buttons=array('like' => 0,'share' => 0,'embed' => 0,'fullscreen' => 0,'scaling' => 0,'watchlater' => 0);
        $logos=array('custom' => array('active' => 1));
        if($category_id==2)
        $embed=array('playbar' => 0,'volume' => 0,'buttons' => $buttons,'logos' => $logos,'title' => $title);
        else
        $embed=array('playbar' => 1,'volume' => 1,'buttons' => $buttons,'logos' => $logos,'title' => $title);
        $privacy=array('view' => 'disable','embed' => 'public','download' => 0,'add' => 0,'comments' => 'nobody');  //'embed' => 'whitelist'
        $lib->request($uri, array('name' => $_POST['name'],'description' => $_POST['description'],'embed' => $embed,'privacy' => $privacy), 'PATCH');
        */
        /*    $lib->request($uri, array('name' => $_POST['name'],'description' => $_POST['description']), 'PATCH');
        }
        catch (reklamUploadException $e) {
        print 'Error uploading ' . $file_name . "\n";
        print 'Server reported: ' . $e->getMessage() . "\n";
        }

        $insert_data = array(
        'category_id' => $category_id,
        'name' => $_POST['name'],
        'vid' => $video_id,
        'description' => $_POST['description'],
        'duration' => $duration,
        'link' => $link,
        'mp4link' => $mp4link,                        
        'pictures' => $pictures,
        'status' => $status
        );       

        if($video_id) $db_link->insertData('reklam',$insert_data); */
        //@unlink($file_name);
        //print $sql;
        echo '<script>document.location.href="?menu=reklam&category_id='.$category_id.'";</script>';
    }
}
if ($tip=='add_reklam_vast'){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-default">
                <div class="card-header">
                    <?php print $tbl_category['name_az']?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form role="form" name="reklam_edit" action="" method="post" enctype="multipart/form-data">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="az">
                                    <div class="form-group">
                                        <label>Vast reklam Link</label><input class="form-control" type="text" name="mp4link" size="107">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="az">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="name" size="107" value="<?php print stripslashes($reklam_info['name'])?>">
                                        <label>Description</label><textarea class="form-control" id="description" name="description" rows="15" cols="80"><?php print stripcslashes($reklam_info['description'])?></textarea>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <center>
                                <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
        /* $insert_data = array(
        'category_id' => $category_id,
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'mp4link' => $_POST['mp4link'],                        
        'type' => 'vast',                        
        'status' => 'available'
        );       

        $db_link->insertData('reklam',$insert_data);  */
        //@unlink($file_name);
        //print $sql;
        echo '<script>document.location.href="?menu=reklam&category_id='.$category_id.'";</script>';
    }
}
?>