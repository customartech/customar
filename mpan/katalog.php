<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

if(!$security_test) exit;

if ($_GET['tip']==edit_katalog) $content_date=$katalog_info['content_date']; else $content_date=date('Y-m-d');
$category_id=$_GET['category_id'];

$tip='';
$category_id='';
$category_id=(int)$_GET['category_id'];
$cid=(int)$_GET['cid'];
$tip=(string)isset($_GET['tip'])?$_GET['tip']:"";
$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

$desired_dir="../uploads/katalog/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
}

if (empty($_GET['tip'])){
    ?>
    <style>
        ul {padding:0px; margin: 0px;}
        #response { padding:10px;  background-color:#9F9; border:2px solid #396;  margin-bottom:20px; }
        #listKatalog li { margin: 0 0 3px; padding:5px; background-color:#DCE2E0; color:#000; list-style: none;text-align: left; font-weight: bold; }
        #listKatalog ul li { margin: 0 0 3px; padding:5px; background-color:#DCE2E0; color:#000; list-style: none;text-align: left; font-weight: bold; }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){     
            function slideout(){
                setTimeout(function(){
                    $("#response").slideUp("slow", function () {  }); }, 2000);
            }
            $("#response").hide();
            $(function() {
                $("#listKatalog tbody").sortable({ opacity: 0.8, cursor: 'move', update: function() {
                    var order = $(this).sortable("serialize") + '&update=update'; 
                    $.post("updatekatalog.php", order, function(theResponse){
                        $("#response").html(theResponse);
                        $("#response").slideDown('slow');
                        slideout();
                    });                                                              
                    }                                  
                });
            });

        });    
    </script>    
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header"> <?php print $tbl_category; ?>  </div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=katalog&tip=add_katalog&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>
                    </div>
                    <br>
                    <div id="response"> </div>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listKatalog">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title Az</th>
                                    <th>Title Ru</th> 
                                    <th>Title En</th> 
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $tbl_katalog = $db_link->where('category_id',$category_id)->get('katalog');
                                foreach ($tbl_katalog as $katalog) {
                                    $id = stripslashes($katalog['id']);
                                    $content_date = stripslashes($katalog['content_date']);
                                    $name_az = stripslashes($katalog['name_az']);
                                    $name_en = stripslashes($katalog['name_en']);
                                    $name_ru = stripslashes($katalog['name_ru']);
                                    $qiymet = stripslashes($katalog['qiymet']);

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td style='cursor:move'>$content_date</td>
                                    <td>$name_az</td>
                                    <td>$name_ru</td>
                                    <td>$name_en</td>
                                    <td>$qiymet</td>
                                    <td class='center'>
                                    <a href='?menu=katalog&tip=edit_katalog&category_id=$category_id&cid=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=katalog&tip=delete_katalog&category_id=$category_id&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
                                    </td>
                                    </tr></li>";
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


if ($_GET['tip']==delete_katalog){
    $id = addslashes($_GET['cid']);
    $db_link->where('id',$id)->delete('katalog');
    echo '<script>document.location.href="?menu=katalog&category_id='.$category_id.'";</script>';
}


if ($_GET['tip']==edit_katalog){
    $id = addslashes($_GET['cid']);
    $katalog_info = $db_link->where ('id', $id)->getOne('katalog');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="katalog_edit" action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php print $katalog_info['id']?>" />                    
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
                                <label>Title</label><input class="form-control" type="text" name="name_az" size="107" value="<?php print stripslashes($katalog_info['name_az'])?>">
                                <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($katalog_info['content_az'])?></textarea>
                            </div>

                            <div class="tab-pane fade" id="en" role="tabpanel" aria-labelledby="en-tab">
                                <label>Title</label><input class="form-control" type="text" name="name_en" size="107" value="<?php print stripslashes($katalog_info['name_en'])?>">
                                <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($katalog_info['content_en'])?></textarea>
                            </div>

                            <div class="tab-pane fade" id="ru" role="tabpanel" aria-labelledby="ru-tab">
                                <label>Title</label><input class="form-control" type="text" name="name_ru" size="107" value="<?php print stripslashes($katalog_info['name_ru'])?>">
                                <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($katalog_info['content_ru'])?></textarea>
                            </div> 
                        </div> 

                        <div class="form-group">
                            <label>Qiymet</label><input class="form-control" type="text" name="qiymət" size="107" value='<?php print stripslashes($katalog_info['qiymet'])?>'>
                        </div>

                    </div>
<!--                    <div class="form-group">
                        <label>Kategory<span class="required">*</span></label>
                        <div class="select-wrapper">
                            <select required class="form-control" id="category" name="category">
                                <option value="0">- - - - -</option>
                                <?php
                                $query = "SELECT * FROM `kcategory` order by title_az asc";
                                $result = mysql_query ($query,$db_link);
                                while ($line = mysql_fetch_array($result)){
                                    $id = stripslashes($line["id"]);
                                    $name = stripslashes($line["title_az"]);
                                    print "<option id='$id' value='$id'>$name</option>";
                                }
                                @mysql_free_result($result);
                                ?>
                            </select>
                            <?php print  "<script> katalog_edit.category.value='".$katalog_info['category']."'; </script>"; ?>
                        </div>
                    </div> -->                            

                    <div class="form-group">
                        <label>Image 1</label> <input class="form-control" type="file" name="sekil" size="42"> <?php print ($katalog_info['img1'])?"<img src='".$desired_dir.$katalog_info['img1']."' border=0 width=50> ".$desired_dir.$katalog_info['img1']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 2</label> <input class="form-control" type="file" name="sekil1" size="42"> <?php print ($katalog_info['img2'])?"<img src='".$desired_dir.$katalog_info['img2']."' border=0 width=50> ".$desired_dir.$katalog_info['img2']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 3</label> <input class="form-control" type="file" name="sekil2" size="42"> <?php print ($katalog_info['img3'])?"<img src='".$desired_dir.$katalog_info['img3']."' border=0 width=50> ".$desired_dir.$katalog_info['img3']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 4</label> <input class="form-control" type="file" name="sekil3" size="42"> <?php print ($katalog_info['img4'])?"<img src='".$desired_dir.$katalog_info['img4']."' border=0 width=50> ".$desired_dir.$katalog_info['img4']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 5</label> <input class="form-control" type="file" name="sekil4" size="42"> <?php print ($katalog_info['img5'])?"<img src='".$desired_dir.$katalog_info['img5']."' border=0 width=50> ".$desired_dir.$katalog_info['img5']:"";?>
                    </div>

                    <br><center>
                        <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                    </center>
                    </form>
                </div>
            </div>          

            <!--<label>Type and Price</label>-->
            <?php
            //include "ktypeprice.php";
            ?>

        </div>


        <?php
    } 
    elseif($_POST['edit']) {
        include('SimpleImage.php'); 
        //$tarix=Date("Ymdhis");
        $target_path = "../uploads/katalog/$category_id/";
        $img_name = $_FILES['sekil']['name']; 
        $img_name1 = $_FILES['sekil1']['name']; 
        $img_name2 = $_FILES['sekil2']['name']; 
        $img_name3 = $_FILES['sekil3']['name']; 
        $img_name4 = $_FILES['sekil4']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'katalog_'.$id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            $imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(580, 580)->save($target_path.$img_name);
        } else{
            $img=''; 
        }
        $ext1 = pathinfo($_FILES['sekil1']['name'], PATHINFO_EXTENSION);
        if($img_name1) {
            $img_name1 = 'katalog_'.$id.'_2.'.$ext1;
            @move_uploaded_file($_FILES['sekil1']['tmp_name'], $target_path.$img_name1);
            $img1=$img_name1;
            $imgg1 = new abeautifulsite\SimpleImage($target_path.$img_name1);
            $imgg1->best_fit(580, 580)->save($target_path.$img_name1);
        } else{
            $img1=''; 
        }
        $ext2 = pathinfo($_FILES['sekil2']['name'], PATHINFO_EXTENSION);
        if($img_name2) {
            $img_name2 = 'katalog_'.$id.'_3.'.$ext2;
            @move_uploaded_file($_FILES['sekil2']['tmp_name'], $target_path.$img_name2);
            $img2=$img_name2;
            $imgg2 = new abeautifulsite\SimpleImage($target_path.$img_name2);
            $imgg2->best_fit(580, 580)->save($target_path.$img_name2);
        } else{
            $img2=''; 
        }

        $ext3 = pathinfo($_FILES['sekil3']['name'], PATHINFO_EXTENSION);
        if($img_name3) {
            $img_name3 = 'katalog_'.$id.'_4.'.$ext3;
            @move_uploaded_file($_FILES['sekil3']['tmp_name'], $target_path.$img_name3);
            $img3=$img_name3;
            $imgg3 = new abeautifulsite\SimpleImage($target_path.$img_name3);
            $imgg3->best_fit(580, 580)->save($target_path.$img_name3);
        } else{
            $img3=''; 
        }

        $ext4 = pathinfo($_FILES['sekil4']['name'], PATHINFO_EXTENSION);
        if($img_name4) {
            $img_name4 = 'katalog_'.$id.'_5.'.$ext4;
            @move_uploaded_file($_FILES['sekil4']['tmp_name'], $target_path.$img_name4);
            $img4=$img_name4;
            $imgg4 = new abeautifulsite\SimpleImage($target_path.$img_name4);
            $imgg4->best_fit(580, 580)->save($target_path.$img_name4);
        } else{
            $img4=''; 
        }

        $insert_data = array(
            'name_az' => $_POST['name_az'],
            'name_ru' => $_POST['name_ru'],
            'name_en' => $_POST['name_en'],
            'content_az' => $_POST['content_az'],
            'content_en' => $_POST['content_en'],
            'content_ru' => $_POST['content_ru'],
            'category' => $_POST['category'],
            'category_id' => $_POST['category_id'],
            'qiymet' => $_POST['qiymet']
        );       

        $db_link->where('id', $id)->update('katalog', $insert_data);        

        if($img_name){
            $insert_data = array(
                'img1' => $img
            );       
            $db_link->where('id', $id)->update('katalog', $insert_data);
        }

        if($img_name1){
            $insert_data = array(
                'img2' => $img1
            );       
            $db_link->where('id', $id)->update('katalog', $insert_data);
        }

        if($img_name2){
            $insert_data = array(
                'img3' => $img2
            );       
            $db_link->where('id', $id)->update('katalog', $insert_data);
        }

        if($img_name3){
            $insert_data = array(
                'img4' => $img3
            );       
            $db_link->where('id', $id)->update('katalog', $insert_data);
        }

        if($img_name4){
            $insert_data = array(
                'img5' => $img4
            );       
            $db_link->where('id', $id)->update('katalog', $insert_data);
        }
        echo '<script>document.location.href="?menu=katalog&category_id='.$category_id.'";</script>';
    }
}

if ($_GET['tip']==add_katalog){
    if(!$_POST['add']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="katalog_add" action="" method="post" enctype="multipart/form-data">
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
                                <label>Title</label><input class="form-control" type="text" name="name_az" size="107" value="<?php print stripslashes($katalog_info['name_az'])?>">
                                <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($katalog_info['content_az'])?></textarea>
                            </div>

                            <div class="tab-pane fade" id="en" role="tabpanel" aria-labelledby="en-tab">
                                <label>Title</label><input class="form-control" type="text" name="name_en" size="107" value="<?php print stripslashes($katalog_info['name_en'])?>">
                                <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($katalog_info['content_en'])?></textarea>
                            </div>

                            <div class="tab-pane fade" id="ru" role="tabpanel" aria-labelledby="ru-tab">
                                <label>Title</label><input class="form-control" type="text" name="name_ru" size="107" value="<?php print stripslashes($katalog_info['name_ru'])?>">
                                <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($katalog_info['content_ru'])?></textarea>
                            </div> 
                        </div> 

                        <div class="form-group">
                            <label>Qiymet</label><input class="form-control" type="text" name="qiymət" size="107" value='<?php print stripslashes($katalog_info['qiymet'])?>'>
                        </div>

                    </div>
<!--                    <div class="form-group">
                        <label>Kategory<span class="required">*</span></label>
                        <div class="select-wrapper">
                            <select required class="form-control" id="category" name="category">
                                <option value="0">- - - - -</option>
                                <?php
                                $query = "SELECT * FROM `kcategory` order by title_az asc";
                                $result = mysql_query ($query,$db_link);
                                while ($line = mysql_fetch_array($result)){
                                    $id = stripslashes($line["id"]);
                                    $name = stripslashes($line["title_az"]);
                                    print "<option id='$id' value='$id'>$name</option>";
                                }
                                @mysql_free_result($result);
                                ?>
                            </select>
                            <?php print  "<script> katalog_edit.category.value='".$katalog_info['category']."'; </script>"; ?>
                        </div>
                    </div>-->                             

                    <div class="form-group">
                        <label>Image 1</label> <input class="form-control" type="file" name="sekil" size="42"> <?php print ($katalog_info['img1'])?"<img src='".$desired_dir.$katalog_info['img1']."' border=0 width=50> ".$desired_dir.$katalog_info['img1']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 2</label> <input class="form-control" type="file" name="sekil1" size="42"> <?php print ($katalog_info['img2'])?"<img src='".$desired_dir.$katalog_info['img2']."' border=0 width=50> ".$desired_dir.$katalog_info['img2']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 3</label> <input class="form-control" type="file" name="sekil2" size="42"> <?php print ($katalog_info['img3'])?"<img src='".$desired_dir.$katalog_info['img3']."' border=0 width=50> ".$desired_dir.$katalog_info['img3']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 4</label> <input class="form-control" type="file" name="sekil3" size="42"> <?php print ($katalog_info['img4'])?"<img src='".$desired_dir.$katalog_info['img4']."' border=0 width=50> ".$desired_dir.$katalog_info['img4']:"";?>
                    </div>
                    <div class="form-group">
                        <label>Image 5</label> <input class="form-control" type="file" name="sekil4" size="42"> <?php print ($katalog_info['img5'])?"<img src='".$desired_dir.$katalog_info['img5']."' border=0 width=50> ".$desired_dir.$katalog_info['img5']:"";?>
                    </div>

                    <br><center>
                        <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                    </center>
                    </form>
                </div>
            </div>          

            <!--<label>Type and Price</label>-->
            <?php
            //include "ktypeprice.php";
            ?>

        </div>        

        <?php
    } 
    elseif($_POST['add']) {
        include('SimpleImage.php');
        $q1 = mysql_query("show table status like 'katalog'") or die(mysql_error());
        $katalog_id=mysql_result($q1, 0, 'Auto_increment');            
        $target_path = "../uploads/katalog/$category_id/"; 
        $img_name = $_FILES['sekil']['name']; 
        $img_name1 = $_FILES['sekil1']['name']; 
        $img_name2 = $_FILES['sekil2']['name']; 
        $img_name3 = $_FILES['sekil3']['name']; 
        $img_name4 = $_FILES['sekil4']['name']; 

        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'katalog_'.$katalog_id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            $imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(580, 580)->save($target_path.$img_name);
        } else{
            $img=''; 
        }
        $ext1 = pathinfo($_FILES['sekil1']['name'], PATHINFO_EXTENSION);
        if($img_name1) {
            $img_name1 = 'katalog_'.$katalog_id.'_2.'.$ext1;
            @move_uploaded_file($_FILES['sekil1']['tmp_name'], $target_path.$img_name1);
            $img1=$img_name1;
            $imgg1 = new abeautifulsite\SimpleImage($target_path.$img_name1);
            $imgg1->best_fit(580, 580)->save($target_path.$img_name1);
        } else{
            $img1=''; 
        }
        $ext2 = pathinfo($_FILES['sekil2']['name'], PATHINFO_EXTENSION);
        if($img_name2) {
            $img_name2 = 'katalog_'.$katalog_id.'_3.'.$ext2;
            @move_uploaded_file($_FILES['sekil2']['tmp_name'], $target_path.$img_name2);
            $img2=$img_name2;
            $imgg2 = new abeautifulsite\SimpleImage($target_path.$img_name2);
            $imgg2->best_fit(580, 580)->save($target_path.$img_name2);
        } else{
            $img2=''; 
        }

        $ext3 = pathinfo($_FILES['sekil3']['name'], PATHINFO_EXTENSION);
        if($img_name3) {
            $img_name3 = 'katalog_'.$katalog_id.'_4.'.$ext3;
            @move_uploaded_file($_FILES['sekil3']['tmp_name'], $target_path.$img_name3);
            $img3=$img_name3;
            $imgg3 = new abeautifulsite\SimpleImage($target_path.$img_name3);
            $imgg3->best_fit(580, 580)->save($target_path.$img_name3);
        } else{
            $img3=''; 
        }

        $ext4 = pathinfo($_FILES['sekil4']['name'], PATHINFO_EXTENSION);
        if($img_name4) {
            $img_name4 = 'katalog_'.$katalog_id.'_5.'.$ext4;
            @move_uploaded_file($_FILES['sekil4']['tmp_name'], $target_path.$img_name4);
            $img4=$img_name4;
            $imgg4 = new abeautifulsite\SimpleImage($target_path.$img_name4);
            $imgg4->best_fit(580, 580)->save($target_path.$img_name4);
        } else{
            $img4=''; 
        }

        $insert_data = array(
            'name_az' => $_POST['name_az'],
            'name_ru' => $_POST['name_ru'],
            'name_en' => $_POST['name_en'],
            'content_az' => $_POST['content_az'],
            'content_en' => $_POST['content_en'],
            'content_ru' => $_POST['content_ru'],
            'category' => $_POST['category'],
            'category_id' => $_POST['category_id'],
            'img1' => $img,
            'img2' => $img1,
            'img3' => $img2,
            'img4' => $img3,
            'img5' => $img4,
            'qiymet' => $_POST['qiymet']
        );       

        $db_link->insert('katalog', $insert_data);

        //print $sql;
        echo '<script>document.location.href="?menu=katalog&category_id='.$category_id.'";</script>';
    }
}
?>