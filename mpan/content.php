<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

if(!$security_test) exit;
$category_id=(int)$_GET['category_id'];
$cid=(int)$_GET['cid'];
$tip=(string)isset($_GET['tip'])?$_GET['tip']:"";
$desired_dir="../uploads/content/";
$tbl_category = $db_link->where("id", $cid)->getValue ("category", "name_az");

if ($_GET['tip']=='edit_content'){
    $id = addslashes($_GET['cid']);
    $content_info = $db_link->where ('id', $id)->getOne('content');

    if(!$_POST['edit']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category;?> https://<?php print $siteName;?>/az/content/<?php print $cid;?>.html
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="content_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $content_info['id']?>" />                    
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#az"  id="az-tab" data-toggle="tab" role="tab" aria-controls="az" aria-selected="true">Azerbaijani</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#en"  id="en-tab" data-toggle="tab" role="tab" aria-controls="en" aria-selected="true">English</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#ru"  id="ru-tab" data-toggle="tab" role="tab" aria-controls="ru" aria-selected="true">Russian</a>
                                </li>    
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="az" role="tabpanel" aria-labelledby="az-tab">
                                    <textarea id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($content_info['content_az'])?></textarea>
                                </div>

                                <div class="tab-pane fade" id="en" role="tabpanel" aria-labelledby="en-tab">
                                    <textarea id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($content_info['content_en'])?></textarea>
                                </div>

                                <div class="tab-pane fade" id="ru" role="tabpanel" aria-labelledby="ru-tab">
                                    <textarea id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($content_info['content_ru'])?></textarea>
                                </div> 

                            </div>
                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42">
                                    <?php
                                        print ($content_info['img'])?"<img src='".$desired_dir.$content_info['img']."' border=0 width=100> ".$desired_dir.$content_info['img']:"";
                                    ?>

                                </div>                            

                            <br><center>
                                <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok">
                            </center>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php
    } elseif($_POST['edit']) {

        $target_path = "../uploads/content/"; 
        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'content_'.$id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);  */
        } else{
            $img=''; 
        }

        if($img_name){
            $insert_data = array(
                'img' => $img
            );       
            $db_link->where('id', $_GET['cid'])->update('content', $insert_data);   
        }        

        $insert_data = array(
            'content_az' => $_POST['content_az'],
            'content_en' => $_POST['content_en'],
            'content_ru' => $_POST['content_ru']
        );       

        $db_link->where('id', $_GET['cid'])->update('content', $insert_data);

        print "<div class='alert alert-success alert-dismissable'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
        Məlumat dəyişdirildi. <a href='index.php?menu=content&tip=edit_content&cid=".$_GET['cid']."' class='alert-link'>Yenidən dəyişmək üçün vurun</a>
        </div>";

    }
}
?>