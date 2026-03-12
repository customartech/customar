<?php
session_start();
include("config.php");
include("function.php");
if($_POST['giris']) {
    unset($_SESSION['machine']);
    unset($_SESSION['username']);
    unset($_SESSION['userid']);
    unset($_SESSION['password']);
    unset($_SESSION['loggedin']);
    unset($_SESSION['project']);
    unset($_SESSION['userAccessKey']);

    $username = addslashes($_POST['user']);
    $pass = addslashes($_POST['pass']);
    $hash = 'lehlulu';
    $pass2encrypt = $hash.$pass;
    $password = md5($pass2encrypt);

    $db_link->where ("username", $username);
    $db_link->where ("password", $password);
    $userinfo = $db_link->getOne ("users");
    if($db_link->count>0) { 
        $_SESSION['flag'] = $userinfo['global_flag'];
        if($_SESSION['flag'] & CUST_DISABLED){
            $_SESSION['loggedin']=false;
            $error = "Sizin bu b&ouml;lm&#601;y&#601; <br>girm&#601;y&#601; haqq&#305;n&#305;z yoxdur";
        }else{
            $_SESSION['access_key']=md5(yazi(25));
            $_SESSION['username'] = $userinfo['username'];
            $_SESSION['userid'] = $userinfo['id'];
            $_SESSION['last_login'] = $userinfo['last_enter'];
            $_SESSION['last_ip'] = $userinfo['last_ip'];
            $_SESSION['machine'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['userAccessKey']=yazi(20);
            $_SESSION['loggedin']=true;
            $_SESSION['project']='admin';

            //$data = array('LAST_LOGIN' => date('Y-m-d H:i:s'));

            $data = Array (
                'last_enter' => $userinfo['now_enter'],
                'last_ip' => $userinfo['now_ip'],
                'now_enter' => $db_link->now(),
                'now_ip' => $_SESSION['machine']
            );

            $db_link->where ('id', $userinfo['id']); 
            $db_link->update('users',$data);

            //print $db_link->getLastQuery(); 
            header("Location: index.php");
        }
    } else {
        $_SESSION['loggedin']=false;
        $error = "Username and/or <br> password is wrong";
    }
}else{
    $_SESSION['loggedin']=false;
} 


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Login</title>
        <!-- Core CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="assets/css/login.css" rel="stylesheet">
        <!-- Checkboxes style -->
        <link href="assets/css/bootstrap-checkbox.css" rel="stylesheet">
    </head>
    <body>
        <div class="login-menu">
            <div class="container">
                <nav class="nav">
                    <a class="nav-link" href="/"><img width="50px" src="/assets/img/logos/logo.png" /></a>
                    <a class="nav-link active" href="login.php">Login</a>
                </nav>
            </div>
        </div>

        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="card">
                    <h4 class="card-header">Login</h4>
                    <div class="card-body">

                        <!--<div class="alert alert-success" role="alert">
                            <h5 class="alert-heading">Demo Login</h5>
                            <p>Email: demo@pikeadmin.com<br />Password: 123456</p>
                        </div>-->

                        <?php if($error) print '<p class="text-danger"><b>Error!</b> '.$error.'</p>';?>
                        <form data-toggle="validator" role="form" method="post" action="">                                

                            <div class="row">    
                                <div class="col-md-12">    
                                    <div class="form-group">
                                        <label>Login </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                            <input type="text" class="form-control" name="user" data-error="Input valid text" required>                                  
                                        </div>                                
                                        <div class="help-block with-errors text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-unlock" aria-hidden="true"></i></span>
                                            <input type="password" id="inputPassword" data-minlength="6" name="pass" class="form-control" data-error="Password to short" required />
                                        </div>    
                                        <div class="help-block with-errors text-danger"></div>
                                    </div>
                                </div>
                            </div>
<!--
                            <div class="row">                                
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox_remember" type="checkbox" name="remember">
                                    <label for="checkbox_remember"> Remember me</label>
                                </div>    
                            </div>-->

                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="redirect" value="" />
                                    <input type="submit" class="btn btn-primary btn-lg btn-block" value="Login" name="giris" />
                                </div>
                            </div>
                        </form>
                        <!--
                        <div class="clear"></div> 

                        <i class="fa fa-user fa-fw"></i> No account yet? <a href="register.php">Register new account</a><br />
                        <i class="fa fa-undo fa-fw"></i> Forgot password? <a href="reset-password.php">Reset password</a> -->

                    </div>    

                </div>    

            </div>    
        </div>

        <footer class="footer">
            <div class="container">
                <span class="text-muted"><a target="_blank" href="https://mahmudlu.az/"><b>MTIO</b></a></span>
            </div>
        </footer>

        <!-- Core Scripts -->
        <script src="assets/js/jquery-1.10.2.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>

        <!-- Bootstrap validator  -->
        <script src="assets/js/validator.min.js"></script>

    </body>
</html>