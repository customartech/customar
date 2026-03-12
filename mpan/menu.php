<?php
if(!is_logged_in()){
    header("Location: login.php");
    exit;
}

if(!$security_test) exit;
if($_SESSION['flag'] & CUST_SUPERUSER){
    ?>
    <li class="submenu">
        <a class="pro" href="#"><i class="fa fa-fw fa-cog"></i><span> Settings </span> <span class="menu-arrow"></span></a>
        <ul class="list-unstyled"> 
            <li><a href='?menu=menyu'><i class='fa fa-wrench fa-fw'></i> Menu</a></li>
            <li><a href='?menu=services_service'><i class='fa fa-wrench fa-fw'></i> Services</a></li>
<!--            <li><a href='?menu=services_industry'><i class='fa fa-wrench fa-fw'></i> Services Segment</a></li>
            <li><a href='?menu=services_solution'><i class='fa fa-wrench fa-fw'></i> Services Details</a></li> -->
            <li><a href='?menu=sifaris'><i class='fa fa-wrench fa-fw'></i> GET A QUOTE</a></li>
            <li><a href='?menu=fmanager'><i class='fa fa-folder fa-fw'></i> File Manager</a></li>                   
            <li><a href='?menu=users'><i class='fa fa-users fa-fw'></i> Users</a></li>                   
        </ul>
    </li>
    <?php
}

function buildTree($sub_id=0,$db_link) {
    global $category_id;
    if($_SESSION['flag'] & CUST_SUPERUSER){
        $cat_menyus = $db_link->where("sub_id",$sub_id)->orderBy("blok","asc")->get('category');
        foreach ($cat_menyus as $cat_menyu) {
            $menyuico="file-text-o";
            $cat_menyu_id=$cat_menyu['id'];  
            if($cat_menyu['type']=='news') 
                $menyulink="?menu=xeber&category_id=".$cat_menyu['id'];  
            if($cat_menyu['type']=='ourteam')
                $menyulink="?menu=ourteam&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='services')
                $menyulink="?menu=services&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='projects')
                $menyulink="?menu=projects&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='content'){
                $menyulink="?menu=content&tip=edit_content&cid=".$cat_menyu['id'];
/*                if($cat_menyu_id==26) $menyuico="copyright";
                if($cat_menyu_id==27) $menyuico="tasks";
                if($cat_menyu_id==28) $menyuico="shopping-cart";
                if($cat_menyu_id==31) $menyuico="television"; */
            } 
            if($cat_menyu['type']=='photos')
                $menyulink="?menu=photos&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='file')
                $menyulink="?menu=files&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='links')
                $menyulink="?menu=links&tip=edit_links&id=".$cat_menyu['id'];
            if($cat_menyu['type']=='katalog')
                $menyulink="?menu=katalog&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='vimeo'){
                $menyulink="?menu=vimeo&category_id=".$cat_menyu['id'];
                $menyuico="file-video-o";
            }

            if($cat_menyu['type']=='reklam'){
                $menyulink="?menu=reklam&category_id=".$cat_menyu['id'];
                $menyuico="bar-chart";
            }
            if($cat_menyu['type']=='multimedia')
                $menyulink="?menu=multimedia&category_id=".$cat_menyu['id'];

            if($cat_menyu_id==$category_id) 
                $cat_menyu_b="<b>".$cat_menyu['name_az']."</b>"; 
            else 
                $cat_menyu_b=$cat_menyu['name_az']; 

            $count = $db_link->where("sub_id",$cat_menyu_id)->getValue ("category", "count(*)");

            if($count>0){
                print "<li class='submenu'><a href='$menyulink' style='float: left; z-index: 1;'><span class='fa fa-pencil'></span></a><a href='JavaScript:;'><span>$cat_menyu_b</span><span class='menu-arrow'></span></a><ul class='list-unstyled'>"; 
                buildTree($cat_menyu_id,$db_link);
                print "</ul></li>";
            }else{
                print "<li class='submenu'><a href='$menyulink'><i class='fa fa-fw fa-$menyuico'></i> <span>$cat_menyu_b</span></a>";
                print "</li>";
            }
        }
    }else{
        $cat_menyus = $db_link->where("sub_id",$sub_id)->where("type",'vimeo')->orderBy("blok","asc")->get('category');
        foreach ($cat_menyus as $cat_menyu) {
            $menyuico="file-text-o";
            $cat_menyu_id=$cat_menyu['id'];  
            if($cat_menyu['type']=='news') 
                $menyulink="?menu=xeber&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='content'){
                $menyulink="?menu=content&tip=edit_content&cid=".$cat_menyu['id'];
/*                if($cat_menyu_id==26) $menyuico="copyright";
                if($cat_menyu_id==27) $menyuico="tasks";
                if($cat_menyu_id==28) $menyuico="shopping-cart";
                if($cat_menyu_id==31) $menyuico="television";*/
            } 
            if($cat_menyu['type']=='photos')
                $menyulink="?menu=photos&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='file')
                $menyulink="?menu=files&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='links')
                $menyulink="?menu=links&tip=edit_links&id=".$cat_menyu['id'];
            if($cat_menyu['type']=='katalog')
                $menyulink="?menu=katalog&category_id=".$cat_menyu['id'];
            if($cat_menyu['type']=='vimeo'){
                $menyulink="?menu=vimeo&category_id=".$cat_menyu['id'];
                $menyuico="file-video-o";
            }

            if($cat_menyu['type']=='reklam'){
                $menyulink="?menu=reklam&category_id=".$cat_menyu['id'];
                $menyuico="bar-chart";
            }
            if($cat_menyu['type']=='multimedia')
                $menyulink="?menu=multimedia&category_id=".$cat_menyu['id'];

            if($cat_menyu_id==$category_id) 
                $cat_menyu_b="<b>".$cat_menyu['name_az']."</b>"; 
            else 
                $cat_menyu_b=$cat_menyu['name_az']; 

            $count = $db_link->where("sub_id",$cat_menyu_id)->getValue ("category", "count(*)");

            if($count>0){
                print "<li class='submenu'><a href='JavaScript:;'><i class='fa fa-fw fa-$menyuico'></i> <span>$cat_menyu_b</span><span class='menu-arrow'></span></a><ul class='list-unstyled'>"; 
                buildTree($cat_menyu_id,$db_link);
                print "</ul></li>";
            }else{
                print "<li class='submenu'><a href='$menyulink'><i class='fa fa-fw fa-$menyuico'></i> <span>$cat_menyu_b</span></a>";
                print "</li>";
            }
        }

    }
}

buildTree(0,$db_link);
?>