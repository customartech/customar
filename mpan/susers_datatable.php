<?php
session_start();
$security_test=1;
include("config.php");
include("function.php");
$Encryptor = new Encryption();
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$draw=$_GET['draw'];
$start=$_GET['start'];
$length=$_GET['length'];
$search=$_GET[search][value];
$st=0;

/*if($search){
$db_link->orWhere ('vid', "%$search%",'LIKE');  
$db_link->orWhere ('name', "%$search%",'LIKE');  
$db_link->orWhere ('description', "%$search%",'LIKE');  
}
$db_link->where ("category_id",$category_id);
$tbl_vimeo = $db_link->orderBy("id","desc")->get('vimeo');
$total_count=$db_link->count;*/

if($search){
    $db_link->orWhere ('ad', "%$search%",'LIKE');  
    $db_link->orWhere ('email', "%$search%",'LIKE');  
    $db_link->orWhere ('soyad', "%$search%",'LIKE');  
}
$tbl_qeydiyyat = $db_link->withTotalCount()->orderBy("id","desc")->get('qeydiyyat',array($start,$length));
$total_count=$db_link->totalCount; 
$this_count=$db_link->count; 
print '{"draw": '.$draw.',"recordsTotal": '.$total_count.', "recordsFiltered": '.$total_count.',"data": [';
foreach ($tbl_qeydiyyat as $qeydiyyat) {    
    $st++;
    $id = stripslashes($qeydiyyat['id']);
    //$full_name = stripslashes($qeydiyyat['name']);
    $full_name = chop($qeydiyyat['ad'])." ".chop($qeydiyyat['soyad']);
    $password = chop($qeydiyyat['password']);
    $isareler = array(",", '"', "/", "-");
    $full_name=str_replace($isareler, "", $full_name);
    $full_name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $full_name);

    $email = stripslashes($qeydiyyat['email']);
    $tel = stripslashes($qeydiyyat['tel']);
    $now_ip = stripslashes($qeydiyyat['now_ip']);
    $now_enter = stripslashes($qeydiyyat['now_enter']);

    $linkdel="onclick='return confirm();'";
    $actlink="<a href='?menu=susers&tip=delete_susers&cid=$id' $linkdel><span class='fa fa-trash'></span></a> ";
    $sondaxilolma=$now_ip."<br>".$now_enter;

    print '[
    "'.$id.'",
    "'.$sondaxilolma.'",
    "'.$full_name.'",
    "'.$tel.'",
    "'.$email.'",
    "'.$actlink.'"
    ]';
    if($this_count!=$st) print ",";
}
print "]}";