<?php
session_start();
$security_test=1;
include("config.php");
include("function.php");
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}
$draw=$_GET['draw'];
$start=$_GET['start'];
$length=$_GET['length'];
$category_id=$_GET['category_id'];
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
    $db_link->orWhere ('vid', "%$search%",'LIKE');  
    $db_link->orWhere ('name', "%$search%",'LIKE');  
    $db_link->orWhere ('description', "%$search%",'LIKE');  
}
$db_link->where ("category_id",$category_id);
$tbl_vimeo = $db_link->withTotalCount()->orderBy("id","desc")->get('reklam',array($start,$length));
$total_count=$db_link->totalCount; 
$this_count=$db_link->count; 
print '{"draw": '.$draw.',"recordsTotal": '.$total_count.', "recordsFiltered": '.$total_count.',"data": [';
foreach ($tbl_vimeo as $vimeo) {    
    $st++;
    $id = stripslashes($vimeo['id']);
    $vid = stripslashes($vimeo['vid']);
    $create_date = stripslashes($vimeo['create_date']);
    $status = stripslashes($vimeo['status']);
    $name =str_replace("'","",$vimeo['name']);
    $name =str_replace('"',"",$name);
    $redirect_link = stripslashes($vimeo['redirect_link']);
    $plays = addslashes($vimeo['plays']);
    $max_plays = addslashes($vimeo['max_plays']);
    $daily_plays = addslashes($vimeo['daily_plays']);
    $max_daily_plays = addslashes($vimeo['max_daily_plays']);
    $status = stripslashes($vimeo['status']);
    if ($pictures) $pictures ="<img src='$pictures' border=0 style='width:120px;'>";
    $linkdel="onclick='return confirm();'";
    $delbutton= "<a href='?menu=reklam&tip=delete_reklam&category_id=$category_id&cid=$id' $linkdel><span class='fa fa-trash'></span></a>";
    $editbutton="<a rel=tooltip title='REDAKTƏ ET' href='?menu=reklam&tip=edit_reklam&category_id=$category_id&cid=$id'><span class='fa fa-pencil'></span></a>";

    if($status) 
        $publish="<a rel=tooltip title='Unpublish' href='?menu=reklam&tip=unpublish&category_id=$category_id&cid=$id'><span class='fa fa-check'></span></a>"; 
    else 
        $publish="<a rel=tooltip title='publish' href='?menu=reklam&tip=publish&category_id=$category_id&cid=$id'><span class='fa fa-close'></span></a>";

    $actlink="$publish  | $editbutton | $delbutton";
    print '[
    "'.$vid.'",
    "'.$create_date.'",
    "'.$name.'",
    "'.$redirect_link.'",
    "'.$plays.'",
    "'.$daily_plays.'",
    "'.$max_daily_plays.'",
    "'.$max_plays.'",
    "'.$actlink.'"
    ]';
    if($this_count!=$st) print ",";
}
print "]}";