<?php
if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
    header("Location: login.php");
    exit;
}
if(!$security_test) exit;
if( !(($_SESSION['flag'] & CUST_SUPERUSER) or ($_SESSION['flag'] & CUST_CONTENT)) ){
    echo "<h1>Access Denied";
    echo "<p>Sizin bu b&ouml;lm&#601;y&#601;y girm&#601;y&#601; haqq&#305;n&#305;z yoxdur</h1>";
    return;
}

if ($_GET['tip']==edit_bloks) $bloks_date=$bloks_info['bloks_date']; else $bloks_date=date('Y-m-d');
$category_id=$_GET['category_id'];

if (empty($_GET['tip'])){

    $sql = "SELECT * FROM bloks ORDER BY id Desc";
    $q = mysql_query($sql);
    ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"> Bloklar  </div>
                <div class="panel-body">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Block</th>
                                    <th>Title AZ</th>
                                    <th>Title EN</th>
									<th>Title RU</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($news = mysql_fetch_array($q)) {
                                    $id = stripslashes($news['id']);
                                    $name_az = stripslashes($news['title_az']);
                                    $name_en = stripslashes($news['title_en']);
									$name_ru = stripslashes($news['title_ru']);

                                    print "<tr class='odd gradeA'>
                                    <td>Blok $id</td>
                                    <td>$name_az</td>
                                    <td>$name_en</td>
									<td>$name_ru</td>
                                    <td class='center'>
                                    <a href='?menu=bloks&tip=edit_bloks&category_id=$category_id&cid=$id'><span class='glyphicon glyphicon-pencil'></span></a>
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

if ($_GET['tip']==edit_bloks){
    $id = addslashes($_GET['cid']);
    $sql = "SELECT * FROM bloks WHERE id = '$id'";
    $q = mysql_query($sql);
    $bloks_info = mysql_fetch_array($q);

    if(!$_POST['edit']) {
        ?>


        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php print @mysql_result(mysql_query("SELECT `name_az` FROM category where `id`='".$category_id."'",$db_link), 0, 0); ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form role="form" name="bloks_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $bloks_info['id']?>" />                    
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#az" data-toggle="tab">Azerbaijani</a></li>
                                <li><a href="#en" data-toggle="tab">English</a></li>
								<li><a href="#ru" data-toggle="tab">Russian</a></li>
                            </ul>

                            <div class="tab-content">
                            <div class="tab-pane fade in active" id="az">
                                <div class="form-group">
                                    <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($bloks_info['title_az'])?>'>
                                    <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($bloks_info['content_az'])?></textarea>
                                    <label>Link</label><input class="form-control" type="text" name="link_az" size="22" value='<?php print addslashes($bloks_info['link_az'])?>'>

                                </div>
                            </div>

 
                            <div class="tab-pane fade" id="en">
                                <div class="form-group">
                                    <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($bloks_info['title_en'])?>'>
                                    <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($bloks_info['content_en'])?></textarea>
                                    <label>Link</label><input class="form-control" type="text" name="link_en" size="22" value='<?php print addslashes($bloks_info['link_en'])?>'>

                                </div>
                            </div>

							<div class="tab-pane fade" id="ru">
                                <div class="form-group">
                                    <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($bloks_info['title_ru'])?>'>
                                    <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($bloks_info['content_ru'])?></textarea>
                                    <label>Link</label><input class="form-control" type="text" name="link_ru" size="22" value='<?php print addslashes($bloks_info['link_ru'])?>'>

                                </div>
                            </div>

                            <div class="form-group">
                                <label>Image</label> 
								<input class="form-control" type="file" name="sekil" size="42"> 
								<?php print ($bloks_info['img'])?"/uploads/bloks/".$bloks_info['img']:"";?>
                            </div>

                            <br><center>
                                <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok"> 
								<input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>
                </div>

            </div>
        </div>        
        <?php
    } elseif($_POST['edit']) {
        $target_path = "../uploads/bloks/"; 
        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'bloks_'.$_GET['category_id'].'_'.$id.'_'.$project.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
        } else{
            $img=''; 
        }

        $sql = "UPDATE bloks SET link_az = '".$_POST['link_az']."', link_ru = '".$_POST['link_ru']."', link_en = '".$_POST['link_en']."', title_az = '".$_POST['title_az']."', title_en = '".$_POST['title_en']."', title_ru = '".$_POST['title_ru']."', content_az= '".$_POST['content_az']."', content_ru = '".$_POST['content_ru']."', content_en = '".$_POST['content_en']."'  WHERE id = '$id' and project='$project'";
        $q = mysql_query($sql); 

        if($img_name){
            $sql = "UPDATE bloks SET img = '".$img."' WHERE id = '$id' and project='$project'";
            $q = mysql_query($sql);
        }
        echo '<script>document.location.href="?menu=bloks&category_id='.$category_id.'";</script>';
    }
}
?>
<script>
    CKEDITOR.replace( 'content_az' , {
        removePlugins: 'newpage,elementspath,save',
        extraPlugins: 'wysiwygarea',    
        filebrowserBrowseUrl : 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>',
        filebrowserUploadUrl : 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>',
        filebrowserImageBrowseUrl : 'filemanager/dialog.php?type=1&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>'
    });
    CKEDITOR.replace( 'content_en' , {
        removePlugins: 'newpage,elementspath,save',
        extraPlugins: 'wysiwygarea',    
        filebrowserBrowseUrl : 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>',
        filebrowserUploadUrl : 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>',
        filebrowserImageBrowseUrl : 'filemanager/dialog.php?type=1&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>'
    });
    CKEDITOR.replace( 'content_ru' , { 
        removePlugins: 'newpage,elementspath,save',
        extraPlugins: 'wysiwygarea',    
        filebrowserBrowseUrl : 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>',
        filebrowserUploadUrl : 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>',
        filebrowserImageBrowseUrl : 'filemanager/dialog.php?type=1&editor=ckeditor&fldr=&akey=<?php print $_SESSION['access_key'];?>'
    });
</script>  