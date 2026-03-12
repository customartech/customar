<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
        //(is_dir("$dir/$file")) ? print "$dir/$file - folder<br>" : print "$dir/$file - file<br>";
    }
    return rmdir($dir);
} 

if(!$security_test) exit;
$cid=$_GET['cid'];
$id=$_GET['id'];
$category_id=$_GET['category_id'];

if ($_GET['tip']=='delete_multimedia') {

    print '<form method="post" enctype="multipart/form-data" action="">';

    if ($_POST['Delete']) {
        $sql_result=mysql_query("DELETE FROM multimedia WHERE id ='".$cid."' and project='$project'");
        @mysql_free_result($sql_result);
        $sql_result=mysql_query("DELETE FROM multimedia_file WHERE m_id ='".$cid."' and project='$project'");
        @mysql_free_result($sql_result);
        $target_path = "../uploads/multimedia/$cid/";
        delTree($target_path);

        print "<script>document.location.href='?menu=multimedia&category_id=$category_id';</script>";

    }

    $sql = mysql_query("SELECT * FROM multimedia WHERE id='".$cid."' and project='$project'");
    while ($line = mysql_fetch_array($sql)) {
        $name=$line['name_en'];
    }
    ?>
    <fieldset>
        <legend>Delete category</legend>
        <table border="0" cellpadding="2" cellspacing="0" align=center>
            <tr>
                <td><br><b>( <?php print $name?> ) is deleting. Are you sure? </b><br><br>
                    <input onfocus="inputon(this)" onblur="inputout(this)" type="submit" name="Delete" value="Delete">&nbsp;&nbsp;
                    <input onfocus="inputon(this)" onblur="inputout(this)" type=button value="Cancel" onclick="javascript:location.href='?menu=multimedia';">
                </td>
            </tr>
        </table>
    </fieldset>
    </form>
    <?php
}

if (empty($_GET['tip'])){
    ?>
    <table width="99%" border="0" cellpadding="1" cellspacing="1" style="border: 1px solid #999999; background-color: #FFFFFF">
        <tr>
            <td colspan="4" align="center"><strong><a href="?menu=multimedia&tip=add_multimedia&category_id=<?php print addslashes($_GET['category_id'])?>">Add</a></strong></td>
        </tr>
        <tr>
            <td width="90%" bgcolor="#999999"><strong>Menu</strong></td>
            <td width="3%" align="center" bgcolor="#999999"> <strong>Edit</strong></td>
            <td width="3%" align="center" bgcolor="#999999"> <b>Content</b></td>
            <td width="3%" align="center" bgcolor="#999999"><strong>Delete</strong></td>
        </tr>
        <?php
        $multimedia_info = $db_link->where ('category_id', $category_id)->get('multimedia');
        foreach ($multimedia_info as $multimedia) {
            $sub=$multimedia['id'];
            $file_type=$multimedia['file_type'];
            ?>
            <tr onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor=''">
                <td style="border-style: dotted; border-width: 1px"><?php print $multimedia['name_az']?></td>
                <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=multimedia&category_id=<?php print addslashes($_GET['category_id'])?>&tip=edit_multimedia&cid=<?php print $multimedia['id']?>">Edit</a></td>
                <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=multimedia&category_id=<?php print addslashes($_GET['category_id'])?>&tip=add_multimedia_file&file_type=<?php print $file_type;?>&cid=<?php print $multimedia['id']?>">Image</a></td>
                <td align="center" style="border-style: dotted; border-width: 1px"><a href="JavaScript:Del('?menu=multimedia&category_id=<?php print addslashes($_GET['category_id'])?>&tip=delete_multimedia&cid=<?php print $multimedia['id']?>');">Delete</a></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}


if ($_GET['tip']==edit_multimedia){
    $id = addslashes($_GET['cid']);
    $multimedia_info = $db_link->where ('id', $id)->getOne('multimedia');

    if(!$_POST['edit']) {
        ?>
        <form name="file_edit" action="" method="post">
            <table width="99%" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="22%">&nbsp;</td>
                    <td width="78%" valign="bottom"><span>Submenu:</span></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;Azerbaijani: </td>
                    <td><input name="name_az" type="text" style="width: 40%" size="20" value="<?php print $multimedia_info['name_az']?>" /></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;English: </td>
                    <td><input type="text" name="name_en" style="width: 40%"  value="<?php print $multimedia_info['name_en']?>"/></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;Russian: </td>
                    <td><input type="text" name="name_ru" style="width: 40%"  value="<?php print $multimedia_info['name_ru']?>"/></td>
                </tr>
                <tr>
                    <td class="inn_table_text">&nbsp;&nbsp;&nbsp;Type: </td>
                    <td class="fhn_link2">
                        <select name = "file_type" class="errorMessage">
                            <option value="image">Image</option>
                    </select><?php print  "<script> file_edit.file_type.value='".$multimedia_info['file_type']."'; </script>"; ?></td>
                </tr>
                <tr>
                    <td class="inn_table_text">&nbsp;&nbsp;&nbsp;Activation: </td>
                    <td class="fhn_link2"><select name = "status" class="errorMessage">
                        <option value="active">Active</option>
                        <option value="deactive">Deactive</option>
                    </select><?php print  "<script> file_edit.status.value='".$multimedia_info['status']."'; </script>"; ?></td>
                </tr>
                <tr>
                    <td valign=middle align="center" colspan="2">
                        <input name="edit" type="submit" id="edit" value="OK" />
                        <input type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                </tr>     
            </table>
        </form>
        <?php
    } elseif($_POST['edit']) {
        $insert_data = array(
            'file_type' => $_POST['file_type'],
            'status' => $_POST['status'],
            'name_az' => $_POST['name_az'],
            'name_en' => $_POST['name_en'],
            'name_ru' => $_POST['name_ru']
        );        
        $db_link->where('id', $id)->update('multimedia', $insert_data);         

        echo '<script>document.location.href="?menu=multimedia&category_id='.$category_id.'";</script>';
    }
}


if (($_GET['tip']==add_multimedia_file)and($_GET['file_type']==youtube)){
    ?>
    <form name="file_edit" action="" method="post" enctype="multipart/form-data">  
        <?php
        if ($_GET['delete']=='1') {
            $db_link->where('id',$_GET['id'])->delete('multimedia_file');
            //@unlink("../uploads/".$_GET['fayl']);
            @mysql_free_result($sql_result);

            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=image&cid=".$_GET['cid'];
            echo '<script>document.location.href="'.$relink.'";</script>';
        }

        if($_POST['add']) {
            $youtube_url=$_POST['uploaded_img'];
            $insert_data = array(
                'm_id' => $_GET['cid'],
                'file' => $youtube_url
            );       
            $db_link->insert ('multimedia_file', $insert_data);
            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=youtube&cid=".$_GET['cid'];
            echo '<script>document.location.href="'.$relink.'";</script>';                
        }

        if($_POST['edit']) {
            $youtube_url=$_POST['uploaded_img'];
            $insert_data = array(
                'file' => $youtube_url
            );        
            $db_link->where('id', $_GET['id'])->update('multimedia_file', $insert_data); 
            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=youtube&cid=".$_GET['cid'];
            //echo '<script>document.location.href="'.$relink.'";</script>';                
        }

        if ($_GET['edit']=='1') {    
            $id = addslashes($_GET['id']);
            $multimedia_info = $db_link->where ('id', $id)->getOne('multimedia_file');
        }     

        ?>
        <script>
            function youtube_url(url) {
                if (url === null) { tinyMCEPopup.close(); return; }
                var code, regexRes;
                regexRes = url.match("[\\?&]v=([^&#]*)");
                code = (regexRes === null) ? url : regexRes[1];
                if (code === "") { tinyMCEPopup.close(); return; }
                document.getElementById('uploaded_img').value=code;
            } 
        </script>        
        <table width="100%" border="0" style="border: 1px solid #45C6E3; background-color: #FFFFFF" cellspacing="1" cellpadding="1">
            <?php
            if ($_GET['edit']=='1') {
                ?>
                <tr>
                    <td colspan="3" align="center"></td>
                </tr>
                <tr>
                    <td>Youtube link</td>
                    <td width="1%" align="center">:</td>
                    <td>
                        <input name="uploaded" type="text" size="80" value="http://www.youtube.com/watch?v=<?php print $multimedia_info['file'];?>" onchange="youtube_url(this.value);">
                        <input name="uploaded_img" id="uploaded_img" type="hidden">
                    </td>
                </tr>
                <!--                <tr>
                <td>Adi az</td>
                <td width="1%" align="center">:</td>
                <td><input name="name_az" type="text" value="<?php print $multimedia_info['name_az'];?>"></td>
                </tr>
                <tr>
                <td>Adi en</td>
                <td width="1%" align="center">:</td>
                <td><input name="name_en" type="text" value="<?php print $multimedia_info['name_en'];?>"></td>
                </tr>
                <tr>
                <td>Adi ru</td>
                <td width="1%" align="center">:</td>
                <td><input name="name_ru" type="text" value="<?php print $multimedia_info['name_ru'];?>"></td>
                </tr>
                <tr>
                <td valign=middle align="center" colspan="3">
                <input name="edit" type="submit" id="edit" value="Daxil et" />
                <input type=button value="Geri" onclick="javascript:history.go(-1);"></td>
                </tr> -->

                <?php
            }else{
                ?>
                <tr>
                    <td colspan="3" align="center"></td>
                </tr>
                <tr>
                    <td>Youtube link</td>
                    <td width="1%" align="center">:</td>
                    <td>                        
                        <input name="uploaded" type="text" size="80"  onchange="youtube_url(this.value);">
                        <input name="uploaded_img" id="uploaded_img" type="hidden">
                    </td>
                </tr>
                <!--                <tr>
                <td>Adi az</td>
                <td width="1%" align="center">:</td>
                <td><input name="name_az" type="text"></td>
                </tr>
                <tr>
                <td>Adi en</td>
                <td width="1%" align="center">:</td>
                <td><input name="name_en" type="text"></td>
                </tr>
                <tr>
                <td>Adi ru</td>
                <td width="1%" align="center">:</td>
                <td><input name="name_ru" type="text"></td>
                </tr>  -->
                <tr>
                    <td valign=middle align="center" colspan="3">
                        <input name="add" type="submit" id="add" value="OK" />
                        <input type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                </tr>
                <?php
            }       
            ?>             
        </table>

        <table width="98%" border="0" cellpadding="1" cellspacing="1" style="border: 1px solid #45C6E3; background-color: #FFFFFF">
            <tr>
                <td width="90%" background="images/top_menu/2.jpg"><strong>Multimedia block</strong></td>
                <td colspan=3 width="10%" align="center" background="images/top_menu/2.jpg"><strong>Sil</strong></td>
            </tr>
            <?php
            $multimedia_info = $db_link->where ('m_id', $_GET['cid'])->get('multimedia_file');
            foreach ($multimedia_info as $multimedia) {    
                $id=$multimedia['id'];
                $file=$multimedia['file'];
                $name_en=$multimedia['name_en'];?>
                <tr onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor=''">
                    <td style="border-style: dotted; border-width: 1px"><img width=100 src='http://img.youtube.com/vi/<?=$file?>/0.jpg' border='0'> <?php print $name_en;?></td>
                    <td align="center" style="border-style: dotted; border-width: 1px"><a href="JavaScript:;" onclick='Del("?menu=multimedia&tip=add_multimedia_file&fayl=<?php print $file?>&file_type=youtube&cid=<?php print $_GET['cid']?>&id=<?php print $id?>&delete=1")'>Delete</a></td>
                    <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=multimedia&tip=add_multimedia_file&fayl=<?php print $file?>&file_type=youtube&cid=<?php print $_GET['cid']?>&id=<?php print $id?>&edit=1">Edit</a></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </form> 

    <?php
} 


if (($_GET['tip']==add_multimedia_file)and($_GET['file_type']==image)){
    ?>
    <form name="file_edit" action="" method="post" enctype="multipart/form-data">  
        <?php
        if ($_GET['delete']=='1') {
            $db_link->where('id',$_GET['id'])->delete('multimedia_file');
            @unlink("../uploads/multimedia/$cid/".$_GET['fayl']);
            @mysql_free_result($sql_result);

            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=image&cid=".$_GET['cid'];
            echo '<script>document.location.href="'.$relink.'";</script>';
        }

        if ($_GET['cover']=='1') {
            $insert_data = array(
                'cover' => 0
            );        
            $db_link->where('m_id', $_GET['cid'])->update('multimedia_file', $insert_data);             

            $insert_data = array(
                'cover' => 1
            );        
            $db_link->where('id', $_GET['id'])->update('multimedia_file', $insert_data); 

            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=image&cid=".$_GET['cid'];
            echo '<script>document.location.href="'.$relink.'";</script>';
        }

        if($_POST['add']) {
            if(isset($_FILES['uploaded'])){
                $errors= array();
                /*
                $saat=date("YmdHis");
                $target_path = "../uploads/";
                $img_name = 'image'.$_GET['cid'].'_'.$saat.'.jpg';
                $img_type = $_FILES['uploaded']['type'];
                $target_path = $target_path.$img_name; */

                foreach($_FILES['uploaded']['tmp_name'] as $key => $tmp_name ){
                    $file_name = $key.$_FILES['uploaded']['name'][$key];
                    $file_size =$_FILES['uploaded']['size'][$key];
                    $file_tmp =$_FILES['uploaded']['tmp_name'][$key];
                    $file_type=$_FILES['uploaded']['type'][$key];    
                    if($file_size > 10097152){$errors[]='File size must be less than 10 MB'; }        
                    $insert_data = array(
                        'm_id' => $_GET['cid'],
                        'file' => $file_name
                    );       
                    $db_link->insert ('multimedia_file', $insert_data);
                    //print $db_link->getLastQuery();
                    $desired_dir="../uploads/multimedia/".$_GET['cid'];

                    if(empty($errors)==true){
                        if(is_dir($desired_dir)==false){
                            mkdir("$desired_dir", 0755);        // Create directory if it does not exist
                        }
                        if(is_dir("$desired_dir/".$file_name)==false){
                            move_uploaded_file($file_tmp,"$desired_dir/".$file_name);
                        }else{                                    // rename the file if another one exist
                            $new_dir="$desired_dir/".$file_name.time();
                            rename($file_tmp,$new_dir) ;                
                        }
                        mysql_query($sql); 
                    }else{
                        print_r($errors);
                    }
                }
                if(empty($error)){
                    echo "Success";
                }
            }                


            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=image&cid=".$_GET['cid'];
            //echo '<script>document.location.href="'.$relink.'";</script>';                
        }

        if($_POST['edit']) {
            include('SimpleImage.php');
            $id = addslashes($_GET['id']);
            $saat=date("YmdHis");
            $target_path = "../uploads/multimedia/$cid/";
            $img_name = 'image'.$_GET['cid'].'_'.$saat.'.jpg';
            $target_path = $target_path.$img_name;
            if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target_path)){
                $insert_data = array(
                    'file' => $img_name
                );        
                $db_link->where('id', $id)->update('multimedia_file', $insert_data); 
                $img = new abeautifulsite\SimpleImage($target_path);
                $img->best_fit(1000, 1000)->save($target_path); 
            }

            /*$insert_data = array(
                'name_az' => $_POST['name_az'],
                'name_ru' => $_POST['name_ru'],
                'name_en' => $_POST['name_en']
            );        
            $db_link->where('id', $id)->update('multimedia_file', $insert_data); */

            $relink="?menu=multimedia&tip=add_multimedia_file&fayl=$file&file_type=image&cid=".$_GET['cid'];
            //echo '<script>document.location.href="'.$relink.'";</script>';                
        }

        if ($_GET['edit']=='1') {    
            $id = addslashes($_GET['id']);
            $multimedia_info = $db_link->where ('id', $id)->getOne('multimedia');

        }     

        ?>
        <table width="100%" border="0" style="border: 1px solid #45C6E3; background-color: #FFFFFF" cellspacing="1" cellpadding="1">
            <?php
            if ($_GET['edit']=='1') {
                ?>
                <tr>
                    <td colspan="3" align="center"></td>
                </tr>
                <tr>
                    <td>Image</td>
                    <td width="1%" align="center">:</td>
                    <td><input name="uploaded[]" multiple type="file"> <?php print $multimedia_info['file'];?></td>
                </tr>
                <tr>
                    <td valign=middle align="center" colspan="3">
                        <input name="edit" type="submit" id="edit" value="OK" />
                        <input type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                </tr> 

                <?php
            }else{
                ?>
                <tr>
                    <td colspan="3" align="center"></td>
                </tr>
                <tr>
                    <td>Image</td>
                    <td width="1%" align="center">:</td>
                    <td><input name="uploaded[]" multiple type="file"></td>
                </tr>
                <tr>
                    <td valign=middle align="center" colspan="3">
                        <input name="add" type="submit" id="add" value="OK" />
                        <input type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                </tr>
                <?php
            }       
            ?>             
        </table>

        <table width="98%" border="0" cellpadding="1" cellspacing="1" style="border: 1px solid #45C6E3; background-color: #FFFFFF">
            <tr>
                <td width="90%" background="images/top_menu/2.jpg"><strong>Multimedia block</strong></td>
                <td colspan=3 width="10%" align="center" background="images/top_menu/2.jpg"><strong>Sil</strong></td>
            </tr>
            <?php
            $multimedia_info = $db_link->where ('m_id', $_GET['cid'])->get('multimedia_file');
            foreach ($multimedia_info as $multimedia) { 
                $id=$multimedia['id'];
                $cover=$multimedia['cover'];
                $file=$multimedia['file'];
                $name_en=$multimedia['name_en'];?>
                <tr onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor=''">
                    <td style="border-style: dotted; border-width: 1px"><img width=100 src='../image.php?img=<?php print $file?>&w=150&h=150&type=multimedia&project=<?php print $project?>&cid=<?=$_GET['cid']?>' border='0'> <?php print $name_en;?></td>
                    <td align="center" style="border-style: dotted; border-width: 1px"><a href="JavaScript:;" onclick='Del("?menu=multimedia&tip=add_multimedia_file&fayl=<?php print $file?>&file_type=image&cid=<?php print $_GET['cid']?>&id=<?php print $id?>&delete=1")'>Delete</a></td>
                    <td align="center" style="border-style: dotted; border-width: 1px"><?php if($cover!=1){?><a href="?menu=multimedia&tip=add_multimedia_file&fayl=<?php print $file?>&file_type=image&cid=<?php print $_GET['cid']?>&id=<?php print $id?>&cover=1">Main</a><?php }?></td>
                    <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=multimedia&tip=add_multimedia_file&fayl=<?php print $file?>&file_type=image&cid=<?php print $_GET['cid']?>&id=<?php print $id?>&edit=1">Edit</a></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </form> 

    <?php
} 


if ($_GET['tip']==add_multimedia){

    if(!$_POST['add']) {
        ?>
        <form name="file_edit" action="" method="post">
            <table width="99%" border="0" align="left" cellpadding="0" cellspacing="0" bgcolor="f7f7f7">
                <tr>
                    <td width="22%">&nbsp;</td>
                    <td width="78%" valign="bottom"><span>Submenu:</span></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;Azerbaijani: </td>
                    <td><input name="name_az" type="text" style="width: 40%" size="20" value="<?php print $multimedia_info['name_az']?>" /></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;English: </td>
                    <td><input type="text" name="name_en" style="width: 40%"  value="<?php print $multimedia_info['name_en']?>"/></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;Russian: </td>
                    <td><input type="text" name="name_ru" style="width: 40%"  value="<?php print $multimedia_info['name_ru']?>"/></td>
                </tr>
                <tr>
                    <td class="inn_table_text">&nbsp;&nbsp;&nbsp;Type: </td>
                    <td class="fhn_link2">
                        <select name = "file_type" class="errorMessage">
                            <option value="image">Image</option>
                            <option value="youtube">Youtube</option>
                        </select></td>
                </tr>
                <tr>
                    <td class="inn_table_text">&nbsp;&nbsp;&nbsp;Activation: </td>
                    <td class="fhn_link2"><select name = "status" class="errorMessage">
                            <option value="active">Active</option>
                            <option value="deactive">Deactive</option>
                        </select></td>
                </tr>
                <tr>
                    <td valign=middle align="center" colspan="2">
                        <input name="add" type="submit" id="add" value="OK" />
                        <input type=button value="Cancel" onclick="javascript:history.go(-1);"></td>
                </tr>      
            </table>
        </form>
        <?php
    } elseif($_POST['add']) {
        $insert_data = array(
            'file_type' => $_POST['file_type'],
            'category_id' => $category_id,
            'status' => $_POST['status'],
            'name_az' => $_POST['name_az'],
            'name_en' => $_POST['name_en'],
            'name_ru' => $_POST['name_ru']
        );       
        $db_link->insert ('multimedia', $insert_data);
        echo '<script>document.location.href="?menu=multimedia&category_id='.$category_id.'";</script>';
    }
}
?>