<?php 
session_start();
$security_test=1;
include("config.php");
include("function.php");

if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$array  = $_POST['arrayorder'];
if($array){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array as $idval) {
            $update_data = array('sira' => $count);
            $db_link->where('id', $idval)->update('news', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    }
    $array1    = $_POST['arrayorder1'];
}  
?>