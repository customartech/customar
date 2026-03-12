<?php

    if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
        header("Location: login.php");
        exit;
    }

    if(!$security_test) exit;

    if (empty($_GET['type'])) {
        if($_GET['type_p']==password_change)	{
            $sql = "SELECT * FROM users WHERE where id='".$_SESSION['userid']."'";
            $q = mysql_query($sql);
            $users = mysql_fetch_array($q);
            if(!$_POST['change']) {
            ?>
            <form method="post" action="">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="14%" bgcolor="#999999">&nbsp;</td>
                        <td width="82%" align="center" bgcolor="#999999"><strong>User <?php echo $users['username']?> a new password </strong></td>
                        <td width="4%" bgcolor="#999999">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="14%">&nbsp;</td>
                        <td width="82%">&nbsp;</td>
                        <td width="4%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><strong>New Password</strong></td>
                        <td><input type="text" name="new_password">
                            <input name="change" type="submit" id="change" value="Change password"></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
            <?php
            } else {
                $hash = "lehlulu";
                $pass2encrypt = $hash.addslashes($_POST['new_password']);
                $password = md5($pass2encrypt);
                $sql = "UPDATE users SET password = '".$password."' where id='".$_SESSION['userid']."'";
                $q = mysql_query($sql);
                if(!$q) {
                    echo '<p align="center" style="color:#FF6600"><b>An error occurred while trying to change the password. Please try again later some time.</b></p>';
                } else {
                    echo '<p align="center" style="color:#FF6600"><b>Password has been changed</b></p>';
                }
            }
        }

        
        
        $sql = "SELECT * FROM users where id='".$_SESSION['userid']."'";
        $q = mysql_query($sql);
    ?>
    <table width="98%" border="0" style="border: 1px solid #999999; background-color: #FFFFFF" cellspacing="1" cellpadding="1">
    <tr>
        <td colspan="2" align="center"><strong>Users</strong></td>
    </tr>
    <tr>
        <td width="75%" bgcolor="#999999"><strong>User</strong></td>
        <td align="center" bgcolor="#999999"> <strong>Change password </strong></td>
    </tr>
    <?php
        while($users = mysql_fetch_array($q)) { ?>
        <tr onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor=''">
            <td style="border-style: dotted; border-width: 1px"><a href="?menu=edit_user&type=edit"><?php print $users['full_name']?></a></td>
            <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=edit_user&type_p=password_change"><strong>Change password</strong></a></td>
        </tr>
        <?php 
        }
        echo "</table>\n";
    }

    
    if ($_GET['type']==edit) {

        if ($_POST['update_user']) {
            $result = @mysql_query( "UPDATE users SET full_name = '".$_POST['full_name']."',email='".$_POST['email']."',comment='".$_POST['comment']."'  where id='".$_SESSION['userid']."'");
            echo "<center><b>&#304;stfad&#601;&ccedil;i m&#601;lumatlar&#305; yenil&#601;ndi</b>\n<center>";
        }

        $sql = "SELECT * FROM users where id='".$_SESSION['userid']."'";
        $q = mysql_query($sql);
        $users = mysql_fetch_array($q);
        $flag=$users['global_flag'];
    ?>
    <table align='center' width='80%' id="table4">
    <tr>
    <td align='center'>
    <form action='' method='post' enctype='multipart/form-data'>
        <center>
        <table width='566' id="table5" style="border: 1px solid #999999; background-color: #FFFFFF">
            <tr>
                <td align='right' width="87">Login:</td>
                <td width='469'><input type='text' disabled name="login" size="50" value="<?php print $users['username']?>"></td>
            </tr>
            <tr>
                <td align='right' width="87">Full name:</td>
                <td width='469'><input type='text' name="full_name" size="50" value="<?php print $users['full_name']?>"></td>
            </tr>
            <tr>
                <td align='right' width="87">Email:</td>
                <td><input type='text' name="email" size="50" value="<?php print $users['email']?>"></td>
            </tr>
            <tr>
                <td align='right' width="87">Comment:</td>
                <td><input type='text' name="comment"  value="<?php print $users['comment']?>" size="50"></td>
            </tr>
            <tr>
                <td align='center' colspan=2>
                    <input accesskey='u' type="submit" name = "update_user" value="Update User">
                <input accesskey='r' type='reset' value="Go Back" onclick="javascript:location.href='?menu=edit_user';"> </td>
            </tr>
        </table>
    </form>
    <?php }?>