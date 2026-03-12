<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

//if ($_GET['tip']==) $news_date=$news_info['news_date']; else $news_date=date('Y-m-d');
$category_id=$_GET['category_id'];


$desired_dir="../uploads/menuconfig/$category_id/";
if(is_dir($desired_dir)==false){
    mkdir($desired_dir, 0755);
}

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
                    $.post("updatenews.php", order, function(theResponse){
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
                <div class="card-header">menuconfig</div>
                <div class="card-body">

                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=menuconfig&tip=add_menuconfig" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>
                    </div>
                    <br>
                    <div id="response"> </div>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listKatalog">
                            <thead>
                                <tr>
                                    <th>Metbex</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $news_info = $db_link->get('menuconfig');
                                foreach ($news_info as $news) {
                                    $id = stripslashes($news['id']);
                                    $metbex = stripslashes($news['metbex']);
                                    $ceki = stripslashes($news['ceki']);  

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td>$metbex</td>
                                    <td class='center'>
                                    <a href='?menu=menuconfig&tip=edit_menuconfig&cid=$id'><span class='fa fa-pencil'></span></a>
                                    <a onclick='Del(\"?menu=menuconfig&tip=delete_menuconfig&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
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


if ($_GET['tip']==delete_menuconfig){
    $id = addslashes($_GET['cid']);
    $db_link->where('id',$id)->delete('menuconfig');
    echo '<script>document.location.href="?menu=menuconfig";</script>';
}



if ($_GET['tip']==edit_menuconfig){
    $id = addslashes($_GET['cid']);
    $news_info = $db_link->where ('id', $id)->getOne('menuconfig');

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <a href="index.php?menu=course&category_id=<?php print $tbl_category_ust;?>"><?php print $tbl_category; ?></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="menuconfig_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $news_info['id']?>" />                    
                            <div class="tab-content" id="myTabContent">
                                <div class="row col-lg-12">
                                    <div class="form-group col-lg-6">
                                        <label>metbex</label>
                                        <?php
                                        combo_metbex($news_info['metbex'],$db_link);
                                        ?>
                                    </div> 

                                    <div class="form-group col-lg-6">
                                        <label>Ceki</label><input class="form-control" type="text" name="ceki" size="22" value='<?php print addslashes($news_info['ceki'])?>'>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label> Qiymet</label><input class="form-control" type="text" name="qiymet" size="22" value='<?php print addslashes($news_info['qiymet'])?>'>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label>Ad</label><input class="form-control" type="text" name="title_az" size="22" value='<?php print addslashes($news_info['title_az'])?>'>
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
            'metbex' => $_POST['metbex'],
            'ceki' => $_POST['ceki'],
            'qiymet' => $_POST['qiymet'],
            'title_az' => $_POST['title_az'],
            'title_en' => $_POST['title_en'],
            'title_ru' => $_POST['title_ru'],
            'yeni' => $_POST['yeni']
        );        
        $db_link->where('id', $id)->update('menuconfig', $insert_data); 
        echo '<script>document.location.href="?menu=menuconfig";</script>';
    }
}

if ($_GET['tip']==add_menuconfig){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <a href="index.php?menu=course&category_id=<?php print $tbl_category_ust;?>"><?php print $tbl_category; ?></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="menuconfig_edit" action="" method="post" enctype="multipart/form-data">
                            <div class="tab-content" id="myTabContent">
                                <div class="row col-lg-12">
                                    <div class="form-group col-lg-12">
                                        <label>metbex</label>
                                        <?php
                                        combo_metbex($category_id,$db_link);
                                        ?>
                                    </div> 
                                </div>                                

                            </div>

                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#1"  id="1-tab" data-toggle="tab" role="tab" aria-controls="1" aria-selected="true">Bazar ertəsi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#2"  id="2-tab" data-toggle="tab" role="tab" aria-controls="2" aria-selected="true">Çərşənbə axşamı</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#3"  id="3-tab" data-toggle="tab" role="tab" aria-controls="3" aria-selected="true">Çərşənbə</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#4"  id="4-tab" data-toggle="tab" role="tab" aria-controls="4" aria-selected="true">Cümə axşamı</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#5"  id="5-tab" data-toggle="tab" role="tab" aria-controls="5" aria-selected="true">Cümə</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#6"  id="6-tab" data-toggle="tab" role="tab" aria-controls="6" aria-selected="true">Şənbə</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#7"  id="7-tab" data-toggle="tab" role="tab" aria-controls="7" aria-selected="true">Bazar</a>
                                </li>    
                            </ul>

                            <div class="tab-content" id="myTabContent" style="padding-top: 10px;">
                                <div class="tab-pane fade show active" id="1" role="tabpanel" aria-labelledby="1-tab">
                                    <div class="col-md-12"><?php combo_yemek(1,$category_id,$db_link);?></div>
                                </div>

                                <div class="tab-pane fade" id="2" role="tabpanel" aria-labelledby="2-tab">
                                    <div class="col-md-12"><?php combo_yemek(2,$category_id,$db_link);?></div>
                                </div>
                                <div class="tab-pane fade" id="3" role="tabpanel" aria-labelledby="3-tab">
                                    <div class="col-md-12"><?php combo_yemek(3,$category_id,$db_link);?></div>
                                </div>
                                <div class="tab-pane fade" id="4" role="tabpanel" aria-labelledby="4-tab">
                                    <div class="col-md-12"><?php combo_yemek(4,$category_id,$db_link);?></div>
                                </div>
                                <div class="tab-pane fade" id="5" role="tabpanel" aria-labelledby="5-tab">
                                    <div class="col-md-12"><?php combo_yemek(5,$category_id,$db_link);?></div>
                                </div>
                                <div class="tab-pane fade" id="6" role="tabpanel" aria-labelledby="6-tab">
                                    <div class="col-md-12"><?php combo_yemek(6,$category_id,$db_link);?></div>
                                </div>
                                <div class="tab-pane fade" id="7" role="tabpanel" aria-labelledby="7-tab">
                                    <div class="col-md-12"><?php combo_yemek(7,$category_id,$db_link);?></div>
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
        //print "<pre>";
        //print_r($_POST);
        $metbex1=implode(",", $_POST['metbex1']);
        $metbex2=implode(",", $_POST['metbex2']);
        $metbex3=implode(",", $_POST['metbex3']);
        $metbex4=implode(",", $_POST['metbex4']);
        $metbex5=implode(",", $_POST['metbex5']);
        $metbex6=implode(",", $_POST['metbex6']);
        $metbex7=implode(",", $_POST['metbex7']);
        $insert_data = array(
            'metbex' => $_POST['metbex'],
            'metbex1' => $metbex1,
            'metbex2' => $metbex2,
            'metbex3' => $metbex3,
            'metbex4' => $metbex4,
            'metbex5' => $metbex5,
            'metbex6' => $metbex6,
            'metbex7' => $metbex7,
            'cid' => 0
        );       
        $db_link->insert ('menuconfig', $insert_data); 
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=menuconfig";</script>';

    }
}
?>
