<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

if(!$security_test) exit;


if ($_GET['tip']=='delete_menyu') {
    $cid=$_GET['cid'];
    $db_link->where('id',$cid)->delete('category');
    echo '<script>document.location.href="?menu=menyu";</script>';
}


function queryToCombo($table,$comboname,$data1,$data2,$selected=null,$where=null){
    global $db_link;
    if(isset($table)){
        $tbl_data = $db_link->where("sub_id",0)->orderBy("blok","asc")->get('category'); 
        $count1=$db_link->count;

        $combo.="<select name='$comboname' id='$comboname' class='form-control'>";
        $combo.="<option value='0'> - - - - - </option>";
        foreach ($tbl_data as $tbl_datas) {
            if($selected==$tbl_datas[$data1])
                $combo.="<option selected='selected' value='".$tbl_datas[$data1]."'>".$tbl_datas[$data2]."</option>";
            else
                $combo.="<option value='".$tbl_datas[$data1]."'>".$tbl_datas[$data2]."</option>";

            $tbl_data1 = $db_link->where("sub_id",$tbl_datas[$data1])->orderBy("blok","asc")->get('category'); 
            //print $db_link->getLasQuery();
            foreach ($tbl_data1 as $tbl_datas1) {
                if($selected==$tbl_datas1[$data1])
                    $combo.="<option selected='selected' value='".$tbl_datas1[$data1]."'>- - -".$tbl_datas1[$data2]."</option>";
                else
                    $combo.="<option value='".$tbl_datas1[$data1]."'>- - -".$tbl_datas1[$data2]."</option>";
            }

        }
        $combo.="</select>";
        return $combo;
    }
    return false;
}


if (empty($_GET['tip'])){
    ?>


    <style>
        ul {padding:0px; margin: 0px;}
        #response { padding:10px;  background-color:#9F9; border:2px solid #396;  margin-bottom:20px; }
        #listMenyu li { margin: 0 0 3px; padding:5px; background-color:#DCE2E0; color:#000; list-style: none;text-align: left; font-weight: bold; }
        #listMenyu ul li { margin: 0 0 3px; padding:5px; background-color:#DCE2E0; color:#000; list-style: none;text-align: left; font-weight: bold; }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){     
            function slideout(){
                setTimeout(function(){
                    $("#response").slideUp("slow", function () {  }); }, 2000);
            }
            $("#response").hide();
            $(function() {
                $("#listMenyu ul").sortable({ opacity: 0.8, cursor: 'move', update: function() {
                    var order = $(this).sortable("serialize") + '&update=update'; 
                    $.post("updatemenyu.php", order, function(theResponse){
                        $("#response").html(theResponse);
                        $("#response").slideDown('slow');
                        slideout();
                    });                                                              
                    }                                  
                });
            });

        });    
    </script>

    <div class="col-lg-12">
    <div class="card card-primary mb-3">
        <div class="card-header">
            Menyular   <a href="?menu=menyu&tip=add_menyu" type="button" class="btn btn-outline btn-primary">Add new menu</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="container" class="col-lg-12">
                    <div id="listMenyu">
                        <div id="response"> </div>
                        <?php
                        function  buildTreeM($sub_id=0,$lv,$db_link){
                            $lv++;
                            $dddd=$lv*5;
                            $cat_menyus = $db_link->where("sub_id",$sub_id)->orderBy("blok","asc")->get('category'); 
                            $count1=$db_link->count;
                            if($count1>0) {print "<ul>"; }
                            foreach ($cat_menyus as $row1) {
                                $mid1 = stripslashes($row1['id']);
                                $blok1 = stripslashes($row1['blok']);
                                $name_az1 = stripslashes($row1['name_az']);

                                print "<li style='padding-left: ".$dddd."px;' id='arrayorder".$lv."_$mid1'>
                                <table style='margin:0px' class='table table-striped table-bordered table-hover'>
                                <tr class='odd gradeA'>
                                <td style='cursor:move;' width='10px'>$blok1</td>
                                <td style='padding-left: 30px; width:80%'>$name_az1</td>

                                <td class='center' width='20px'>
                                <a href='?menu=menyu&tip=edit_menyu&cid=$mid1'><span class='fa fa-pencil'></span></a>
                                <a onclick='Del(\"?menu=menyu&tip=delete_menyu&cid=$mid1\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
                                </td></tr></table>";
                                print "<div class='clear'></div>"; 
                                $count = $db_link->where("sub_id",$mid1)->getValue ("category", "count(*)"); 
                                if($count)buildTreeM($mid1,$lv,$db_link);
                                print "</li>";  

                                /*print "<tr class='odd gradeA' id='arrayorder".$sub_id."_$mid1'>
                                <td style='cursor:move;padding-left: 30px;'>$blok1</td>
                                <td style='padding-left: 30px;'>$name_az1</td>
                                <td style='padding-left: 30px;'>$name_en1</td>
                                <td style='padding-left: 30px;'>$name_ru1</td>
                                <td class='center'>
                                <a href='?menu=menyu&tip=edit_menyu&cid=$mid1'><span class='glyphicon glyphicon-pencil'></span></a>
                                <a onclick='Del(\"?menu=menyu&tip=delete_menyu&cid=$mid1\");' href='JavaScript:;'><span class='glyphicon glyphicon-trash'></span></a>
                                </td>
                                </tr>";*/    
                                /*$count = $db_link->selectData('category', array('sub_id'=>$mid1), null, 'blok', null, null)->numRows(); 
                                if($count)buildTreeM($mid1,$db_link);
                                print "</li>";*/
                            }
                            if($count1>0) print "</ul>";
                        }
                        buildTreeM(0,0,$db_link); 
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>            
    <?php
}



if ($_GET['tip']=='edit_menyu'){
    $id = addslashes($_GET['cid']);
    $category_info = $db_link->where("id",$id)->getOne('category');
    $this_count=$db_link->count;

    if(!$_POST['edit']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    Menyular
                </div>
                <div class="card-body">
                    <div class="row">        
                        <form class="col-lg-12" name="menu_edit" action="" method="post" enctype="multipart/form-data">
                            <table width="100%" border="0" style="border: 1px solid #999999; background-color: #FFFFFF" cellspacing="1" cellpadding="1">
                                <tr>
                                    <td colspan="3" align="center"><input type="hidden" name="id" value="<?php print $category_info['id']?>" /></td>
                                </tr>
                                <tr>
                                    <td valign=top width="10%">Category</td>
                                    <td width="6" valign=top align="center">:</td>
                                    <td>
                                        <?php print queryToCombo("category","category","id","name_az",$category_info['sub_id']);?>
                                    </td>    
                                </tr>  
                                <tr>
                                    <td>Title image</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                    <input type="file" name="uploaded" size="48"><?php print $category_info['img1']?></td>
                                </tr>                
                                <tr>
                                    <td>AZE</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <input type="text" name="name_az" value="<?php print $category_info['name_az']?>" size="48" /></td>
                                </tr>

                                <tr>
                                    <td>ENG</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <input type="text" name="name_en" value="<?php print $category_info['name_en']?>" size="48" /></td>
                                </tr> 
                                <tr>
                                    <td>RUS</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <input type="text" name="name_ru" value="<?php print $category_info['name_ru']?>" size="48" /></td>
                                </tr> 

                                <tr>
                                    <td>Type</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <select name = "type">
                                            <option value="content">Content</option>
                                            <option value="news">News</option>
                                            <option value="ourteam">Ourteam</option>
                                            <option value="projects">Projects</option>
                                            <option value="services">Services</option>
                                            <option value="photos">Photos</option>
                                        </select><?php print  "<script> menu_edit.type.value='".$category_info['type']."'; </script>"; ?>
                                    </td>
                                </tr>
                                <!--                <tr>
                                <td>Location</td>
                                <td align="center">&nbsp;</td>
                                <td><select name = "ust">
                                <option value="1">Top menu</option>
                                <option value="0">Normal menu</option>
                                </select><?php print  "<script> menu_edit.ust.value='".$category_info['ust']."'; </script>"; ?>
                                </td>
                                </tr>-->        
                                <!--                <tr>
                                <td>Footer menu</td>
                                <td align="center">&nbsp;</td>
                                <td>
                                <input <?php if($category_info['alt']) print 'checked="checked"'; ?> type="checkbox" name="alt" value="1">
                                </td>
                                </tr> -->       
                                <tr>
                                    <td>Status</td>
                                    <td align="center">&nbsp;</td>
                                    <td>
                                        <select name = "status">
                                            <option value="active">Active</option>
                                            <option value="deactive">Deactive</option>
                                        </select><?php print  "<script> menu_edit.status.value='".$category_info['status']."'; </script>"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="center">&nbsp;</td>
                                    <td><input class="btn btn-outline btn-primary" name="edit" type="submit" id="edit" value="OK" /> <input class="btn btn-outline btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div>          
        <?php
    } elseif($_POST['edit']) {

        $update_data = array(
            'sub_id' => $_POST['category'],
            'name_az' => $_POST['name_az'],
            'name_en' => $_POST['name_en'],
            'name_ru' => $_POST['name_ru'],
            'type' => $_POST['type'],
            'status' => $_POST['status']
        );
        $db_link->where('id', $id)->update('category', $update_data);

        $saat=date("YmdHis");
        $target_path = "../uploads/menyu/";
        $ext = pathinfo($_FILES['uploaded']['name'], PATHINFO_EXTENSION);
        $img_name = 'menyu'.$id.'.'.$ext;//$img_name = 'menyu'.$id.'_'.$saat.'.'.$ext;
        $img_type = $_FILES['uploaded']['type'];
        $target_path = $target_path.$img_name;
        if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target_path)){
            $images=array('img1' => $img_name);
            $db_link->where('id', $id)->update('category', $images);
        }


        $update_data1 = array(
            'name_az' => $_POST['name_az'],
            'name_en' => $_POST['name_en'],
            'name_ru' => $_POST['name_ru']
        );
        $db_link->where('category_id', $id)->update('content', $update_data1);
        echo '<script>document.location.href="?menu=menyu";</script>';            

    }
}



if ($_GET['tip']=='add_menyu'){

    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    Menyular
                </div>
                <div class="card-body">
                    <div class="row">        
                        <form class="col-lg-12" action="" method="post" enctype="multipart/form-data">
                            <table width="100%" border="0" style="border: 1px solid #999999; background-color: #FFFFFF" cellspacing="1" cellpadding="1">
                                <tr>
                                    <td colspan="3" align="center"></td>
                                </tr>

                                <tr>
                                    <td valign=top width="10%">Category</td>
                                    <td width="6" valign=top align="center">:</td>
                                    <td>
                                        <?php print queryToCombo("category","category","id","name_az",$category_info['sub_id']);?>
                                        For submenu select top menu</td>
                                </tr>  
                                <tr>
                                    <td>Title image</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                    <input type="file" name="uploaded" size="48"><?php print $category_info['img1']?></td>
                                </tr>  
                                <tr>
                                    <td>AZE</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <input type="text" name="name_az" size="48" /></td>
                                </tr>
                                <tr>
                                    <td>ENG</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <input type="text" name="name_en" size="48" /></td>
                                </tr> 
                                <tr>
                                    <td>RUS</td>
                                    <td width="1%" align="center">:</td>
                                    <td>
                                        <input type="text" name="name_ru" size="48" /></td>
                                </tr>      
                                <tr>
                                    <td>Type</td>
                                    <td width="1%" align="center">:</td>
                                    <td><select name = "type">
                                            <option value="content">Content</option>
                                            <option value="news">News</option>
                                            <option value="ourteam">Ourteam</option>
                                            <option value="projects">Projects</option>
                                            <option value="services">Services</option>
                                            <option value="photos">Photos</option>
                                        </select>
                                    </td>
                                </tr>
                                <!--                <tr>
                                <td>Location</td>
                                <td align="center">&nbsp;</td>
                                <td><select name = "ust">
                                <option value="1">Top menu</option>
                                <option value="0">Normal menu</option>
                                </select><?php print  "<script> menu_edit.ust.value='".$category_info['ust']."'; </script>"; ?>
                                </td>
                                </tr> -->                
                                <tr>
                                    <td>Status</td>
                                    <td align="center">&nbsp;</td>
                                    <td><select name = "status">
                                            <option value="active">Active</option>
                                            <option value="deactive">Deactive</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="center">&nbsp;</td>
                                    <td><input class="btn btn-outline btn-primary" name="add" type="submit" id="add" value="OK" /> <input class="btn btn-outline btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div>        
        <?php
    } elseif($_POST['add']) {
        $insert_data = array(
            'sub_id' => (int)$_POST['category'],
            'name_az' => $_POST['name_az'],
            'name_en' => $_POST['name_en'],
            'name_ru' => $_POST['name_ru'],
            'type' => $_POST['type'],
            'status' => $_POST['status']
        );
        $newid=$db_link->insert('category', $insert_data);
        //print $db_link->getLastQuery()."<br>";

        $saat=date("YmdHis");
        $target_path = "../uploads/menyu/";
        $ext = pathinfo($_FILES['uploaded']['name'], PATHINFO_EXTENSION);        
        $img_name = 'menyu'.$newid.'.'.$ext;  //$img_name = 'menyu'.$maxid.'_'.$saat.'.'.$ext;
        $img_type = $_FILES['uploaded']['type'];
        $target_path = $target_path.$img_name;
        if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target_path)){
            $update_data1 = array('img1' => $img_name);
            $db_link->where('id', $newid)->update('category', $update_data1);
        }            

        $insert_data1 = array(
            'category_id' => (int)$newid,
            'name_az' => $_POST['name_az'],
            'name_en' => $_POST['name_en'],
            'name_ru' => $_POST['name_ru']
        ); 
        $newid=$db_link->insert('content', $insert_data1);
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=menyu";</script>';          
    }
}
?>