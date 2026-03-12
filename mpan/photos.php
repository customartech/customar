<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
if(!$security_test) exit;
$category_id=$_GET['category_id'];                   

$desired_dir="../uploads/photos/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
} 

$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

?>                                            
<?php
if ($_GET['tip']=='delete_photos') {
    $db_link->where('id',$_GET['id'])->delete('photos');
    @unlink($desired_dir.$_GET['fayl']);
    $relink="?menu=photos&category_id=".$_GET['category_id'];
    echo '<script>document.location.href="'.$relink.'";</script>';  
}

if($_POST['add']) {

    $insert_data = array(
        'm_id' => $_GET['category_id'],
        'name_az' => $_POST['name_az'],
        'name_en' => $_POST['name_en'],
        'name_ru' => $_POST['name_ru'],
        'link_az' => $_POST['link_az'],
        'link_en' => $_POST['link_en'],
        'link_ru' => $_POST['link_ru'],        
        'description' => $_POST['description']        
    );       
    $cat_id = $db_link->insert ('photos', $insert_data);    

    $saat=date("YmdHis");
    $target_path = $desired_dir;

    $ext_az = pathinfo($_FILES['uploaded_az']['name'], PATHINFO_EXTENSION);
    $img_name_az = 'photos'.$_GET['category_id'].'_az_'.$saat.'.'.$ext_az;
    $target_path_az = $target_path.$img_name_az;
    if(move_uploaded_file($_FILES['uploaded_az']['tmp_name'], $target_path_az)){
        $insert_data = array(
            'file_az' => $img_name_az
        );        
        $db_link->where('id', $cat_id)->update('photos', $insert_data);  
    }  

    /*    $ext_en = pathinfo($_FILES['uploaded_en']['name'], PATHINFO_EXTENSION);
    $img_name_en = 'photos'.$_GET['category_id'].'_en_'.$saat.'.'.$ext_en;
    $target_path_en = $target_path.$img_name_en;
    if(move_uploaded_file($_FILES['uploaded_en']['tmp_name'], $target_path_en)){
    $sql = "UPDATE photos SET  file_en = '".$img_name_en."' WHERE id = '".$id."'";
    $q = mysql_query($sql);
    }    

    $ext_ru = pathinfo($_FILES['uploaded_ru']['name'], PATHINFO_EXTENSION);            
    $img_name_ru = 'photos'.$_GET['category_id'].'_ru_'.$saat.'.'.$ext_ru;
    $target_path_ru = $target_path.$img_name_ru;
    if(move_uploaded_file($_FILES['uploaded_ru']['tmp_name'], $target_path_ru)){
    $sql = "UPDATE photos SET  file_ru = '".$img_name_ru."' WHERE id = '".$id."'";
    $q = mysql_query($sql);
    } */

    $relink="?menu=photos&category_id=".$_GET['category_id'];
    echo '<script>document.location.href="'.$relink.'";</script>';               

}


if($_POST['edit']) {
    $id = addslashes($_GET['id']);
    $saat=date("YmdHis");
    $target_path = $desired_dir;
    $multimedia_info = $db_link->where ('id', $id)->getOne('photos');

    $ext_az = pathinfo($_FILES['uploaded_az']['name'], PATHINFO_EXTENSION);
    $img_name_az = 'photos'.$_GET['category_id'].'_az_'.$saat.'.'.$ext_az;
    $target_path_az = $target_path.$img_name_az;
    if(move_uploaded_file($_FILES['uploaded_az']['tmp_name'], $target_path_az)){
        @unlink($target_path.$multimedia_info['file_az']);
        $insert_data = array(
            'file_az' => $img_name_az
        );        
        $db_link->where('id', $id)->update('photos', $insert_data); 

    }  
    /*    $ext_en = pathinfo($_FILES['uploaded_en']['name'], PATHINFO_EXTENSION);
    $img_name_en = 'photos'.$_GET['category_id'].'_en_'.$saat.'.'.$ext_en;
    $target_path_en = $target_path.$img_name_en;
    if(move_uploaded_file($_FILES['uploaded_en']['tmp_name'], $target_path_en)){
    @unlink($target_path.$multimedia_info['file_en']);
    $sql = "UPDATE photos SET  file_en = '".$img_name_en."' WHERE id = '".$id."'";
    $q = mysql_query($sql);
    }    

    $ext_ru = pathinfo($_FILES['uploaded_ru']['name'], PATHINFO_EXTENSION);            
    $img_name_ru = 'photos'.$_GET['category_id'].'_ru_'.$saat.'.'.$ext_ru;
    $target_path_ru = $target_path.$img_name_ru;
    if(move_uploaded_file($_FILES['uploaded_ru']['tmp_name'], $target_path_ru)){
    @unlink($target_path.$multimedia_info['file_ru']);
    $sql = "UPDATE photos SET  file_ru = '".$img_name_ru."' WHERE id = '".$id."'";
    $q = mysql_query($sql);
    }*/             

    $insert_data = array(
        'description' => $_POST['description'],
        'name_az' => $_POST['name_az'],
        'name_en' => $_POST['name_en'],
        'name_ru' => $_POST['name_ru'],
        'link_az' => $_POST['link_az'],
        'link_en' => $_POST['link_en'],
        'link_ru' => $_POST['link_ru']
    );        
    $db_link->where('id', $id)->update('photos', $insert_data); 

    $relink="?menu=photos&category_id=".$_GET['category_id'];
    echo '<script>document.location.href="'.$relink.'";</script>';                
}

if ($_GET['tip']=='add_photos'){
    ?>
    <div class="col-lg-12">
        <div class="card card-primary mb-3">
            <div class="card-header">
                <?php print $tbl_category; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <form class="col-lg-12" role="form" name="file_edit" action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>File</label><input class="form-control" name="uploaded_az" type="file">
                            <label>Title az</label><input class="form-control" name="name_az" type="text" value="<?php print $multimedia_info['name_az'];?>">
                            <label>Title en</label><input class="form-control" name="name_en" type="text" value="<?php print $multimedia_info['name_en'];?>">
                            <label>Title ru</label><input class="form-control" name="name_ru" type="text" value="<?php print $multimedia_info['name_ru'];?>">
                            <label>Description az</label><input class="form-control" name="link_az" type="text" value="<?php print $multimedia_info['link_az'];?>">
                            <label>Description en</label><input class="form-control" name="link_en" type="text" value="<?php print $multimedia_info['link_en'];?>">
                            <label>Description ru</label><input class="form-control" name="link_ru" type="text" value="<?php print $multimedia_info['link_ru'];?>">
                            <label>Link</label><input class="form-control" name="description" type="text" value="<?php print $multimedia_info['description'];?>">
                        </div>
                        <br><center>
                            <input class="btn btn-primary" name="add" type="submit" id="edit" value="OK"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                        </center>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <?php
}

if ($_GET['tip']=='edit_photos'){
    $id = addslashes($_GET['id']);
    $multimedia_info = $db_link->where ('id', $id)->getOne('photos');

    ?>
    <div class="col-lg-12">
        <div class="card card-primary mb-3">
            <div class="card-header">
                <?php print $tbl_category; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <form class="col-lg-12" role="form" name="file_edit" action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php print $multimedia_info['id']?>" />                    
                        <div class="form-group">
                            <label>File</label><input class="form-control" name="uploaded_az" type="file"><?php print $multimedia_info['file_az'];?><br>
                            <label>Title az</label><input class="form-control" name="name_az" type="text" value="<?php print $multimedia_info['name_az'];?>">
                            <label>Title en</label><input class="form-control" name="name_en" type="text" value="<?php print $multimedia_info['name_en'];?>">
                            <label>Title ru</label><input class="form-control" name="name_ru" type="text" value="<?php print $multimedia_info['name_ru'];?>">

                            <label>Description az</label><input class="form-control" name="link_az" type="text" value="<?php print $multimedia_info['link_az'];?>">
                            <label>Description en</label><input class="form-control" name="link_en" type="text" value="<?php print $multimedia_info['link_en'];?>">
                            <label>Description ru</label><input class="form-control" name="link_ru" type="text" value="<?php print $multimedia_info['link_ru'];?>">
                            <label>Link</label><input class="form-control" name="description" type="text" value="<?php print $multimedia_info['description'];?>">

                        </div>
                        <br><center>
                            <input class="btn btn-primary" name="edit" type="submit" id="edit" value="OK"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                        </center>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <?php
}

if (empty($_GET['tip'])){
    ?>    
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header"> <?php print $tbl_category; ?>  </div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=photos&tip=add_photos&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>
                    </div>
                    <br>

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Title </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $photos_info = $db_link->where ('m_id', $category_id)->get('photos');
                                foreach ($photos_info as $photos) {                                    
                                    $id=$photos['id'];
                                    $file=$photos['file_az'];
                                    $name_az=$photos['name_az'];
                                    $name_en=$photos['name_en'];
                                    $name_ru=$photos['name_ru'];

                                    print "<tr class='odd gradeA'>
                                    <td>$file</td>
                                    <td>$name_az</td>
                                    <td class='center'>
                                    <a href='?menu=photos&category_id=$category_id&id=$id&tip=edit_photos'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=photos&tip=add_photos_file&fayl=$file&file_type=image&category_id=$category_id&id=$id&tip=delete_photos\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
                                    </td>
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
?>