<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
if(!$security_test) exit;
?>
<iframe src="filemanager/dialog.php?type=0&fldr=1&editor=0&akey=<?php print $_SESSION['userAccessKey'];?>" frameborder="0" width="98%" height="700px"  style="padding:0;margin-top:8px;border-radius:4px;border: 1px solid #6B6B6B;box-shadow:0 0 4px #6B6B6B;"></iframe>
