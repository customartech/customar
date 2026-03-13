<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

//if ($_GET['tip']==) $news_date=$news_info['news_date']; else $news_date=date('Y-m-d');
$category_id=$_GET['category_id'];

if (isset($_POST['add_client_quick'])) {
    $return_url = $_SERVER['REQUEST_URI'] ?? ('?menu=xeber&category_id='.(int)$category_id);

    $client_name = trim((string)($_POST['client_name'] ?? ''));
    if ($client_name === '') {
        echo '<script>document.location.href="'.$return_url.'";</script>';
        exit;
    }

    $partners_category_id = 5;
    $desired_dir_partners = "../uploads/photos/$partners_category_id/";
    if (is_dir($desired_dir_partners) == false) {
        mkdir($desired_dir_partners, 0755);
    }

    $insert_data = array(
        'm_id' => $partners_category_id,
        'name_az' => $client_name,
        'name_en' => '',
        'name_ru' => '',
        'link_az' => '',
        'link_en' => '',
        'link_ru' => '',
        'description' => ''
    );
    $new_id = $db_link->insert('photos', $insert_data);

    if (!empty($new_id) && isset($_FILES['client_logo']) && !empty($_FILES['client_logo']['name'])) {
        $saat = date('YmdHis');
        $ext = pathinfo($_FILES['client_logo']['name'], PATHINFO_EXTENSION);
        $img_name = 'photos'.$partners_category_id.'_az_'.$saat.'.'.$ext;
        $target_path = $desired_dir_partners.$img_name;
        if (move_uploaded_file($_FILES['client_logo']['tmp_name'], $target_path)) {
            $db_link->where('id', (int)$new_id)->update('photos', array('file_az' => $img_name));
        }
    }

    $sep = (strpos($return_url, '?') !== false) ? '&' : '?';
    $redirect = $return_url.$sep.'new_client='.urlencode($client_name);
    echo '<script>document.location.href="'.$redirect.'";</script>';
    exit;
}


$desired_dir="../uploads/news/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
}
$tbl_category = $db_link->where("id", $category_id)->getValue ("category", "name_az");

if (empty($_GET['tip'])){
    $news_info = $db_link->orderBy("news_date","desc")->where ('category_id', $category_id)->get('news');
    ?>
<!--    <style>
        #response { padding:10px;  background-color:#9F9; border:2px solid #396;  margin-bottom:20px; }
    </style> -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header"> <?php print $tbl_category; ?>  </div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=xeber&tip=add_xeber&category_id=<?php print $category_id;?>" type="button" class="btn btn-outline btn-primary">Add new</a>
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
                                foreach ($news_info as $news) {
                                    $id = stripslashes($news['id']);
                                    $news_date = stripslashes($news['news_date']);
                                    $name_az = stripslashes($news['title_az']);
                                    $name_en = stripslashes($news['title_en']);
                                    $name_ru = stripslashes($news['title_ru']);  

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td>$news_date</td>
                                    <td>$name_az</td>
                                    <td>$name_en</td>
                                    <td>$name_ru</td>
                                    <td class='center'>
                                    <a href='?menu=xeber&tip=edit_xeber&category_id=$category_id&cid=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=xeber&tip=delete_xeber&category_id=$category_id&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tip']=='delete_xeber'){
    $id = addslashes($_GET['cid']);
    $news_img = $db_link->where("id", $id)->getValue ("news", "img");
    //$news_info = mysql_fetch_array($q);
    @unlink($desired_dir.$news_img);

    $db_link->where('id',$id)->delete('news');
    echo '<script>document.location.href="?menu=xeber&category_id='.$category_id.'";</script>';
}



if ($_GET['tip']=='edit_xeber'){
    $id = addslashes($_GET['cid']);
    $news_info = $db_link->where ('id', $id)->getOne('news');

    $client_options = $db_link->where('m_id', 5)->orderBy('name_az', 'asc')->get('photos', null, ['name_az']);

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="xeber_edit" action="" method="post" enctype="multipart/form-data">
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
                                        <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($news_info['content_az'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($news_info['title_en'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($news_info['content_en'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($news_info['title_ru'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru'])?></textarea>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label>Service</label>
                                    <?php
                                    combo_service($news_info['service'],$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="news_date" size="22" value='<?php print addslashes($news_info['news_date'])?>'>
                                </div>
                                <div class="form-group">
                                    <label>Client</label>
                                    <select class="form-control select21" name="client">
                                        <?php
                                        $current_client = trim((string)($_GET['new_client'] ?? ($news_info['client'] ?? '')));
                                        $seen_clients = [];
                                        if ($current_client !== '') {
                                            $safe_current = htmlspecialchars($current_client, ENT_QUOTES, 'UTF-8');
                                            $seen_clients[mb_strtolower($current_client, 'UTF-8')] = true;
                                            print "<option value=\"$safe_current\" selected=\"selected\">$safe_current</option>";
                                        } else {
                                            print "<option value=\"\" selected=\"selected\"></option>";
                                        }
                                        if (!empty($client_options) && is_array($client_options)) {
                                            foreach ($client_options as $opt) {
                                                $name = trim((string)($opt['name_az'] ?? ''));
                                                if ($name === '') continue;
                                                $key = mb_strtolower($name, 'UTF-8');
                                                if (isset($seen_clients[$key])) continue;
                                                $seen_clients[$key] = true;
                                                $safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                                                print "<option value=\"$safe\">$safe</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary mt-2" data-toggle="modal" data-target="#quickClientModal">+</button>
                                </div>

                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42">
                                    <?php
                                    print ($news_info['img'])?"<img src='".$desired_dir.$news_info['img']."' border=0 width=100> ".$desired_dir.$news_info['img']:"";
                                    ?>

                                </div>
                            </div>

                            <br><center>
                                <input class="btn btn-primary" name="edit" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="quickClientModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add client</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Client name</label>
                                        <input class="form-control" type="text" name="client_name" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Logo</label>
                                        <input class="form-control" type="file" name="client_logo" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" name="add_client_quick" value="1">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
    } 
    elseif($_POST['edit']) {
        //include('SimpleImage.php'); 
        //$tarix=Date("Ymdhis");

        $target_path = "../uploads/news/$category_id/"; 
        $img_name = $_FILES['sekil']['name']; 
        $img_name1 = $_FILES['sekil1']['name']; 
        $img_name2 = $_FILES['sekil2']['name']; 
        $img_name3 = $_FILES['sekil3']['name']; 
        $img_name4 = $_FILES['sekil4']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'news_'.$_GET['category_id'].'_'.$id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);  */
        } else{
            $img=''; 
        }

        if($img_name1) {
            $img_name1 = 'news_'.$_GET['category_id'].'_'.$id.'_2.jpg';
            @move_uploaded_file($_FILES['sekil1']['tmp_name'], $target_path.$img_name1);
            $img1=$img_name1;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name1);
            $imgg->best_fit(600, 600)->save($target_path.$img_name1); */
        } else{
            $img1=''; 
        }

        if($img_name2) {
            $img_name2 = 'news_'.$_GET['category_id'].'_'.$id.'_3.jpg';
            @move_uploaded_file($_FILES['sekil2']['tmp_name'], $target_path.$img_name2);
            $img2=$img_name2;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name2);
            $imgg->best_fit(600, 600)->save($target_path.$img_name2); */
        } else{
            $img2=''; 
        }

        if($img_name3) {
            $img_name3 = 'news_'.$_GET['category_id'].'_'.$id.'_4.jpg';
            @move_uploaded_file($_FILES['sekil3']['tmp_name'], $target_path.$img_name3);
            $img3=$img_name3;
            $imgg = new abeautifulsite\SimpleImage($target_path.$img_name3);
            $imgg->best_fit(600, 600)->save($target_path.$img_name3);
        } else{
            $img3=''; 
        }

        if($img_name4) {
            $img_name4 = 'news_'.$_GET['category_id'].'_'.$id.'_5.jpg';
            @move_uploaded_file($_FILES['sekil4']['tmp_name'], $target_path.$img_name4);
            $img4=$img_name4;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name4);
            $imgg->best_fit(600, 600)->save($target_path.$img_name4); */
        } else{
            $img4=''; 
        }

        $insert_data = array(
            'news_date' => $_POST['news_date'],
            'service' => $_POST['service'],
            'client' => $_POST['client'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_ru' => $_POST['content_ru'],
            'content_en' => $_POST['content_en']
        );       
        $db_link->where('id', $id)->update('news', $insert_data); 

        if($img_name){
            $insert_data = array(
                'img' => $img
            );       
            $db_link->where('id', $id)->update('news', $insert_data);   
        }

        if($img_name1){
            $insert_data = array(
                'img1' => $img1
            );       
            $db_link->where('id', $id)->update('news', $insert_data);
        }

        if($img_name2){
            $insert_data = array(
                'img2' => $img2
            );       
            $db_link->where('id', $id)->update('news', $insert_data);
        }

        if($img_name3){
            $insert_data = array(
                'img3' => $img3
            );       
            $db_link->where('id', $id)->update('news', $insert_data);
        }

        if($img_name4){
            $insert_data = array(
                'img4' => $img4
            );       
            $db_link->where('id', $id)->update('news', $insert_data);
        }

        echo '<script>document.location.href="?menu=xeber&category_id='.$category_id.'";</script>';


    }
}

if ($_GET['tip']=='add_xeber'){
    if(!$_POST['add']) {
        $client_options = $db_link->where('m_id', 5)->orderBy('name_az', 'asc')->get('photos', null, ['name_az']);
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="xeber_edit" action="" method="post" enctype="multipart/form-data">
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
                                        <label>Title</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($news_info['title_az'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_az" name="content_az" rows="15" cols="80"><?php print stripcslashes($news_info['content_az'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="en">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($news_info['title_en'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_en" name="content_en" rows="15" cols="80"><?php print stripcslashes($news_info['content_en'])?></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ru">
                                    <div class="form-group">
                                        <label>Title</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($news_info['title_ru'])?>'>
                                        <label>Content</label><textarea class="form-control" id="content_ru" name="content_ru" rows="15" cols="80"><?php print stripcslashes($news_info['content_ru'])?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Service</label>
                                    <?php
                                    combo_service(0,$db_link);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Client</label>
                                    <select class="form-control select21" name="client">
                                        <?php
                                        $current_client = trim((string)($_GET['new_client'] ?? ''));
                                        $seen_clients = [];
                                        if ($current_client !== '') {
                                            $safe_current = htmlspecialchars($current_client, ENT_QUOTES, 'UTF-8');
                                            $seen_clients[mb_strtolower($current_client, 'UTF-8')] = true;
                                            print "<option value=\"$safe_current\" selected=\"selected\">$safe_current</option>";
                                        } else {
                                            print "<option value=\"\" selected=\"selected\"></option>";
                                        }
                                        if (!empty($client_options) && is_array($client_options)) {
                                            foreach ($client_options as $opt) {
                                                $name = trim((string)($opt['name_az'] ?? ''));
                                                if ($name === '') continue;
                                                $key = mb_strtolower($name, 'UTF-8');
                                                if (isset($seen_clients[$key])) continue;
                                                $seen_clients[$key] = true;
                                                $safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                                                print "<option value=\"$safe\">$safe</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary mt-2" data-toggle="modal" data-target="#quickClientModal">+</button>
                                </div>
                                <div class="form-group">
                                    <label>Date</label><input class="form-control" type="text" name="news_date" size="22" value='<?php print date('Y-m-d')?>'>
                                </div>

                                <div class="form-group">
                                    <label>Image</label> <input class="form-control" type="file" name="sekil" size="42"> <?php print ($news_info['img'])?"<img src='/uploads/images/".$news_info['img']."' border=0 width=50> /uploads/images/".$news_info['img']:"";?>
                                </div>
                            </div>

                            <br>
                            <center>
                                <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="quickClientModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add client</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Client name</label>
                                        <input class="form-control" type="text" name="client_name" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Logo</label>
                                        <input class="form-control" type="file" name="client_logo" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" name="add_client_quick" value="1">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
        //include('SimpleImage.php');

        $news_id = $db_link->where("category_id", $category_id)->getValue ("news", "max(id)")+1;          
        $target_path = "../uploads/news/$category_id/"; 

        $img_name = $_FILES['sekil']['name']; 
        $img_name1 = $_FILES['sekil1']['name']; 
        $img_name2 = $_FILES['sekil2']['name']; 
        $img_name3 = $_FILES['sekil3']['name']; 
        $img_name4 = $_FILES['sekil4']['name']; 
        $ext = pathinfo($_FILES['sekil']['name'], PATHINFO_EXTENSION);
        if($img_name) {
            $img_name = 'news_'.$_GET['category_id'].'_'.$news_id.'_1.'.$ext;
            @move_uploaded_file($_FILES['sekil']['tmp_name'], $target_path.$img_name);
            $img=$img_name;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name);
            $imgg->best_fit(600, 600)->save($target_path.$img_name);*/
        } else{
            $img=''; 
        }

        if($img_name1) {
            $img_name1 = 'news_'.$_GET['category_id'].'_'.$news_id.'_2.jpg';
            @move_uploaded_file($_FILES['sekil1']['tmp_name'], $target_path.$img_name1);
            $img1=$img_name1;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name1);
            $imgg->best_fit(600, 600)->save($target_path.$img_name1);*/
        } else{
            $img1=''; 
        }

        if($img_name2) {
            $img_name2 = 'news_'.$_GET['category_id'].'_'.$news_id.'_3.jpg';
            @move_uploaded_file($_FILES['sekil2']['tmp_name'], $target_path.$img_name2);
            $img2=$img_name2;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name2);
            $imgg->best_fit(600, 600)->save($target_path.$img_name2);*/
        } else{
            $img2=''; 
        }


        if($img_name3) {
            $img_name3 = 'news_'.$_GET['category_id'].'_'.$news_id.'_4.jpg';
            @move_uploaded_file($_FILES['sekil3']['tmp_name'], $target_path.$img_name3);
            $img3=$img_name3;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name3);
            $imgg->best_fit(600, 600)->save($target_path.$img_name3);*/
        } else{
            $img3=''; 
        }


        if($img_name4) {
            $img_name4 = 'news_'.$_GET['category_id'].'_'.$news_id.'_5.jpg';
            @move_uploaded_file($_FILES['sekil4']['tmp_name'], $target_path.$img_name4);
            $img4=$img_name4;
            /*$imgg = new abeautifulsite\SimpleImage($target_path.$img_name4);
            $imgg->best_fit(600, 600)->save($target_path.$img_name4);*/
        } else{
            $img4=''; 
        }

        $insert_data = array(
            'news_date' => $_POST['news_date'],
            'service' => $_POST['service'],
            'client' => $_POST['client'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_ru' => $_POST['content_ru'],
            'content_en' => $_POST['content_en'],
            'category_id' => $_GET['category_id'],
            'img' => $img,
            'img1' => $img1,
            'img2' => $img2,
            'img3' => $img3,
            'img4' => $img4
        );       
        $db_link->insert ('news', $insert_data); 
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=xeber&category_id='.$category_id.'";</script>';

    }
}
?>