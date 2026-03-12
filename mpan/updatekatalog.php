<?php 
session_start();
$security_test=1;
if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
    header("Location: login.php");
    exit;
}
include("config.php");
$project=$_SESSION['site_project'];    
$array  = $_POST['arrayorder'];
$array1 = $_POST['arrayorder1'];
$array2 = $_POST['arrayorder2'];
$array3 = $_POST['arrayorder3'];
$array4 = $_POST['arrayorder4'];
$array5    = $_POST['arrayorder5'];
if($array){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array as $idval) {
            $query = "UPDATE katalog SET blok = " . $count . " WHERE id = " . $idval;
            mysql_query($query) or die('Səhf var');
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
            $query = "UPDATE katalog SET blok = " . $count . " WHERE project='$project' and id = " . $idval;
            mysql_query($query) or die('Səhf var');
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array2){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array2 as $idval) {
            $query = "UPDATE katalog SET blok = " . $count . " WHERE project='$project' and id = " . $idval;
            mysql_query($query) or die('Səhf var');
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array3){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array3 as $idval) {
            $query = "UPDATE katalog SET blok = " . $count . " WHERE project='$project' and id = " . $idval;
            mysql_query($query) or die('Səhf var');
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array4){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array4 as $idval) {
            $query = "UPDATE katalog SET blok = " . $count . " WHERE project='$project' and id = " . $idval;
            mysql_query($query) or die('Səhf var');
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}

if($array5){
    if ($_POST['update'] == "update"){
        $count = 1;
        foreach ($array5 as $idval) {
            $query = "UPDATE katalog SET blok = " . $count . " WHERE project='$project' and id = " . $idval;
            mysql_query($query) or die('Səhf var');
            $count ++;    
        }
        echo 'Məlumat dəyişdirildi';
    } 
}   
?>