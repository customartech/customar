<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

//if ($_GET['tip']==) $services_date=$services_info['services_date']; else $services_date=date('Y-m-d');
$category_id=$_GET['category_id'];


$desired_dir="../uploads/services/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
}
$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

if (empty($_GET['tip'])){
    $services_info = $db_link->orderBy("sira","asc")->where ('category_id', $category_id)->get('services');
    ?>
    <style>
        #response { padding:10px;  background-color:#9F9; border:2px solid #396;  margin-bottom:20px; }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header"> <?php print $tbl_category; ?>  </div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=services&tip=add_services&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>
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
                                    <th>Title EN</th>
                                    <th>Title RU</th> 
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($services_info as $services) {
                                    $id = stripslashes($services['id']);
                                    $services_date = stripslashes($services['services_date']);
                                    $name_az = stripslashes($services['title_az']);
                                    $name_en = stripslashes($services['title_en']);
                                    $name_ru = stripslashes($services['title_ru']);  

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td style='cursor:move'>$services_date</td>
                                    <td>$name_az</td>
                                    <td>$name_en</td>
                                    <td>$name_ru</td>
                                    <td class='center'>
                                    <a href='?menu=services&tip=edit_services&category_id=$category_id&cid=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=services&tip=delete_services&category_id=$category_id&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tip']==delete_services){
    $id = addslashes($_GET['cid']);
    $services_img = $db_link->where("id", $id)->getValue ("services", "img");
    //$services_info = mysql_fetch_array($q);
    @unlink($desired_dir.$services_img);
    $db_link->where('id',$id)->delete('services');
    echo '<script>document.location.href="?menu=services&category_id='.$category_id.'";</script>';
}



if ($_GET['tip']==edit_services){
    $id = addslashes($_GET['cid']);
    $services_info = $db_link->where ('id', $id)->getOne('services');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="services_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $services_info['id']?>" />                    
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
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($services_info['title_az'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($services_info['content_az'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($services_info['title_en'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($services_info['content_en'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($services_info['title_ru'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($services_info['content_ru'])?></textarea>
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label>Service</label>
                                    <?php
                                    combo_service($services_info['service'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Industry</label>
                                    <?php
                                    combo_industry($services_info['industry'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Solution</label>
                                    <?php
                                    combo_solution($services_info['solution'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="services_date" size="22" value='<?php print addslashes($services_info['services_date'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="services_date" size="22" value='<?php print addslashes($services_info['services_date'])?>'>
                                </div>

                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42">
                                    <?php
                                        print ($services_info['img'])?"<img src='".$desired_dir.$services_info['img']."' border=0 width=100> ".$desired_dir.$services_info['img']:"";
                                    ?>

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
        //include('SimpleImage.php'); 
        //$tarix=Date("Ymdhis");

        $target_path = "../uploads/services/$category_id/"; 
        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'services_'.$_GET['category_id'].'_'.$id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);  */
        } else{
            $img=''; 
        }

        $insert_data = array(
            'services_date' => $_POST['services_date'],
            'industry' => $_POST['industry'],
            'service' => $_POST['service'],
            'solution' => $_POST['solution'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_ru' => $_POST['content_ru'],
            'content_en' => $_POST['content_en']
        );       
        $db_link->where('id', $id)->update('services', $insert_data); 

        if($img_name){
            $insert_data = array(
                'img' => $img
            );       
            $db_link->where('id', $id)->update('services', $insert_data);   
        }
       echo '<script>document.location.href="?menu=services&category_id='.$category_id.'";</script>';
    }
}

if ($_GET['tip']==add_services){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="services_edit" action="" method="post" enctype="multipart/form-data">
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
                                <div class="tab-pane fade show active" id="az">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($services_info['title_az'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($services_info['content_az'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($services_info['title_en'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($services_info['content_en'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($services_info['title_ru'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($services_info['content_ru'])?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Service</label>
                                    <?php
                                    combo_service($services_info['service'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Industry</label>
                                    <?php
                                    combo_industry($services_info['industry'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Solution</label>
                                    <?php
                                    combo_solution($services_info['solution'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="services_date" size="22" value='<?php print date('Y-m-d')?>'>
                                </div>

                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42"> <?php print ($services_info['img'])?"<img src='/uploads/services/".$services_info['img']."' border=0 width=50> /uploads/services/".$services_info['img']:"";?>
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
        //include('SimpleImage.php');

        $services_id = $db_link->where("category_id", $category_id)->getValue ("services", "max(id)")+1;          
        $target_path = "../uploads/services/$category_id/"; 

        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'services_'.$_GET['category_id'].'_'.$services_id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);*/
        } else{
            $img=''; 
        }

        $insert_data = array(
            'services_date' => $_POST['services_date'],
            'industry' => $_POST['industry'],
            'service' => $_POST['service'],
            'solution' => $_POST['solution'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_ru' => $_POST['content_ru'],
            'content_en' => $_POST['content_en'],
            'category_id' => $_GET['category_id'],
            'img' => $img
        );       
        $db_link->insert ('services', $insert_data); 
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=services&category_id='.$category_id.'";</script>';

    }
}
?>