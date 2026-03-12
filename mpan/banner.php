<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
if(!$security_test) exit;
$id = addslashes($_GET['cid']);
$tip = addslashes($_GET['tip']);


if($tip=='unpublish') {
    $insert_data = array(
        'status' => 'deactive'
    );       

    $db_link->where('id',$id)->update('banner', $insert_data);
    echo '<script>document.location.href="?menu=banner";</script>';
}

if($tip=='publish') {
    $insert_data = array(
        'status' => 'active'
    );       

    $db_link->where('id',$id)->update('banner', $insert_data);
    echo '<script>document.location.href="?menu=banner";</script>';
}

if ($_GET['tip']==delete_banner){
    $db_link->where('id',$id)->delete('banner');
    echo '<script>document.location.href="?menu=banner";</script>';
}

if (empty($_GET['tip'])){
    $tbl_banner = $db_link->get('banner');
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header"> Banner </div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=banner&tip=add_banner" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>
                    </div>
                    <br>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listKatalog">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Yer</th> 
                                    <th>View<br>Daliy</th> 
                                    <th>Click<br>Daliy</th> 
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($tbl_banner as $banner) {
                                    $id = stripslashes($banner['id']);
                                    $banner_date = stripslashes($banner['banner_date']);
                                    $name_az = stripslashes($banner['title_az']);
                                    $category_id = stripslashes($banner['category_id']);
                                    $plays = stripslashes($banner['plays']);
                                    $daily_plays = stripslashes($banner['daily_plays']);
                                    $click = stripslashes($banner['click']);
                                    $daily_click = stripslashes($banner['daily_click']);
                                    $status = stripslashes($banner['status']);

                                    if($category_id==1) $category="Ana səhifə üst";
                                    elseif($category_id==2) $category="Ana səhifə orta";
                                    elseif($category_id==5) $category="Ana səhifə alt";
                                    elseif($category_id==3) $category="Player alt";
                                    elseif($category_id==4) $category="Player Yan";
                                    elseif($category_id==6) $category="Sag Yan";
                                    elseif($category_id==7) $category="Sol Yan";
                                    elseif($category_id==8) $category="Player üst";

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td>$banner_date</td>
                                    <td>$name_az</td>
                                    <td>$category</td>
                                    <td>$plays<br>$daily_plays</td>
                                    <td>$click<br>$daily_click</td>
                                    <td class='center'>";
                                    print "<a rel=tooltip title='Redakte' href='?menu=banner&tip=edit_banner&cid=$id'><span class='fa fa-pencil'></span></a>&nbsp;";
                                    print "<a rel=tooltip title='Sil'  onclick='Del(\"?menu=banner&tip=delete_banner&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>&nbsp;";

                                    if($status=='active') 
                                        print "<a rel=tooltip title='Unpublish' href='?menu=banner&tip=unpublish&cid=$id'><span class='fa fa-check'></span></a>"; 
                                    else 
                                        print "<a rel=tooltip title='publish' href='?menu=banner&tip=publish&cid=$id'><span class='fa fa-close'></span></a>";

                                    print"</td>
                                    </tr>";
                                } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php
}


if ($_GET['tip']==edit_banner){
    $banner_info = $db_link->where ('id', $id)->getOne('banner');
    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    Banner
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="banner_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $banner_info['id']?>" />                    
                            <div class="row">
                                <div class="col-lg-4 col-md-4">
                                    <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($banner_info['title_az'])?>'>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>max_plays</label> <input class="form-control" type="text" name="max_plays" size="107" value="<?php print stripslashes($banner_info['max_plays'])?>">
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>max_daily_plays</label> <input class="form-control" type="text" name="max_daily_plays" size="107" value="<?php print stripslashes($banner_info['max_daily_plays'])?>">
                                </div>                                 
                                <div class="col-lg-4 col-md-4">
                                    <label>Yeri</label>
                                    <select class="form-control" name = "categoryid">
                                        <option value="1">Ana səhifə üst</option>
                                        <option value="2">Ana səhifə orta</option>
                                        <option value="5">Ana səhifə alt</option>
                                        <option value="8">Player ust</option>
                                        <option value="3">Player alt</option>
                                        <option value="4">Player Yan</option>
                                        <option value="6">Sag Yan</option>
                                        <option value="7">Sol Yan</option>                                    
                                    </select><?php print  "<script> banner_edit.categoryid.value='".$banner_info['category_id']."'; </script>"; ?>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <label>User</label> 
                                    <div class="form-group">
                                        <?php
                                        combo_reklam_user($cid,$banner_info['channel_id'],$db_link);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Desktop Editor <input onclick="toggle_tinymce_checkbutton('bdesktop','desktop');" id="bdesktop" type="button" value="on"></label><textarea class="form-control" id="desktop" name="desktop" rows="15" cols="80"><?php print stripcslashes($banner_info['desktop'])?></textarea>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Mobil Editor <input onclick="toggle_tinymce_checkbutton('bmobil','mobil');" id="bmobil" type="button" value="on"></label><textarea class="form-control" id="mobil" name="mobil" rows="15" cols="80"><?php print stripcslashes($banner_info['mobil'])?></textarea>
                            </div>
                            <div class="form-group col-lg-12">
                                <br>
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
        $update_data = array(
            'title_az' => $_POST['title_az'],                        
            'desktop' => $_POST['desktop'],                        
            'mobil' => $_POST['mobil'],                        
            'channel_id' => (int)$_POST['channel_reklam'],
            'max_daily_plays' => (int)$_POST['max_daily_plays'],                        
            'max_plays' => (int)$_POST['max_plays'],                                     
            'category_id' => (int)$_POST['categoryid']
        );       
        $db_link->where ('id', $id)->update ('banner', $update_data);
        echo '<script>document.location.href="?menu=banner";</script>';
    }
}

if ($_GET['tip']==add_banner){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-default">
                <div class="card-header">
                    Banner
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="banner_edit" action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-4">
                                    <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($banner_info['title_az'])?>'>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>max_plays</label> <input class="form-control" type="text" name="max_plays" size="107" value="0">
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>max_daily_plays</label> <input class="form-control" type="text" name="max_daily_plays" size="107" value="0">
                                </div>                                
                                <div class="form-group col-lg-4 col-md-4">
                                    <label>Yeri</label>
                                    <select class="form-control" name = "categoryid">
                                        <option value="1">Ana səhifə üst</option>
                                        <option value="2">Ana səhifə orta</option>
                                        <option value="5">Ana səhifə alt</option>
                                        <option value="8">Player ust</option>
                                        <option value="3">Player alt</option>
                                        <option value="4">Player Yan</option>
                                        <option value="6">Sag Yan</option>
                                        <option value="7">Sol Yan</option>                                                                       
                                    </select><?php print  "<script> banner_edit.categoryid.value='".$banner_info['category_id']."'; </script>"; ?>
                                </div>
                                <div class="form-group col-lg-4 col-md-4">
                                    <label>User</label> 
                                    <div class="form-group">
                                        <?php
                                        combo_reklam_user($cid,$banner_info['channel_id'],$db_link);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Desktop Editor <input onclick="toggle_tinymce_checkbutton('bdesktop','desktop');" id="bdesktop" type="button" value="on"></label><textarea class="form-control" id="desktop" name="desktop" rows="15" cols="80"><?php print stripcslashes($banner_info['desktop'])?></textarea>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Mobil Editor <input onclick="toggle_tinymce_checkbutton('bmobil','mobil');" id="bmobil" type="button" value="on"></label><textarea class="form-control" id="mobil" name="mobil" rows="15" cols="80"><?php print stripcslashes($banner_info['mobil'])?></textarea>
                            </div>
                            <div class="form-group col-lg-12">
                                <br>
                                <center>
                                    <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                                </center>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
        $insert_data = array(
            'title_az' => $_POST['title_az'],                        
            'desktop' => $_POST['desktop'],                        
            'mobil' => $_POST['mobil'],
            'channel_id' => (int)$_POST['channel_reklam'],
            'max_daily_plays' => (int)$_POST['max_daily_plays'],                        
            'max_plays' => (int)$_POST['max_plays'],                                     
            'category_id' => (int)$_POST['categoryid'],
            'banner_date' => $db_link->now(),
            'banner_time' => $db_link->now(),                        
            'status' => 'active'
        );       
        $db_link->insert('banner', $insert_data);
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=banner";</script>';
    }
}
?>