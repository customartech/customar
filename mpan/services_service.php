<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$category_id=$_GET['category_id'];
$cid=$_GET['cid'];
$tbl_category = $db_link->getValue ("category", "name_az");

if (empty($_GET['tipi'])){
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header"><a href="?menu=services_service&tipi=add_services_service&category_id=<?php print $category_id;?>&cid=<?php print $cid;?>" type="button" class="btn btn-outline btn-primary">Add new</a></div>
                <div class="card-body">
                    <div id="response"> </div>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $news_info = $db_link->get('services_service');
                                foreach ($news_info as $news) {
                                    $id = stripslashes($news['id']);
                                    $name_az = stripslashes($news['title_az']);

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td>$name_az</td>
                                    <td class='center'>
                                    <a href='?menu=services_service&tipi=edit_services_service&category_id=$category_id&cid=$cid&id=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=services_service&tipi=delete_services_service&category_id=$category_id&cid=$cid&id=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tipi']=='delete_services_service'){
    $id = addslashes($_GET['id']);
    $db_link->where('id',$id)->delete('services_service');
    $relink="?menu=services_service";
    echo '<script>document.location.href="'.$relink.'";</script>';
}



if ($_GET['tipi']=='edit_services_service'){
    $id = addslashes($_GET['id']);
    $news_info = $db_link->where ('id', $id)->getOne('services_service');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="services_service_edit" action="" method="post" enctype="multipart/form-data">
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
        $insert_data = array(
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_en' => $_POST['content_en'],
            'content_ru' => $_POST['content_ru']
        );
        $db_link->where('id', $id)->update('services_service', $insert_data);
        $relink="?menu=services_service";
        echo '<script>document.location.href="'.$relink.'";</script>';
    }
}

if ($_GET['tipi']=='add_services_service'){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="services_service_edit" action="" method="post" enctype="multipart/form-data">
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
        $insert_data = array(
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en'],
            'content_az' => $_POST['content_az'],
            'content_en' => $_POST['content_en'],
            'content_ru' => $_POST['content_ru']
        );
        $db_link->insert ('services_service', $insert_data);
        $relink="?menu=services_service";
        echo '<script>document.location.href="'.$relink.'";</script>';

    }
}
?>