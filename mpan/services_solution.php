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
                <div class="card-header"><a href="?menu=services_solution&tipi=add_services_solution&category_id=<?php print $category_id;?>&cid=<?php print $cid;?>" type="button" class="btn btn-outline btn-primary">Add new</a></div>
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
                                $news_info = $db_link->get('services_solution');
                                foreach ($news_info as $news) {
                                    $id = stripslashes($news['id']);
                                    $name_az = stripslashes($news['title_az']);

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td>$name_az</td>
                                    <td class='center'>
                                    <a href='?menu=services_solution&tipi=edit_services_solution&category_id=$category_id&cid=$cid&id=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=services_solution&tipi=delete_services_solution&category_id=$category_id&cid=$cid&id=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tipi']=='delete_services_solution'){
    $id = addslashes($_GET['id']);
    $db_link->where('id',$id)->delete('services_solution');
    $relink="?menu=services_solution";
    echo '<script>document.location.href="'.$relink.'";</script>';
}



if ($_GET['tipi']=='edit_services_solution'){
    $id = addslashes($_GET['id']);
    $news_info = $db_link->where ('id', $id)->getOne('services_solution');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="services_solution_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $news_info['id']?>" />
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade in show active" id="az">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Industry</label>
                                            <?php
                                            combo_industry($services_info['industry'],$db_link);
                                            ?>
                                        </div>                                    
                                        <label>Title AZ</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($news_info['title_az'])?>'>
                                        <label>Title EN</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($news_info['title_en'])?>'>
                                        <label>Title RU</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($news_info['title_ru'])?>'>
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
            'services_industry_id' => $_POST['industry'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en']
        );
        $db_link->where('id', $id)->update('services_solution', $insert_data);
        $relink="?menu=services_solution";
        echo '<script>document.location.href="'.$relink.'";</script>';
    }
}

if ($_GET['tipi']=='add_services_solution'){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <?php print $tbl_category; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="services_solution_edit" action="" method="post" enctype="multipart/form-data">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="az">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Industry</label>
                                            <?php
                                            combo_industry($services_info['industry'],$db_link);
                                            ?>
                                        </div>                                    
                                        <label>Title AZ</label><input class="form-control" type="text" name="title_az" size="107" value='<?php print stripslashes($news_info['title_az'])?>'>
                                        <label>Title EN</label><input class="form-control" type="text" name="title_en" size="107" value='<?php print stripslashes($news_info['title_en'])?>'>
                                        <label>Title RU</label><input class="form-control" type="text" name="title_ru" size="107" value='<?php print stripslashes($news_info['title_ru'])?>'>
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
            'services_industry_id' => $_POST['industry'],
            'title_az' => $_POST['title_az'],
            'title_ru' => $_POST['title_ru'],
            'title_en' => $_POST['title_en']
        );
        $db_link->insert ('services_solution', $insert_data);
        $relink="?menu=services_solution";
        echo '<script>document.location.href="'.$relink.'";</script>';

    }
}
?>