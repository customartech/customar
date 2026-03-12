<?php
session_start();
$security_test=1;
include_once("../sqlinj.php");

$sqlyoxlama=new sqlinj;
$sqlyoxlama->basla("aio","all");
if($_GET['lang']) $_SESSION['lang'] = $_GET['lang'];
if(!($_SESSION['lang'])) $_SESSION['lang'] = 'az'; $lang = $_SESSION['lang'];
if(!$_SESSION['HTTP_REFERER']) $_SESSION['HTTP_REFERER']=$_SERVER['HTTP_REFERER']; 
if(!($_SESSION['users']))  $_SESSION['users']=array();
include("config.php");
include("function.php");
$Encryptor = new Encryption();
if(($_GET['email']) and ($_GET['pass'])){
    $username = $Encryptor->Decrypt($_GET['email']);
    $password = $_GET['pass'];
    //print  $username.$password;
    //$key = array_search($username, $_SESSION['users']);
    //$us=array_column($_SESSION['users'], 'username');   

    $db_link->where ("email", $username);
    $db_link->where ("password", $password);
    $db_link->where ("status", "active");
    $userinfo = $db_link->getOne ("channels");
    if($db_link->count>0) { 
        $_SESSION['access_key']=md5(yazi(25));
        $_SESSION['username'] = $userinfo['email'];
        $_SESSION['global_flag'] = $userinfo['global_flag'];
        $_SESSION['privilege'] = $userinfo['privilege'];
        $_SESSION['profit'] = $userinfo['profit'];
        $_SESSION['name'] = $userinfo['name'];
        $_SESSION['fbshare'] = $userinfo['fbshare'];
        $_SESSION['logo'] = $userinfo['logo_t'];
        $_SESSION['description'] = $userinfo['description'];
        $_SESSION['userid'] = $userinfo['id'];
        $_SESSION['machine'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['loggedin']=true;
        $_SESSION['project']='sayt';

        $data = Array (
            'last_enter' => $userinfo['now_enter'],
            'last_ip' => $userinfo['now_ip'],
            'now_enter' => $db_link->now(),
            'now_ip' => $_SESSION['machine']
        );

        $db_link->where ('id', $userinfo['id']); 
        $db_link->update('channels',$data);
        $HTTP_REFERER=$_SESSION['HTTP_REFERER'];
        $_SESSION['HTTP_REFERER']='';
        unset($_SESSION['HTTP_REFERER']);
        echo "<script>window.location.href='/';</script>";
        //echo "<script>window.top.location.reload();</script>";
        //print "<pre>";
        //print_r($_SESSION['users']);
        exit;
    }
    else {
        if(count($_SESSION['users'])>0){
            $error = "İstifadəçi adı vəya parol səhvdir";    
        }else{
            $_SESSION['loggedin']=false;
            $error = "İstifadəçi adı vəya parol səhvdir";  
        }

    }
} else {
    if(count($_SESSION['users'])>0){
        $error = "Bütün bölmələri doldurun";    
    }else{
        $_SESSION['loggedin']=false;
        $error = "Bütün bölmələri doldurun";  
    }        
}
?>