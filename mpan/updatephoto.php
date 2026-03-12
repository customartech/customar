<?php 
    session_start();
    $security_test=1;
    if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
        header("Location: login.php");
        exit;
    }
    include("config.php");
    $category_id = addslashes($_GET['category_id']);
    $array  = $_POST['arrayorder'];
    if($array){
        if ($_POST['update'] == "update"){
            $count = 1;
            foreach ($array as $idval) {
                $query = "UPDATE photos SET sira = " . $count . " WHERE id = " . $idval." and m_id=".$category_id;
                mysql_query($query) or die('Səhf var');
                $count ++;    
            }
            echo 'Məlumat dəyişdirildi';
        }
        $array1    = $_POST['arrayorder1'];
    }else{
       print 'Səhf var';
    }
?>