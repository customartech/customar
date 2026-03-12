<?php 
session_start();
$security_test=1;
include("config.php");
include("function.php");

if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$project=$_SESSION['site_project'];    
$array  = $_POST['arrayorder0'];
$array1 = $_POST['arrayorder1'];
$array2 = $_POST['arrayorder2'];
$array3 = $_POST['arrayorder3'];
$array4 = $_POST['arrayorder4'];
$array5    = $_POST['arrayorder5'];
if($array){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array as $idval) {
            $update_data = array('blok' => $count);
            $db_link->where('id', $idval)->update('category', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    }
    $array1    = $_POST['arrayorder1'];
}

if($array1){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array1 as $idval) {
            $update_data = array('blok' => $count);
            $db_link->where('id', $idval)->update('category', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array2){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array2 as $idval) {
            $update_data = array('blok' => $count);
            $db_link->where('id', $idval)->update('category', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array3){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array3 as $idval) {
            $update_data = array('blok' => $count);
            $db_link->where('id', $idval)->update('category', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array4){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array4 as $idval) {
            $update_data = array('blok' => $count);
            $db_link->where('id', $idval)->update('category', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array5){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array5 as $idval) {
            $update_data = array('blok' => $count);
            $db_link->where('id', $idval)->update('category', $update_data);
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}   
?>