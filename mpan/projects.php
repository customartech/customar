<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

//if ($_GET['tip']==) $projects_date=$projects_info['projects_date']; else $projects_date=date('Y-m-d');
$category_id=$_GET['category_id'];


$desired_dir="../uploads/projects/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
}
$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

if (empty($_GET['tip'])){
    $projects_info = $db_link->orderBy("sira","desc")->where ('category_id', $category_id)->get('projects');
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
                            <a href="?menu=projects&tip=add_projects&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>
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
                                foreach ($projects_info as $projects) {
                                    $id = stripslashes($projects['id']);
                                    $projects_date = stripslashes($projects['projects_date']);
                                    $name_az = stripslashes($projects['title_az']);
                                    $name_en = stripslashes($projects['title_en']);
                                    $name_ru = stripslashes($projects['title_ru']);  

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td style='cursor:move'>$projects_date</td>
                                    <td>$name_az</td>
                                    <td>$name_en</td>
                                    <td>$name_ru</td>
                                    <td class='center'>
                                    <a href='?menu=projects&tip=edit_projects&category_id=$category_id&cid=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=projects&tip=delete_projects&category_id=$category_id&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tip']==delete_projects){
    $id = addslashes($_GET['cid']);
    $projects_img = $db_link->where("id", $id)->getValue ("projects", "img");
    @unlink($desired_dir.$projects_img);
    $db_link->where('id',$id)->delete('projects');
    echo '<script>document.location.href="?menu=projects&category_id='.$category_id.'";</script>';
}



if ($_GET['tip']==edit_projects){
    $id = addslashes($_GET['cid']);
    $projects_info = $db_link->where ('id', $id)->getOne('projects');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="projects_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $projects_info['id']?>" />                    
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
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($projects_info['title_az'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($projects_info['content_az'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($projects_info['title_en'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($projects_info['content_en'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($projects_info['title_ru'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($projects_info['content_ru'])?></textarea>
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label>Latitude</label><input class="form-control" type="text" name="latitude" size="22" value='<?php print addslashes($projects_info['latitude'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Longitude</label><input class="form-control" type="text" name="longitude" size="22" value='<?php print addslashes($projects_info['longitude'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="projects_date" size="22" value='<?php print addslashes($projects_info['projects_date'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="projects_date" size="22" value='<?php print addslashes($projects_info['projects_date'])?>'>
                                </div>

                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42">
                                    <?php
                                        print ($projects_info['img'])?"<img src='".$desired_dir.$projects_info['img']."' border=0 width=100> ".$desired_dir.$projects_info['img']:"";
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

        $target_path = "../uploads/projects/$category_id/"; 
        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'projects_'.$_GET['category_id'].'_'.$id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);  */
        } else{
            $img=''; 
        }

        $insert_data = array(
            'projects_date' => $_POST['projects_date'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_ru' => $_POST['content_ru'],
            'content_en' => $_POST['content_en']
        );       
        $db_link->where('id', $id)->update('projects', $insert_data); 

        if($img_name){
            $insert_data = array(
                'img' => $img
            );       
            $db_link->where('id', $id)->update('projects', $insert_data);   
        }
       echo '<script>document.location.href="?menu=projects&category_id='.$category_id.'";</script>';
    }
}

if ($_GET['tip']==add_projects){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="projects_edit" action="" method="post" enctype="multipart/form-data">
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
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($projects_info['title_az'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($projects_info['content_az'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($projects_info['title_en'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($projects_info['content_en'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($projects_info['title_ru'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($projects_info['content_ru'])?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Latitude</label><input class="form-control" type="text" name="latitude" size="22" value='<?php print addslashes($projects_info['latitude'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Longitude</label><input class="form-control" type="text" name="longitude" size="22" value='<?php print addslashes($projects_info['longitude'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="projects_date" size="22" value='<?php print date('Y-m-d')?>'>
                                </div>

                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42"> <?php print ($projects_info['img'])?"<img src='/uploads/images/".$projects_info['img']."' border=0 width=50> /uploads/images/".$projects_info['img']:"";?>
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

        $projects_id = $db_link->where("category_id", $category_id)->getValue ("projects", "max(id)")+1;          
        $target_path = "../uploads/projects/$category_id/"; 

        $img_name = $_FILES['sekil']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'projects_'.$_GET['category_id'].'_'.$projects_id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);*/
        } else{
            $img=''; 
        }

        $insert_data = array(
            'projects_date' => $_POST['projects_date'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_ru' => $_POST['content_ru'],
            'content_en' => $_POST['content_en'],
            'category_id' => $_GET['category_id'],
            'img' => $img
        );       
        $db_link->insert ('projects', $insert_data); 
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=projects&category_id='.$category_id.'";</script>';

    }
}
?>