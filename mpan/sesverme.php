<?
 if(!isset($_SESSION['username']) || !isset($_SESSION['password']) || ($_SESSION['machine'] != $_SERVER['REMOTE_ADDR'])){
    header("Location: login.php");
    exit;
 }

    if( !(($_SESSION['flag'] & CUST_SUPERUSER) or ($_SESSION['flag'] & CUST_SESVERME)) ){
        echo "<h1>Access Denied";
        echo "<p>Sizin bu b&ouml;lm&#601;y&#601;y girm&#601;y&#601; haqq&#305;n&#305;z yoxdur</h1>";
        return;
    } 


 
    if ($_GET['empty_sesverme']) {
        $ses_id=$_GET['ses_id'];
           mysql_query("UPDATE sesverme Set ses='0' where basid='$ses_id'") or die ("Eror: ".mysql_error());
        mysql_query("DELETE FROM sesverme_log where ses_id='$ses_id'")or die ("Eror: ".mysql_error());
        print "<center><b>S&#305;f&#305;rlama tamamland&#305;</b></center><br>";
    }
    
    if ($_GET['delete_sesverme']) {
        $ses_id=$_GET['ses_id'];
        mysql_query("DELETE FROM sesverme_log where ses_id='$ses_id'")or die ("Eror: ".mysql_error());
        mysql_query("DELETE FROM sesverme where basid='$ses_id'") or die ("Eror: ".mysql_error());
        mysql_query("DELETE FROM sesverme where id='$ses_id'") or die ("Eror: ".mysql_error());
        print "<center><b>Silm&#601; tamamland&#305;</b></center><br>";
    }  
 
?>
<body bgcolor="#FFFFFF" text="#000000" link="#006699" vlink="#5493B4">
<form  action="" method="post" enctype="multipart/form-data">
    <?
    if ($_GET['tip']==add_sesverme){
        if ($_POST['daxil']){
            $q = mysql_query("show table status like 'sesverme'") or die(mysql_error());
            $son_id=mysql_result($q, 0, 'Auto_increment');            
            if (empty ($_POST['title'])){
                print "<center><b>B&uuml;t&uuml;n b&ouml;lm&#601;l&#601;ri doldurun.</b></center><br>";
            } else {            
                mysql_query("INSERT INTO sesverme (title, basid, lang) VALUES ('".$_POST['title']."', '0', '".$_POST['lang']."')")  or die ("Eror: ".mysql_error());
                for ($i = 1; $i <= 10; $i++) {
                    if ($_POST['cavab'.$i]){
                        mysql_query("INSERT INTO sesverme (title, basid, lang) VALUES ('".$_POST['cavab'.$i]."', '$son_id', '".$_POST['lang']."')")  or die ("Eror: ".mysql_error());
                    }
                }
                print "<script>document.location.href='?menu=sesverme';</script>";
            }    
        }
    ?>    
<table border="0" cellpadding="1" cellspacing="1" align="center" style="border: 1px solid #999999; background-color: #FFFFFF">
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90">Dil</td>
      <td style="border-style: dotted; border-width: 1px" align="left">
            <select name = "lang">
                <option value="az">Azərbaycanca</option>
                <option value="en">İngiliscə</option>
                <option value="ru">Rusca</option>
            </select>   
      </td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90">Ba&#351;l&#305;q</td>
      <td style="border-style: dotted; border-width: 1px" align="left"><input type="text" name="title" size="63"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 1</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab1" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 2</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab2" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 3</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab3" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 4</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab4" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 5</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab5" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 6</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab6" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 7</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab7" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 8</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab8" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 9</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab9" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="right" width="90" valign="top">
        Cavab 10</td>
      <td style="border-style: dotted; border-width: 1px"> 
        <input type="text" name="cavab10" size="45"></td>
    </tr>
    <tr>
      <td style="border-style: dotted; border-width: 1px" align="center" colspan="2"><input type="submit" value="Yenisini &#601;lav&#601; et" name="daxil"></td>
    </tr>
</table>
    <?
    }    
    ?>

<?
 if (empty($_GET['tip'])){
    $sql = "SELECT * from sesverme where basid=0 order by id DESC";
    $q = mysql_query($sql);
?>
<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border: 1px solid #999999; background-color: #FFFFFF">
  <tr>
    <td colspan=3><center><b><a href="?menu=sesverme&tip=add_sesverme">Yenisini &#601;lav&#601; et</a></b></center></td>
  </tr>
  <tr>
    <td width="75%" bgcolor="#999999"><strong>S&#601;sverm&#601;</strong></td>
    <td width="14%" align="center" bgcolor="#999999"><strong>S&#305;f&#305;rla</strong></td>
    <td width="11%" align="center" bgcolor="#999999"><strong>Sil</strong></td>
  </tr>
    <?
    while($sesverme = mysql_fetch_array($q)) { ?>
          <tr onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor=''">
            <td style="border-style: dotted; border-width: 1px"><b><?=$sesverme['lang']?> </b> <?=$sesverme['title']?></td>
            <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=sesverme&empty_sesverme=1&ses_id=<?=$sesverme['id']?>">S&#305;f&#305;rla</a></td>
            <td align="center" style="border-style: dotted; border-width: 1px"><a href="?menu=sesverme&delete_sesverme=1&ses_id=<?=$sesverme['id']?>">Sil</a></td>
          </tr>
    <?php
    }
    ?>
</table>
<?}?>
</form>