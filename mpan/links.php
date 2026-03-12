<?php
    if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
        header("Location: login.php");
        exit;
    }

    if( !(($_SESSION['flag'] & CUST_SUPERUSER) or ($_SESSION['flag'] & CUST_CONTENT)) ){
        echo "<h1>Access Denied";
        echo "<p>Sizin bu b&ouml;lm&#601;y&#601;y girm&#601;y&#601; haqq&#305;n&#305;z yoxdur</h1>";
        return;
    }
    if(!$security_test) exit;
    $category_id=$_GET['category_id']; 
    $id=$_GET['id']; 
?>
<?php
    if ($_GET['tip']==delete_links) {
        $sql_result=mysql_query("DELETE FROM links WHERE id ='".$_GET['id']."'");
        @unlink("../uploads/$project/links/".$_GET['fayl']);
        @mysql_free_result($sql_result);
        $relink="?menu=links&category_id=".$_GET['category_id'];
        echo '<script>document.location.href="'.$relink.'";</script>';  
    }

    if($_POST['add']) {

        $sql = "INSERT INTO links (m_id, name_az,name_en,name_ru,link_az,link_en,link_ru) VALUES ('".addslashes($_GET['category_id'])."', '".$_POST['name_az']."', '".$_POST['name_en']."', '".$_POST['name_ru']."', '".$_POST['link_az']."', '".$_POST['link_en']."', '".$_POST['link_ru']."')";
        $q = mysql_query($sql);  

        $q1 = mysql_query("show table status like 'links'") or die(mysql_error());
        $cat_id=mysql_result($q1, 0, 'Auto_increment');    $id=$cat_id-1;         

        $saat=date("YmdHis");
        $target_path = "../uploads/$project/links/";

        $ext_az = pathinfo($_FILES['uploaded_az']['name'], PATHINFO_EXTENSION);
        $img_name_az = 'image'.$_GET['category_id'].'_az_'.$saat.'.'.$ext_az;
        $target_path_az = $target_path.$img_name_az;
        if(move_uploaded_file($_FILES['uploaded_az']['tmp_name'], $target_path_az)){
            $sql = "UPDATE links SET  file_az = '".$img_name_az."' WHERE id = '".$id."'";
            $q = mysql_query($sql);
        }  
        $ext_en = pathinfo($_FILES['uploaded_en']['name'], PATHINFO_EXTENSION);
        $img_name_en = 'image'.$_GET['category_id'].'_en_'.$saat.'.'.$ext_en;
        $target_path_en = $target_path.$img_name_en;
        if(move_uploaded_file($_FILES['uploaded_en']['tmp_name'], $target_path_en)){
            $sql = "UPDATE links SET  file_en = '".$img_name_en."' WHERE id = '".$id."'";
            $q = mysql_query($sql);
        }    

        $ext_ru = pathinfo($_FILES['uploaded_ru']['name'], PATHINFO_EXTENSION);            
        $img_name_ru = 'image'.$_GET['category_id'].'_ru_'.$saat.'.'.$ext_ru;
        $target_path_ru = $target_path.$img_name_ru;
        if(move_uploaded_file($_FILES['uploaded_ru']['tmp_name'], $target_path_ru)){
            $sql = "UPDATE links SET  file_ru = '".$img_name_ru."' WHERE id = '".$id."'";
            $q = mysql_query($sql);
        }

        $relink="?menu=links&category_id=".$_GET['category_id'];
        echo '<script>document.location.href="'.$relink.'";</script>';               

    }


    if($_POST['edit']) {
        $id = addslashes($_GET['id']);
        $saat=date("YmdHis");
        $target_path = "../uploads/$project/links/";

        $ext_az = pathinfo($_FILES['uploaded_az']['name'], PATHINFO_EXTENSION);
        $img_name_az = 'image'.$_GET['category_id'].'_az_'.$saat.'.'.$ext_az;
        $target_path_az = $target_path.$img_name_az;
        if(move_uploaded_file($_FILES['uploaded_az']['tmp_name'], $target_path_az)){
            $sql = "UPDATE links SET  file_az = '".$img_name_az."' WHERE id = '".$id."'";
            $q = mysql_query($sql);
        }  
        $ext_en = pathinfo($_FILES['uploaded_en']['name'], PATHINFO_EXTENSION);
        $img_name_en = 'image'.$_GET['category_id'].'_en_'.$saat.'.'.$ext_en;
        $target_path_en = $target_path.$img_name_en;
        if(move_uploaded_file($_FILES['uploaded_en']['tmp_name'], $target_path_en)){
            $sql = "UPDATE links SET  file_en = '".$img_name_en."' WHERE id = '".$id."'";
            $q = mysql_query($sql);
        }    

        $ext_ru = pathinfo($_FILES['uploaded_ru']['name'], PATHINFO_EXTENSION);            
        $img_name_ru = 'image'.$_GET['category_id'].'_ru_'.$saat.'.'.$ext_ru;
        $target_path_ru = $target_path.$img_name_ru;
        if(move_uploaded_file($_FILES['uploaded_ru']['tmp_name'], $target_path_ru)){
            $sql = "UPDATE links SET  file_ru = '".$img_name_ru."' WHERE id = '".$id."'";
            $q = mysql_query($sql);
        }             

        $sql1 = "UPDATE links SET link_az = '".$_POST['link_az']."' , link_ru = '".$_POST['link_ru']."' , link_en = '".$_POST['link_en']."' ,name_az = '".$_POST['name_az']."' , name_ru = '".$_POST['name_ru']."' , name_en = '".$_POST['name_en']."' WHERE id = '".$id."' and project='$project'"; 
        $q1 = mysql_query($sql1);               
        $relink="?menu=links&category_id=".$_GET['category_id'];
        //echo '<script>document.location.href="'.$relink.'";</script>';                
        
        if($q1){
                print "<div class='alert alert-success alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
                Məlumat dəyişdirildi.
                </div>";
        }else{
            die('Sehf var: ' . mysql_error());
        }                
    }

?>
<?php
    if ($_GET['tip']==add_links){
    ?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php print @mysql_result(mysql_query("SELECT `name_en` FROM category where `id`='".$category_id."'",$db_link), 0, 0); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" name="file_edit" action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php print $multimedia_info['id']?>" />                    
                        <ul class="nav nav-tabs">
                                <li class="active"><a href="#az" data-toggle="tab">Azerbaijani</a></li>
                                <li><a href="#en" data-toggle="tab">English</a></li>
								<li><a href="#ru" data-toggle="tab">Russian</a></li>
                        </ul>

                        <div class="tab-content">
                        <div class="tab-pane fade in active" id="az">
                            <div class="form-group">
                                <label>File</label><input class="form-control" name="uploaded_az" type="file">
                                <label>Link</label><input class="form-control"  name="link_az" type="text" value="<?php print $multimedia_info['link_az'];?>">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="en">
                            <div class="form-group">
                                <label>File</label><input class="form-control" name="uploaded_en" type="file">
                                <label>Link</label><input class="form-control"  name="link_en" type="text" value="<?php print $multimedia_info['link_en'];?>">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="ru">
                            <div class="form-group">
                                <label>File</label><input class="form-control" name="uploaded_ru" type="file">
                                <label>Link</label><input class="form-control"  name="link_ru" type="text" value="<?php print $multimedia_info['link_ru'];?>">
                            </div>
                        </div>
                        <br><center>
                            <input class="btn btn-primary" name="add" type="submit" id="edit" value="Daxil et"> <input  class="btn btn-primary" type=button value="Geri" onclick="javascript:history.go(-1);">
                        </center>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <?php
    }

    if ($_GET['tip']==edit_links){
        $id = addslashes($_GET['id']);
        $sql = "SELECT * FROM links WHERE id = '$id' and project='$project' LIMIT 1";
        $q = mysql_query($sql);
        $multimedia_info = mysql_fetch_array($q);   
    ?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php print @mysql_result(mysql_query("SELECT `name_en` FROM category where `id`='".$id."' and project='$project'",$db_link), 0, 0); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" name="file_edit" action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php print $multimedia_info['id']?>" />                    
                        <ul class="nav nav-tabs">
                                <li class="active"><a href="#az" data-toggle="tab">Azerbaijani</a></li>
                                <li><a href="#en" data-toggle="tab">English</a></li>
								<li><a href="#ru" data-toggle="tab">Russian</a></li>
                        </ul>

                        <div class="tab-content">
                        <div class="tab-pane fade in active" id="az">
                            <div class="form-group">
                                <label>File</label><input class="form-control" name="uploaded_az" type="file"><?php print $multimedia_info['file_az'];?><br>
                                <label>Link</label><input class="form-control"  name="link_az" type="text" value="<?php print $multimedia_info['link_az'];?>">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="en">
                            <div class="form-group">
                                <label>File</label><input class="form-control" name="uploaded_en" type="file"><?php print $multimedia_info['file_en'];?><br>
                                <label>Link</label><input class="form-control"  name="link_en" type="text" value="<?php print $multimedia_info['link_en'];?>">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="ru">
                            <div class="form-group">
                                <label>File</label><input class="form-control" name="uploaded_ru" type="file"><?php print $multimedia_info['file_ru'];?><br>
                                <label>Link</label><input class="form-control"  name="link_ru" type="text" value="<?php print $multimedia_info['link_ru'];?>">
                            </div>
                        </div>
                        <br><center>
                            <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok"> 
							<input class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
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
            <div class="panel panel-default">
                <div class="panel-heading"> <?php print @mysql_result(mysql_query("SELECT `name_az` FROM category where `id`='".$id."' and project='$project'",$db_link), 0, 0); ?>  </div>
                <div class="panel-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=links&tip=add_links&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Yenisini daxil et</a>
                        </div>
                    </div>
                    <br>

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Link AZ</th>
                                    <th>Link EN</th>
                                    <th>Link RU</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT * FROM links where m_id='$category_id' ORDER BY sira";
                                    $q = mysql_query($sql);
                                    while($links = mysql_fetch_array($q)) {
                                        $id=$links['id'];
                                        $file=$links['file_az'];
                                        $name_az=$links['link_az'];
                                        $name_en=$links['link_en'];
										$name_ru=$links['link_ru'];

                                        print "<tr class='odd gradeA'>
                                        <td>$file</td>
                                        <td>$name_az</td>
                                        <td>$name_en</td>
                                        <td>$name_ru</td>
                                        <td class='center'>
                                        <a href='?menu=links&category_id=$category_id&id=$id&tip=edit_links'><span class='glyphicon glyphicon-pencil'></span></a>
                                        <a href='?menu=links&tip=add_links_file&fayl=$file&file_type=image&category_id=$category_id&id=$id&tip=delete_links'><span class='glyphicon glyphicon-trash'></span></a>
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