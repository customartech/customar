<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
if(!$security_test) exit;
$id = addslashes($_GET['cid']);
if (empty($_GET['tip'])){
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card md-3">
                <div class="card-header"> Sayt istfadəçiləri </div>
                <div class="card-body">
                    <div id="custom-toolbar">
                        <div class="form-inline" role="form">
                            <a href="?menu=susers&tip=add_susers" type="button" class="btn btn-outline btn-primary">Add new</a>
                        </div>                    
                    </div>
                    <br>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="UsersTable">
                            <thead>
                                <tr>
                                    <th>İD</th>
                                    <th>Son daxil olma</th>
                                    <th>Name</th>
                                    <th>Tel</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php
}


if ($_GET['tip']==delete_susers){
    $db_link->where('id',$id)->delete('qeydiyyat');
    echo '<script>document.location.href="?menu=susers";</script>';
}



if ($_GET['tip']==edit_susers){
    $qeydiyyat_info = $db_link->where ('id', $id)->getOne('qeydiyyat');
    if(!$_POST['edit']) {
        ?>

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Sayt istfadəçiləri
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form role="form" name="qeydiyyat_edit" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php print $qeydiyyat_info['id']?>" />                    
                            <div class="form-group">
                                <label>Name</label> <?php print stripslashes($qeydiyyat_info['name'])?><br>
                                <label>Mail</label> <?php print stripslashes($qeydiyyat_info['email'])?>
                            </div>
                            <div class="form-group">
                                <label>Permission</label>
                                <select name = "global_flag">
                                    <option value="0">Adi istifadəçi</option>
                                    <option value="1">Video yükləmək</option>
                                    <option value="2">Ancaq reklam</option>
                                    <option value="3">Reklam və Video yükləmək</option>
                                </select><?php print  "<script> qeydiyyat_edit.global_flag.value='".$qeydiyyat_info['global_flag']."'; </script>"; ?>
                            </div> 
                            <div class="form-group">
                                <label>Videolar</label>
                                <select name = "privilege">
                                    <option value="1">Aktiv olsun</option>
                                    <option value="0">Təsdiqləmə ilə</option>
                                </select><?php print  "<script> qeydiyyat_edit.privilege.value='".$qeydiyyat_info['privilege']."'; </script>"; ?>
                            </div> 
                            <div class="form-group">
                                <label>Facebook</label>
                                <select name = "fbshare">
                                    <option value="0">Share Olmasin</option>
                                    <option value="1">Share olsun</option>
                                </select><?php print  "<script> qeydiyyat_edit.fbshare.value='".$qeydiyyat_info['fbshare']."'; </script>"; ?>
                            </div> 
                            <div class="form-group">
                                <label>Gəlirlər</label>
                                <select name = "profit">
                                    <option value="0">Olmasin</option>
                                    <option value="1">Olsun</option>
                                </select><?php print  "<script> qeydiyyat_edit.profit.value='".$qeydiyyat_info['profit']."'; </script>"; ?>
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
        $update_data = array(
            'global_flag' => $_POST['global_flag'],
            'fbshare' => $_POST['fbshare'],
            'profit' => $_POST['profit'],
            'privilege' => $_POST['privilege']
        );       
        $db_link->where ('id', $id)->update ('qeydiyyat', $update_data);
        echo '<script>document.location.href="?menu=susers";</script>';
    }
}

if ($_GET['tip']==add_susers){
    if(!$_POST['add']) {
        ?>
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    Qeydiyyat
                </div>
                <div class="card-body">
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
                            <input class="btn btn-primary" name="add" type="submit" id="edit" value="Ok"> 
                            <input  class="btn btn-primary" type=button value="Cancel" onclick="javascript:history.go(-1);">
                        </center>                        
                    </form>
                </div>

            </div>
        </div>       
        <?php
    } 
    elseif($_POST['add']) {
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
        echo '<script>document.location.href="?menu=susers";</script>';
    }
}
?>