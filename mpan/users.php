<?php

if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
if($_GET['type']=='password_change')    {
    $users = $db_link->where ("id",$userid)->getOne('users');
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
        $insert_data = array(
            'password' => $password           
        );  
        $db_link->where('id',$userid)->update('users', $insert_data);
        echo '<p align="center" style="color:#FF6600"><b>Password has been changed</b></p>';
    }
}

if ($_SESSION['flag'] & CUST_SUPERUSER){     
    if (empty($_GET['type'])) {

        if($_GET['type_p']=='password_change')	{
            $users = $db_link->where ("id",$_GET['uid'])->getOne('users');
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
                $insert_data = array(
                    'password' => $password           
                );  
                $db_link->where('id',$_GET['uid'])->update('users', $insert_data);
                echo '<p align="center" style="color:#FF6600"><b>Password has been changed</b></p>';
            }
        }

        $tbl_users = $db_link->get('users');
        ?>
        <table width="98%" border="0" style="border: 1px solid #999999; background-color: #FFFFFF" cellspacing="1" cellpadding="1">
        <tr>
            <td colspan="4" align="center"><a href="?menu=users&type=create"><strong>Add new user</strong></a></td>
        </tr>
        <tr>
            <td width="10%" bgcolor="#999999"><strong>Login</strong></td>
            <td width="75%" bgcolor="#999999"><strong>User</strong></td>
            <td align="center" bgcolor="#999999"> <strong>Edit </strong></td>
            <td align="center" bgcolor="#999999"> <strong>Change password </strong></td>
        </tr>
        <?php
        foreach ($tbl_users as $users) {
            ?>
            <tr onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor=''">
                <td style="border-style: dotted; border-width: 1px"><?php print $users['username']?></td>
                <td style="border-style: dotted; border-width: 1px"><?php print $users['full_name']?></td>
                <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=users&type=edit&uid=<?php print $users['id']?>"><strong>Edit</strong></a></td>
                <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=users&type_p=password_change&uid=<?php print $users['id']?>"><strong>Change password</strong></a></td>
            </tr>
            <?}
        echo "</table>\n";
    }

    if ($_GET['type']=='edit') {

        if ($_POST['update_user']) {
            $customer = array();
            reset( $_POST );
            foreach( $_POST as $key => $value ){
                if (preg_match('/^(?P<flag>CUST[A-Z_]+)/', $key, $regs)){
                    $customer[$value] |= constant( $regs['flag'] );
                }
            }
            reset( $_POST );

            reset( $customer );
            foreach ( $customer as $key => $value ){
                //$result = @mysql_query( "UPDATE users SET global_flag = '{$value}' WHERE id='".addslashes($_GET['uid'])."'");
                $insert_data = array(
                    'global_flag' => $value,
                );  
                $db_link->where('id',$_GET['uid'])->update('users', $insert_data);                
            }
            reset( $customer );

            $insert_data = array(
                'full_name' => $_POST['full_name'],
                'comment' => $_POST['comment']            
            );  
            $db_link->where('id',$_GET['uid'])->update('users', $insert_data);


            echo "<center><b>&#304;stfad&#601;&ccedil;i m&#601;lumatlar&#305; yenil&#601;ndi</b>\n<center>";
        }

        $users = $db_link->where ("id",$_GET['uid'])->getOne('users');
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
                </tr><!--
                <tr>
                <td align='right' width="87">Email:</td>
                <td><input type='text' name="email" size="50" value="<?php print $users['email']?>"></td>
                </tr> -->
                <tr>
                    <td align='right' width="87">Comment:</td>
                    <td><input type='text' name="comment"  value="<?php print $users['comment']?>" size="50"></td>
                </tr>
                <tr>
                    <td align='right' colspan="2">
                        <table width='100%' id="table1">
                            <tr>
                                <td align='right' width="134">Disabled</td>
                                <td><INPUT type="checkbox" name="CUST_DISABLED" <? print ($flag & CUST_DISABLED)?"checked":"" ?>></td>
                                <td align='right' width="136"></td>
                                <td width="20"></td>
                                <td width="156" align='right'></td>
                                <td width="67"></td>
                            </tr>
                            <tr>
                                <td align='right' width="134">Superuser</td>
                                <td><INPUT type="checkbox" name="CUST_SUPERUSER" <? print ($flag & CUST_SUPERUSER)?"checked":"" ?>></td>
                                <td align='right' width="136">Content Manager</td>
                                <td width="20"><INPUT type="checkbox" name="CUST_CONTENT" <? print ($flag & CUST_CONTENT)?"checked":"" ?>></td>
                                <td width="156">&nbsp;</td>
                                <td width="67">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' colspan=2>
                        <input accesskey='u' type="submit" name = "update_user" value="Update User">
                    <input accesskey='r' type='reset' value="Go Back" onclick="javascript:location.href='?menu=users';"> </td>
                </tr>
            </table>
        </form>
        <?}?>


    <?php
    if ($_GET['type']=='create') {
        if ($_POST['create']) {
            $insert_data = array(
                'full_name' => $_POST['full_name'],
                'username' => $_POST['username'],
                'password' => md5('lehlulu'.$_POST['password']),
                'comment' => $_POST['comment']            
            );  
            $newid=$db_link->insert('users', $insert_data);
            //print $db_link->getLastQuery();
            echo "<script>location.href='?menu=users&type=edit&uid=".$newid."'</script>";
        }
        ?>
        <table align='center' width='80%' id="table4">
        <tr>
        <td align='center'>
        <form action='' method='post' enctype='multipart/form-data'>
            <center>
            <table width='566' id="table5" style="border: 1px solid #999999; background-color: #FFFFFF">
                <tr>
                    <td align='right' width="87">Login:</td>
                    <td width='469'><input type='text' name="username" size="50"></td>
                </tr>
                <tr>
                    <td align='right' width="87">Password:</td>
                    <td width='469'><input type='text' name="password" size="50"></td>
                </tr>
                <tr>
                    <td align='right' width="87">Full name:</td>
                    <td width='469'><input type='text' name="full_name" size="50"></td>
                </tr>
                <tr>
                    <td align='right' width="87">Comment:</td>
                    <td><input type='text' name="comment" size="50"></td>
                </tr>
                <tr>
                    <td align='center' colspan=2>
                        <input accesskey='u' type="submit" name = "create" value="Create">
                    <input accesskey='r' type='reset' value="Go Back" onclick="javascript:location.href='?menu=users';"> </td>
                </tr>
            </table>
        </form>
        <?php }} ?>