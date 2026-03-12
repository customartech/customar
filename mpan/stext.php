<?php
if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
    header("Location: login.php");
    exit;
}
if(!$security_test) exit;

if( !(($_SESSION['flag'] & CUST_SUPERUSER) or ($_SESSION['flag'] & CUST_CONTENT)) ){
    echo "<h1>Access Denied";
    echo "<p>Sizin bu b&ouml;lm&#601;y&#601;y girm&#601;y&#601; haqq&#305;n&#305;z yoxdur</h1>";
    return;
}

if ($_GET['tip']==edit_stext) $stext_date=$stext_info['stext_date']; else $stext_date=date('Y-m-d');
$category_id=$_GET['category_id'];

if (empty($_GET['tip'])){
    ?>

    <style>
        ul {padding:0px; margin: 0px;}
        #response { padding:10px;  background-color:#9F9; border:2px solid #396;  margin-bottom:20px; }
        #list li { margin: 0 0 3px; padding:5px; background-color:#DCE2E0; color:#000; list-style: none;text-align: left; font-weight: bold; }
        #list ul li { margin: 0 0 3px; padding:5px; background-color:#DCE2E0; color:#000; list-style: none;text-align: left; font-weight: bold; }
    </style>
    <?php        

    $seh_say=150;
    $say=mysql_num_rows(mysql_query("SELECT * FROM stext"));
    $say1=$say/$seh_say;

    if (empty($_GET['seh']))
        $vereq=0;
    else
        $vereq=$_GET['seh']*$seh_say;

    $sql = "SELECT * FROM stext ORDER BY id Desc LIMIT $vereq,$seh_say";
    $q = mysql_query($sql);
    ?>
    <table width="100%" border="0" cellpadding="1" cellspacing="1" style="border: 1px solid #999999; background-color: #FFFFFF">
        <tr>
            <td colspan="6" align="center"><strong><a href="?menu=stext&tip=add_stext&category_id=<?php print $category_id?>">Add new</a></strong></td>
        </tr>
        <tr>
            <td width="300" bgcolor="#999999"><strong>Title</strong></td>
            <td width="10px" align="center" bgcolor="#999999"> <strong>Edit</strong></td>
            <td width="10px" align="center" bgcolor="#999999"><strong>Delete</strong></td>
        </tr>
        <?php
        while($stext = mysql_fetch_array($q)) {
            $id = stripslashes($stext['id']);
            $name = stripslashes($stext['name']);
            print "
            <tr onmouseover=\"this.style.backgroundColor='#f1f1f1'\" onmouseout=\"this.style.backgroundColor=''\">
            <td>$name</td>
            <td align='center' width='10px'>";                                   
            print '<a href="?menu=stext&tip=edit_stext&category_id='.$category_id.'&cid='.$id.'">Edit</a>';
            print "</td><td align='center' width='10px'>";
            print '<a onclick="Del(\'index.php?menu=stext&tip=delete_stext&category_id='.$category_id.'&cid='.$id.'\')" href="JavaScript:;">Delete</a> ';
            print "</td></tr>";
        } 
        ?>
        <tr>
            <td colspan="6" align="center">
                <?php
                if ($say1>=2) {
                    for ($i = 1; $i < $say1; $i++) {
                        echo "<a href='?menu=stext&seh=$i'><b>$i</b></a>";
                    }
                }

                ?>

            </td>
        </tr>
    </table>
    <?php
}


if ($_GET['tip']==delete_stext){
    $sql = "DELETE FROM stext WHERE id = '".addslashes($_GET['cid'])."' LIMIT 1";
    $q = mysql_query($sql);
    echo '<script>document.location.href="?menu=stext";</script>';
}

if ($_GET['tip']==edit_stext){
    $id = addslashes($_GET['cid']);
    $sql = "SELECT * FROM stext WHERE id = '$id'";
    $q = mysql_query($sql);
    $stext_info = mysql_fetch_array($q);

    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php print @mysql_result(mysql_query("SELECT `name_az` FROM category where `id`='".$category_id."'",$db_link), 0, 0); ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form role="form" name="stext_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $stext_info['id']?>" />
                            <div class="form-group">
                                <label>Title</label>
                                <input class="form-control" type="text" name="name" size="107" value='<?php print stripslashes($stext_info['name'])?>'>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#az" data-toggle="tab">Azerbaijani</a></li>
                                <li><a href="#en" data-toggle="tab">English</a></li>
                                <li><a href="#ru" data-toggle="tab">Russian</a></li>

                            </ul>

                            <div class="tab-content">
                            <div class="tab-pane fade in active" id="az">
                                <div class="form-group">
                                    <textarea id="content_az" name="content_az"><?php print stripcslashes($stext_info['content_az'])?></textarea> 
                                </div>
                            </div>

                            <div class="tab-pane fade" id="en">
                                <div class="form-group">
                                    <textarea id="content_en" name="content_en"><?php print stripcslashes($stext_info['content_en'])?></textarea> 
                                </div>
                            </div>

                            <div class="tab-pane fade" id="ru">
                                <div class="form-group">
                                    <textarea id="content_ru" name="content_ru"><?php print stripcslashes($stext_info['content_ru'])?></textarea>
                                </div>
                            </div>
                            <br>
                            <center>
                                <input class="btn btn-primary" name="edit" type="submit" id="edit" value="OK"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>

                        </form>
                    </div>
                </div>

            </div>
        </div>        

        <?php
    } elseif($_POST['edit']) {
        $sql = "UPDATE stext SET 
        name = '".$_POST['name']."', 
        content_az = '".$_POST['content_az']."',
        content_ru = '".$_POST['content_ru']."',
        content_en = '".$_POST['content_en']."'
        WHERE id = '$id'";
        $q = mysql_query($sql); 
        echo '<script>document.location.href="?menu=stext";</script>';
    }
}

if ($_GET['tip']==add_stext){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php print @mysql_result(mysql_query("SELECT `name_az` FROM category where `id`='".$category_id."'",$db_link), 0, 0); ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form role="form" name="stext_add" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $stext_info['id']?>" />
                            <div class="form-group">
                                <label>Title</label>
                                <input class="form-control" type="text" name="name" size="107">
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#az" data-toggle="tab">Azerbaijani</a></li>
                                <li><a href="#en" data-toggle="tab">English</a></li>
                                <li><a href="#ru" data-toggle="tab">Russian</a></li>

                            </ul>

                            <div class="tab-content">
                            <div class="tab-pane fade in active" id="az">
                                <div class="form-group">
                                    <textarea id="content_az" name="content_az"></textarea> 
                                </div>
                            </div>

                            <div class="tab-pane fade" id="en">
                                <div class="form-group">
                                    <textarea id="content_en" name="content_en"></textarea> 
                                </div>
                            </div>

                            <div class="tab-pane fade" id="ru">
                                <div class="form-group">
                                    <textarea id="content_ru" name="content_ru"></textarea>
                                </div>
                            </div>
                            <br>
                            <center>
                                <input class="btn btn-primary" name="add" type="submit" id="add" value="OK"> <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                            </center>

                        </form>
                    </div>
                </div>

            </div>
        </div>         
        <?php
    } elseif($_POST['add']) {
        $sql = "INSERT INTO stext ( 
        name, 
        content_az, 
        content_ru,
        content_en
        ) VALUES (
        '".$_POST['name']."', 
        '".$_POST['content_az']."', 
        '".$_POST['content_ru']."',
        '".$_POST['content_en']."'
        )";
        $q = mysql_query($sql);
        echo '<script>document.location.href="?menu=stext";</script>';

    }
}
?>
<script>
    CKEDITOR.replace( 'content_az' , {
        removePlugins: 'newpage,elementspath,save',
        extraPlugins: 'wysiwygarea',
        height:'400px'
    });
    CKEDITOR.replace( 'content_en' , {
        removePlugins: 'newpage,elementspath,save',
        extraPlugins: 'wysiwygarea',
        height:'400px'
    });
    CKEDITOR.replace( 'content_ru' , { 
        removePlugins: 'newpage,elementspath,save',
        extraPlugins: 'wysiwygarea',
        height:'400px'
    });
</script>  