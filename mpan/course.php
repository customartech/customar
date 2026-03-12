<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

//if ($_GET['tip']==) $news_date=$news_info['news_date']; else $news_date=date('Y-m-d');
$category_id=$_GET['category_id'];


$desired_dir="../uploads/course/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
}
$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

if (empty($_GET['tip'])){
    ?>
    <style>
        #response { padding:10px;  background-color:#9F9; border:2px solid #396;  margin-bottom:20px; }
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
                    $.post("updatecourse.php", order, function(theResponse){
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
            <div class="card card-primary mb-3">
                <div class="card-header"> <?php print $tbl_category; ?>  </div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=course&tip=add_course&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>
                    </div>
                    <br>
                    <div id="response"> </div>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listKatalog">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Tədbirlər </th>
                                    <!--<th>KonfransÄ±n ProqramÄ±</th>  -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $news_info = $db_link->where ('category_id', $category_id)->get('course');
                                foreach ($news_info as $news) {
                                    $id = stripslashes($news['id']);
                                    $news_date = stripslashes($news['news_date']);
                                    $name_az = stripslashes($news['title_az']);
                                    $name_en = stripslashes($news['title_en']);
                                    $name_ru = stripslashes($news['title_ru']);

                                    $tedbir_qeyd = $db_link->where("category_id", $id)->getValue ("tedbir", "count(id)");

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td style='cursor:move'>$news_date</td>
                                    <td>$name_az</td>
                                    <td>$tedbir_qeyd</td>
                                    <td class='center'>
                                    <a href='?menu=course&tip=edit_course&category_id=$category_id&cid=$id'><span class='fa fa-pencil'></span></a>
                                    <a href='?menu=tedbir&category_id=$id'><span class='fa fa-clone'></span></a>
                                    <a onclick='Del(\"?menu=course&tip=delete_course&category_id=$category_id&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tip']==delete_course){
    $id = addslashes($_GET['cid']);
    $news_img = $db_link->where("id", $id)->getValue ("course", "img");
    $news_info = mysql_fetch_array($q);
    @unlink($desired_dir.$news_img);
    @mysql_free_result($q);

    $db_link->where('id',$id)->delete('course');
    echo '<script>document.location.href="?menu=course&category_id='.$category_id.'";</script>';
}



if ($_GET['tip']==edit_course){
    $id = addslashes($_GET['cid']);
    $news_info = $db_link->where ('id', $id)->getOne('course');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="course_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $news_info['id']?>" />                    


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
                                <div class="tab-pane fade in show active" id="az">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($news_info['title_az'])?>'>
                                        <label>Kursun təsviri</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($news_info['content_az'])?></textarea>
                                        <label>Kursun xülasəsi</label><textarea class="form-control" id="content_az2" name="content_az2" rows="15" cols="80"><?php print stripcslashes($news_info['content_az2'])?></textarea>
                                        <label>Şərtlər</label><textarea class="form-control" id="content_az3" name="content_az3" rows="15" cols="80"><?php print stripcslashes($news_info['content_az3'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($news_info['title_en'])?>'>
                                        <label>>Kursun təsviri</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($news_info['content_en'])?></textarea>
                                        <label>Kursun xülasəsi</label><textarea class="form-control" id="content_en2" name="content_en2" rows="15" cols="80"><?php print stripcslashes($news_info['content_en2'])?></textarea>
                                        <label>Şərtlər</label><textarea class="form-control" id="content_en3" name="content_en3" rows="15" cols="80"><?php print stripcslashes($news_info['content_en3'])?></textarea>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($news_info['title_ru'])?>'>
                                        <label>>Kursun təsviri</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru'])?></textarea>
                                        <label>Kursun xülasəsi</label><textarea class="form-control" id="content_ru2" name="content_ru2" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru2'])?></textarea>
                                        <label>Şərtlər</label><textarea class="form-control" id="content_ru3" name="content_ru3" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru3'])?></textarea>

                                    </div>
                                </div> 

                                <div class="row col-lg-12">

                                    <div class="form-group col-lg-12">
                                        <label> Sertifikat</label><input class="form-control" type="text" name="sertfikat" size="22" value='<?php print addslashes($news_info['sertfikat'])?>'>
                                    </div>
                                                                    
                                    <!--<div class="form-group col-lg-3">
                                        <label>Date</label><input class="form-control" type="text" name="news_date" size="22" value='<?php print addslashes($news_info['news_date'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label>Vaxt</label><input class="form-control" type="text" name="vaxt" size="22" value='<?php print addslashes($news_info['vaxt'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label> İştirakçı sayı</label><input class="form-control" type="text" name="isayi" size="22" value='<?php print addslashes($news_info['isayi'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label> Dil</label><input class="form-control" type="text" name="dil" size="22" value='<?php print addslashes($news_info['dil'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label> Müddəti</label><input class="form-control" type="text" name="muddet" size="22" value='<?php print addslashes($news_info['muddet'])?>'>
                                    </div>-->


                                    <div class="form-group col-lg-12">
                                        <label>Image</label> <input class="form-control" type="file" name="sekil" size="42">
                                        <?php
                                        print ($news_info['img'])?"<img src='".$desired_dir.$news_info['img']."' border=0 width=100> ".$desired_dir.$news_info['img']:"";
                                        ?>

                                    </div>                                    
                                </div>
                            </div>

                            <br><center>
                                <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>

                </div>

            </div>
        </div>

        <?php
    } 
    elseif($_POST['edit']) {
        $target_path = "../uploads/course/$category_id/"; 
        $insert_data = array(
            'news_date' => $_POST['news_date'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_az2' => $_POST['content_az2'],
            'content_az3' => $_POST['content_az3'],
            'content_ru' => $_POST['content_ru'],
            'content_ru2' => $_POST['content_ru2'],
            'content_ru3' => $_POST['content_ru3'],
            'content_en' => $_POST['content_en'],
            'content_en2' => $_POST['content_en2'],
            'content_en3' => $_POST['content_en3'],
            'vaxt' => $_POST['vaxt'],
            'isayi' => $_POST['isayi'],
            'sertfikat' => $_POST['sertfikat'],
            'dil' => $_POST['dil'],
            'muddet' => $_POST['muddet']
        );       
        $db_link->where('id', $id)->update('course', $insert_data); 
        $target_path = $desired_dir; 
        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'course_'.$_GET['category_id'].'_'.$id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);  */

            $insert_data = array('img' => $img );       
            $db_link->where('id', $id)->update('course', $insert_data); 
        } else{
            $img=''; 
        }

        echo '<script>document.location.href="?menu=course&category_id='.$category_id.'";</script>';


    }
}

if ($_GET['tip']==add_course){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="course_edit" action="" method="post" enctype="multipart/form-data">


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
                                <div class="tab-pane fade in show active" id="az">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($news_info['title_az'])?>'>
                                        <label>Kursun təsviri</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($news_info['content_az'])?></textarea>
                                        <label>Kursun xülasəsi</label><textarea class="form-control" id="content_az2" name="content_az2" rows="15" cols="80"><?php print stripcslashes($news_info['content_az2'])?></textarea>
                                        <label>Şərtlər</label><textarea class="form-control" id="content_az3" name="content_az3" rows="15" cols="80"><?php print stripcslashes($news_info['content_az3'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($news_info['title_en'])?>'>
                                        <label>>Kursun təsviri</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($news_info['content_en'])?></textarea>
                                        <label>Kursun xülasəsi</label><textarea class="form-control" id="content_en2" name="content_en2" rows="15" cols="80"><?php print stripcslashes($news_info['content_en2'])?></textarea>
                                        <label>Şərtlər</label><textarea class="form-control" id="content_en3" name="content_en3" rows="15" cols="80"><?php print stripcslashes($news_info['content_en3'])?></textarea>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($news_info['title_ru'])?>'>
                                        <label>>Kursun təsviri</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru'])?></textarea>
                                        <label>Kursun xülasəsi</label><textarea class="form-control" id="content_ru2" name="content_ru2" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru2'])?></textarea>
                                        <label>Şərtlər</label><textarea class="form-control" id="content_ru3" name="content_ru3" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru3'])?></textarea>

                                    </div>
                                </div> 

                                <div class="row col-lg-12">

                                    <div class="form-group col-lg-12">
                                        <label> Sertifikat</label><input class="form-control" type="text" name="sertfikat" size="22" value='<?php print addslashes($news_info['sertfikat'])?>'>
                                    </div>                                
                                    <!--<div class="form-group col-lg-3">
                                        <label>Date</label><input class="form-control" type="text" name="news_date" size="22" value='<?php print addslashes($news_info['news_date'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label>Vaxt</label><input class="form-control" type="text" name="vaxt" size="22" value='<?php print addslashes($news_info['vaxt'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label> İştirakçı sayı</label><input class="form-control" type="text" name="isayi" size="22" value='<?php print addslashes($news_info['isayi'])?>'>
                                    </div>


                                    <div class="form-group col-lg-3">
                                        <label> Dil</label><input class="form-control" type="text" name="dil" size="22" value='<?php print addslashes($news_info['dil'])?>'>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label> Müddəti</label><input class="form-control" type="text" name="muddet" size="22" value='<?php print addslashes($news_info['muddet'])?>'>
                                    </div> -->


                                    <div class="form-group col-lg-12">
                                        <label>Image</label> <input class="form-control" type="file" name="sekil" size="42">
                                        <?php
                                        print ($news_info['img'])?"<img src='".$desired_dir.$news_info['img']."' border=0 width=100> ".$desired_dir.$news_info['img']:"";
                                        ?>

                                    </div>                                    
                                </div>
                            </div>

                            <br>
                            <center>
                                <input class="btn btn-primary" name="add" type="submit" id="add" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
        $insert_data = array(
            'news_date' => $_POST['news_date'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_az2' => $_POST['content_az2'],
            'content_az3' => $_POST['content_az3'],
            'content_ru' => $_POST['content_ru'],
            'content_ru2' => $_POST['content_ru2'],
            'content_ru3' => $_POST['content_ru3'],
            'content_en' => $_POST['content_en'],
            'content_en2' => $_POST['content_en2'],
            'content_en3' => $_POST['content_en3'],
            'vaxt' => $_POST['vaxt'],
            'isayi' => $_POST['isayi'],
            'sertfikat' => $_POST['sertfikat'],
            'category_id' => $_GET['category_id'],
            'dil' => $_POST['dil'],
            'muddet' => $_POST['muddet']
        );       
        $id=$db_link->insert ('course', $insert_data);
        if($id){ 
            $target_path = $desired_dir; 
            $img_name = $_FILES['sekil']['name']; 
            $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
            if($img_name) {
                $img_name = 'course_'.$_GET['category_id'].'_'.$id.'_1.'.$ext;
                @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
                $img=$img_name;
                /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
                $imgg->best_fit(600, 600)->save($target_path.$img_name);  */
                $insert_data = array('img' => $img);       
                $db_link->where('id', $id)->update('course', $insert_data);            
            } else{
                $img=''; 
            }
        }

        echo '<script>document.location.href="?menu=course&category_id='.$category_id.'";</script>';

    }
}
?>