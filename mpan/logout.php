<?php
session_start();
include("config.php");
	unset($_SESSION['machine']);
	unset($_SESSION['username']);
	unset($_SESSION['userid']);
    unset($_SESSION['password']);
    unset($_SESSION['loggedin']);
    session_destroy();
	header("Location: index.php");
?>