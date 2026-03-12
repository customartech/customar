<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$lang="az";
include "../lang.php";

//if ($_GET['tip']==) $news_date=$news_info['news_date']; else $news_date=date('Y-m-d');
$category_id=$_GET['category_id'];
$id = addslashes($_GET['cid']);
$tid = addslashes($_GET['tid']);
if ($_GET['tip']==bitirib_course_qeyd){
    $insert_data = array('status' => "bitirib");       
    $db_link->where('id', $id)->update('course_qeyd', $insert_data); 
    echo '<script>document.location.href="?menu=course_qeyd";</script>';
}
if ($_GET['tip']==sertfikat_course_qeyd){
    $insert_data = array('status' => "sertfikat");       
    $db_link->where('id', $id)->update('course_qeyd', $insert_data); 
    echo '<script>document.location.href="?menu=course_qeyd";</script>';
}

if ($_GET['tip']==tesdiq_course_qeyd){
    $insert_data = array('status' => "tesdiq");       
    $db_link->where('id', $id)->update('course_qeyd', $insert_data); 
    echo '<script>document.location.href="?menu=course_qeyd";</script>';
}

if ($_GET['tip']==delete_course_qeyd){
    $db_link->where('id',$id)->delete('course_qeyd');
    echo '<script>document.location.href="?menu=course_qeyd";</script>';
}


if (empty($_GET['tip'])){
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">Tədbirə yazılanlar</div>
                <div class="card-body">
                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=course_qeyd&tip=add_user" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>
                    </div>
                    <br>                
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listQeyd">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Kurs</th>
                                    <th>Tədbir</th>
                                    <th>İstifadəçi</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($tid) $db_link->where ('tid',$tid); 
                                $news_info = $db_link->get('course_qeyd');
                                foreach ($news_info as $news) {
                                    $id = stripslashes($news['id']);
                                    $create_date = stripslashes($news['create_date']);
                                    $tid = stripslashes($news['tid']);
                                    $courseid = stripslashes($news['courseid']);
                                    $cid = stripslashes($news['cid']);
                                    $status = stripslashes($news['status']);

                                    $course_ad = $db_link->where("id", $courseid)->getValue ("tedbir", "title_az");   
                                    $tedbir_ad = $db_link->where("id", $tid)->getValue ("tedbir", "title_az"); 
                                    $tedbir_tarix = $db_link->where("id", $tid)->getValue ("tedbir", "news_date"); 
                                    
                                    if($tedbir_tarix=='0000-00-00') $tedbir_tarix = 'Açıq';
                                      
                                    $u_ad = $db_link->where("id", $cid)->getValue ("qeydiyyat", "ad");   
                                    $u_soyad = $db_link->where("id", $cid)->getValue ("qeydiyyat", "soyad");   
                                    $course_qeyd = $db_link->where("tid", $id)->getValue ("course_qeyd", "count(id)");   

                                    print "<tr class='odd gradeA' id='arrayorder_$id'>
                                    <td>$create_date</td>
                                    <td>$course_ad</td>
                                    <td>$tedbir_ad</td>
                                    <td>$u_ad $u_soyad</td>
                                    <td>$status</td>
                                    <td class='center'>
                                    <a rel='tooltip' data-original-title='Qeydiyyati tesdiqle' href='?menu=course_qeyd&tip=tesdiq_course_qeyd&cid=$id'><span class='fa fa-check-circle-o'></span></a>
                                    <a rel='tooltip' data-original-title='Kursu bitirib' href='?menu=course_qeyd&tip=bitirib_course_qeyd&cid=$id'><span class='fa fa-window-close-o'></span></a>
                                    <a rel='tooltip' data-original-title='Sertfikat alib' href='?menu=course_qeyd&tip=sertfikat_course_qeyd&cid=$id'><span class='fa fa-drivers-license-o'></span></a>
                                    <a rel='tooltip' data-original-title='Sil' onclick='Del(\"?menu=course_qeyd&tip=delete_course_qeyd&category_id=$category_id&cid=$id\");' href='JavaScript:;'><span class='fa fa-trash'></span></a>
                                    </td>
                                    </tr>";
                                } 
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Date</th>
                                    <th>Kurs</th>
                                    <th>Tədbir</th>
                                    <th>İstifadəçi</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php
}




if ($_GET['tip']==add_user){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    Qeydiyyat
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="col-lg-12" role="form" name="xeber_edit" action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>Şirkət</label>                                                                  
                                        <div class="form-group">
                                            <?php
                                            combo_company($category_id,$db_link);
                                            ?>                                                                                            
                                        </div>
                                    </div>
                                </div>                            
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>Tedbir</label>                                                                  
                                        <div class="form-group">
                                            <?php
                                            combo_tedbir($category_id,$db_link);
                                            ?>                                                                                            
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>E-mail</label>                                                                  
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" aria-label="Email">                                                                                            
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>Doğum tarixi</label>                                                              
                                        <div class="form-group">
                                            <input type="text" name="ddate" class="form-control" aria-label="Name">                                                                                            
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>Ad</label>                                                              
                                        <div class="form-group">
                                            <input type="text" name="ad" class="form-control" aria-label="Name">                                                                                            
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>Soyad</label>                                                      
                                        <div class="form-group">
                                            <input type="text" name="soyad" class="form-control" aria-label="Name">                                                                                            
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-6">    
                                    <div class="single-input">
                                        <label>Tel</label>                                                      
                                        <div class="form-group">
                                            <input type="text" name="tel" class="form-control" aria-label="Name">                                                                                            
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-6">
                                    <div class="single-input">
                                        Parol                                                                  
                                        <div class="form-group">                                
                                            <input type="password" name="password" class="form-control" value="12345678">                                
                                        </div>
                                    </div>
                                </div>                                     
                                <div class="col-md-6">
                                    <div class="single-input">
                                        Parol tekrar                                                                  
                                        <div class="form-group">                                
                                            <input type="password" name="tpassword" class="form-control" value="12345678">                                
                                        </div>
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
        $tid=$_POST['tid'];
        $newid = $db_link->where("email", $_POST['email'])->getValue ("qeydiyyat", "id");
        if(empty($newid)){
            $name=html_entity_decode($_POST['ad']);
            $soyad=html_entity_decode($_POST['soyad']);
            $company=$_POST['company'];
            $email=$_POST['email'];
            $pass = $_POST['password'];
            $tel = $_POST['tel'];
            $ddate = $_POST['ddate'];
            $hash = 'lehlulu';
            $pass2encrypt = $hash.$pass;
            $password = md5($pass2encrypt);
            $aktive_token=md5(yazi(15));
            $update_user_data = array(
                'company' => $company,
                'ad' => $name,                        
                'soyad' => $soyad,                        
                'ddate' => $ddate, 
                'tel' => $tel, 
                'email' => $email, 
                'pass' => $password,
                'aktive_token' => $aktive_token,
                'pass' => $password
            );
            $newid=$db_link->insert('qeydiyyat', $update_user_data);
            //print $db_link->getLastQuery()."<br>";        
        }
        if($newid){
            $course_id = $db_link->where("id", $tid)->getValue ("tedbir", "category_id");
            $data = Array (
                'tid' => $tid,
                'courseid' => $course_id,
                'cid' => $newid,
                'create_date' => $db_link->now()
            );
            $db_link->insert('course_qeyd',$data); 
        }
        //print $db_link->getLastQuery();
        echo '<script>document.location.href="?menu=course_qeyd";</script>';

    }

}
?>