<?php
if (!$security_test) exit;

function site_is_logged_in()
{
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['project'] == 'sayt') {
        return true;
    } else {
        return false;
    }
}

function ip_to_country($db_link)
{
    $ip = ip2long($_SERVER['REMOTE_ADDR']);
    $db_link->where('IP_FROM', $ip, "<=");
    $db_link->where('IP_TO', $ip, ">=");
    return $db_link->getValue("ip_to_country", "CTRY");
}

function change_lang($new_lang) {
    global $lang,$type,$pages_site;
    $this_url=explode('?',$_SERVER['REQUEST_URI']);
    if(in_array($type, $pages_site) ){
        if($type=='main')
            return "/$new_lang/main/";    
        else
            return str_replace('/'.$lang.'/','/'.$new_lang.'/',$this_url[0]);
    }else
        return "/$new_lang/main/"; 
}

function combo_solution($category_id, $db_link)
{
    global $details,$lang;
    $cat_menyus = $db_link->get('services_solution');
    print "<select style='height:45.5px;' name='solution' id='select_solution' class='browser-default custom-select'>";
    print "<option value='0'> $details </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $services_industry_id = $line["services_industry_id"];
        $ad = stripslashes($line["title_".$lang]);
        if ($category_id == $id)
            print "<option id='$services_industry_id' value='$id' selected='selected'>$ad</option>";
        else
            print "<option id='$services_industry_id' value='$id'>$ad</option>";
    }
    print "</select>";
}

function latin_slug($s){
    $s = trim(mb_strtolower((string)$s, 'UTF-8'));
    $map = [
        'ə' => 'e', 'ı' => 'i', 'ö' => 'o', 'ü' => 'u', 'ğ' => 'g', 'ç' => 'c', 'ş' => 's',
        'Ə' => 'e', 'I' => 'i', 'İ' => 'i', 'Ö' => 'o', 'Ü' => 'u', 'Ğ' => 'g', 'Ç' => 'c', 'Ş' => 's',
    ];
    $s = strtr($s, $map);
    $s = preg_replace('/[^a-z0-9\s\-]/', '', $s);
    $s = preg_replace('/[\s\-]+/', '-', $s);
    return trim($s, '-');
}

function legacy_slug($s){
    if (function_exists('url_slug')) {
        $s = (string)url_slug((string)$s);
    }
    $s = trim(mb_strtolower((string)$s, 'UTF-8'));
    $s = preg_replace('/[^a-z0-9\-]/', '', $s);
    $s = preg_replace('/\-+/', '-', $s);
    return trim($s, '-');
}

function resolve_category_id_by_slug($type, $slug, $lang, $db_link){
    $slug = (string)$slug;
    if ($slug === '') return null;
    $rows = $db_link->where('type', $type)->where('status', 'active')->get('category', null, ['id', 'name_'.$lang]);
    foreach ($rows as $r) {
        $name = (string)($r['name_'.$lang] ?? '');
        if ($name !== '' && (latin_slug($name) === $slug || legacy_slug($name) === $slug)) {
            return (int)$r['id'];
        }
    }
    return null;
}

function resolve_service_id_by_slug($slug, $lang, $db_link){
    $slug = (string)$slug;
    if ($slug === '') return null;
    $rows = $db_link->get('services_service', null, ['id', 'title_'.$lang]);
    foreach ($rows as $r) {
        $name = (string)($r['title_'.$lang] ?? '');
        if ($name !== '' && (latin_slug($name) === $slug || legacy_slug($name) === $slug)) {
            return (int)$r['id'];
        }
    }
    return null;
}

function resolve_news_id_by_slug($category_id, $service_id, $slug, $lang, $db_link){
    $category_id = (int)$category_id;
    $service_id = (int)$service_id;
    $slug = (string)$slug;
    if ($category_id <= 0 || $slug === '') return null;
    $q = $db_link->where('category_id', $category_id);
    if ($service_id > 0) {
        $q = $q->where('service', $service_id);
    }
    $rows = $q->get('news', null, ['id', 'title_'.$lang]);
    foreach ($rows as $r) {
        $t = (string)($r['title_'.$lang] ?? '');
        if ($t !== '' && (latin_slug($t) === $slug || legacy_slug($t) === $slug)) {
            return (int)$r['id'];
        }
    }
    return null;
}

function apply_slug_routes_and_redirect($request_path, $lang, $db_link){
    $request_path = (string)$request_path;
    if (preg_match('~^/([A-Za-z]+)/portfolio/?$~', $request_path, $m)) {
        $_GET['lang'] = $m[1];
        $_GET['type'] = 'news';
        $_GET['cid'] = 1;
        return;
    }
    if (preg_match('~^/([A-Za-z]+)/portfolio/([^/]+)/?$~', $request_path, $m)) {
        $_GET['lang'] = $m[1];
        $_GET['type'] = 'news';
        $_GET['cid'] = 1;
        $sid = resolve_service_id_by_slug($m[2], $lang, $db_link);
        if ($sid) {
            $_GET['subid'] = $sid;
            $service_name = (string)$db_link->where('id', (int)$sid)->getValue('services_service', 'title_'.$lang);
            $canonical = latin_slug($service_name);
            if ($canonical !== '' && $canonical !== $m[2]) {
                header('Location: /'.$m[1].'/portfolio/'.$canonical, true, 301);
                exit;
            }
        }
        return;
    }
    if (preg_match('~^/([A-Za-z]+)/portfolio/([^/]+)/([^/]+)/?$~', $request_path, $m)) {
        $_GET['lang'] = $m[1];
        $_GET['type'] = 'opennews';
        $sid = resolve_service_id_by_slug($m[2], $lang, $db_link);
        $nid = resolve_news_id_by_slug(1, (int)($sid ?? 0), $m[3], $lang, $db_link);
        if ($nid) {
            $_GET['cid'] = $nid;
            $service_name = '';
            if (!empty($sid)) {
                $service_name = (string)$db_link->where('id', (int)$sid)->getValue('services_service', 'title_'.$lang);
            }
            $news_title = (string)$db_link->where('id', (int)$nid)->getValue('news', 'title_'.$lang);
            $canonical_service = latin_slug($service_name);
            $canonical_project = latin_slug($news_title);
            if ($canonical_service !== '' && $canonical_project !== '' && ($canonical_service !== $m[2] || $canonical_project !== $m[3])) {
                header('Location: /'.$m[1].'/portfolio/'.$canonical_service.'/'.$canonical_project, true, 301);
                exit;
            }
        }
        return;
    }
    if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/([^/]+)/?$~', $request_path, $m)) {
        $_GET['lang'] = $m[1];
        $_GET['type'] = $m[2];
        $cat_id = resolve_category_id_by_slug($m[2], $m[3], $lang, $db_link);
        if ($cat_id) {
            $_GET['cid'] = $cat_id;
            $cat_name = (string)$db_link->where('id', (int)$cat_id)->getValue('category', 'name_'.$lang);
            $canonical = latin_slug($cat_name);
            if ($canonical !== '' && $canonical !== $m[3]) {
                header('Location: /'.$m[1].'/'.$m[2].'/'.$canonical, true, 301);
                exit;
            }
        }
        return;
    }
}

function combo_industry($category_id, $db_link)
{
    global $segment,$lang;
    $cat_menyus = $db_link->get('services_industry');
    print "<select style='height:45.5px;' name='industry' id='select_industry' class='browser-default custom-select'>";
    print "<option value='0'> $segment </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $services_service_id = $line["services_service_id"];
        $ad = stripslashes($line["title_".$lang]);
        if ($category_id == $id)
            print "<option id='$services_service_id' value='$id' selected='selected'>$ad</option>";
        else
            print "<option id='$services_service_id' value='$id'>$ad</option>";
    }
    print "</select>";
}

function combo_service($category_id, $db_link)
{
    global $services,$lang;
    if (empty($lang)) $lang = 'az';
    $cat_menyus = $db_link->get('services_service');
    print "<select style='height:45.5px;' name='service' id='select_service' class='browser-default custom-select'>";
    print "<option value='0'> $services </option>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes((string)($line["title_".$lang] ?? ''));
        if ($category_id == $id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
    print "</select>";
}

function menyu_service($tip,$db_link){
    global $lang,$subid;
    if (empty($lang)) $lang = 'az';
    $cat_menyus = $db_link->get('services_service');
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes((string)($line["title_".$lang] ?? ''));
        $ad_slug = latin_slug($ad);
        if($tip==1)
            print "<li><a href='/$lang/portfolio/$ad_slug' class='xidm'>$ad<span></span></a></li>";
        else{
            if($subid==$id)
                print "<li class='filter-item filter-item-active'><a href='/$lang/portfolio/$ad_slug' class='kats'>$ad</a></li>";
            else
                print "<li class='filter-item'><a href='/$lang/portfolio/$ad_slug' class='kats'>$ad<span></a></li>";

        }
    }
}

function menyu_cont_service($db_link){
    global $lang;
    $cat_menyus = $db_link->get('services_service');
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        home_news_blok($id, $lang, $db_link);
    }
}

function testimonial($db_link, $lang){ 
    global $testimonials;
    print "<section style='background-color: #f2f2f2;'>
    <!--Spacer-->
    <div class='ptf-spacer' style=' --ptf-xxl: 8.75rem; --ptf-md: 4.375rem;'></div>
    <div class='container-xxl'>
    <div class='row align-items-center'>
    <div class='col-8'>
    <!--Animated Block-->
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <h2 class='h2 d-inline-flex'>$testimonials</h2>
    </div>
    </div>
    <div class='col-4 text-end'>
    <!--Animated Block-->
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <!--Slider Controls-->
    <div class='ptf-slider-controls ptf-slider-controls--style-3 ptf-review-slider'>
    <div class='ptf-swiper-button-prev ptf-swiper-button-prev--style-3'><i class='lnil lnil-chevron-left'></i></div>
    <div class='ptf-swiper-button-next ptf-swiper-button-next--style-3'><i class='lnil lnil-chevron-right'></i></div>
    </div>
    </div>
    </div>
    </div>
    <!--Spacer-->
    <div class='ptf-spacer' style=' --ptf-xxl: 4.375rem;'></div>
    <!--Animated Block-->
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <!--Content Slider-->
    <div class='ptf-content-slider swiper-container ' data-cursor='' data-navigation-anchor='.ptf-review-slider' data-gap='170' data-loop='enable' data-speed='1000' data-autoplay='true' data-autoplay-speed='5000' data-slides-centered='' data-slide-settings='{&quot;slides_to_show_tablet&quot;:2,&quot;slides_to_show&quot;:2}' data-free-mode='' data-slider-offset='' data-mousewheel=''>
    <div class='swiper-wrapper'>";
    $i = 0;
    $tbl_news = $db_link->arraybuilder()->where('category_id', 9)->orderBy("id", "desc")->get('news', array(0, 15));
    $totalpage = $db_link->totalPages;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $client = stripslashes($line["client"]);
        $content = $line["content_" . $lang];

        print "                                        <div class='swiper-slide'>
        <div class='ptf-twitter-review ptf-twitter-review--style-1'>
        <div class='ptf-twitter-review__header'>
        <div class='ptf-twitter-review__meta'>
        <h6 class='ptf-twitter-review__author'>$basliq</h6>
        <div class='ptf-twitter-review__info'>$client</div>
        </div>
        </div>
        <div class='ptf-twitter-review__content'>
        $content
        </div>
        </div>
        </div>";

    }

    print "</div>
    </div>
    </div>
    </div>
    <!--Spacer-->
    <div class='ptf-spacer' style=' --ptf-xxl: 10rem; --ptf-md: 5rem;'></div>
    </section>";
}
function update_menyu($db_link, $lang)
{
    /*$cat_menyus = $db_link->get('category');
    foreach ($cat_menyus as $line) {
    $id = $line["id"];
    $ad = stripslashes($line["name_".$lang]);
    $update_data = array(
    'seo' => url_slug($ad),
    );       
    $db_link->where ('id', $id)->update ('category', $update_data);
    }

    $cat_menyus = $db_link->get('vimeo');
    foreach ($cat_menyus as $line) {
    $id = $line["id"];
    $ad = stripslashes($line["name"]);
    $update_data = array(
    'seo' => url_slug($ad),
    );       
    $db_link->where ('id', $id)->update ('vimeo', $update_data);
    } */

    $cat_menyus = $db_link->get('channels');
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["name"]);
        $update_data = array(
            'seo' => url_slug($ad),
        );
        $db_link->where('id', $id)->update('channels', $update_data);
    }
}

//update_menyu($db_link,$lang);
function combo_countries($category_id, $lang, $db_link)
{
    $db_link->orderby("short_name_" . $lang, 'asc');
    $cat_menyus = $db_link->get('countries');
    print "
    <select required class='form-control' id='countries' name='countries'><option value=''> - - - - - - - - </option>";

    foreach ($cat_menyus as $line) {
        $id = $line["country_id"];
        $ad = stripslashes($line["short_name_" . $lang]);
        if ($category_id == $id)
            print "<option value='$ad' selected='selected'>$ad</option>";
        else
            print "<option value='$ad'>$ad</option>";
    }
    print "</select>";
}

function combo_menyu($cid, $category_id, $lang, $db_link)
{
    $db_link->where('sub_id', $cid);
    $db_link->where('status', 'active');
    $db_link->orderby('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    print "<div class='col-lg-12'><div class='form-group'>
    <select required class='form-control' id='category' name='category'><option value=''>Kateqoriya seçin</option>";

    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = stripslashes($line["name_" . $lang]);
        if ($category_id == $id)
            print "<option value='$id' selected='selected'>$ad</option>";
        else
            print "<option value='$id'>$ad</option>";
    }
    print "</select>
    </div>
    </div>";
}

function chek_menyu($cid, $category_id, $lang, $db_link)
{
    $db_link->where('sub_id', $cid);
    $db_link->where('status', 'active');
    $db_link->orderby('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    $cids = explode(",", $category_id);
    foreach ($cat_menyus as $line) {
        print "<div class='col-lg-2'><div class='checkbox'><label><label class='checkbox'>";
        $id = $line["id"];
        $ad = stripslashes($line["name_" . $lang]);
        if (in_array($id, $cids))
            print "<input class='form-control' name='category[]' type='checkbox' value='$id' checked>";
        else
            print "<input class='form-control' name='category[]' type='checkbox' value='$id'>";
        print "<span class='arrow'></label></span>$ad </label></div></div>";
    }


}

function top_menyu($lang, $db_link)
{
    global $home;
    //$db_link->where ('ust', 1);
    $db_link->where('status', 'active');
    $db_link->orderby('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    print '<li><a href="/az/index.html">' . $home . '</a></li>';
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = $line["name_" . $lang];
        $m_type = $line["type"];
        $ad_slug = latin_slug($ad);
        if ($m_type === 'news' && (int)$id === 1) {
            $slinks = "/$lang/portfolio";
        } else {
            $slinks = "/$lang/$m_type/$ad_slug";
        }
        print "<li><a href='$slinks'>$ad</a></li>";
    }
}

function ust_menyu($lang, $db_link){
    global $cid;
    global $home;
    global $dil;
    $db_link->where('sub_id', 0);
    $db_link->where('status', 'active');
    $db_link->orderby('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    if (empty($cid)) $hom = "active";
    //print "<li class='nav-item $hom'> <a href='/$lang/main/' class='nav-link' id='navbarDropdownMenuLink'>$home</a> </li>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = $line["name_" . $lang];
        $m_type = $line["type"];
        $ad_slug = latin_slug($ad);
        if ($m_type === 'news' && (int)$id === 1) {
            $slinks = "/$lang/portfolio";
        } else {
            $slinks = "/$lang/$m_type/$ad_slug";
        }
        $sub = $db_link->where('sub_id', $id)->where('status', 'active')->getValue("category", "count(id)");
        if ($id == $cid) $hom = "active"; else $hom = "";
        if ($sub) {
            print "<li>
            <a class='nav-link $hom' href='#' id='navbarDropdown$id' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>$ad</a>";
            sub_menyu($id, $lang, $db_link);
            print "</li>";
        } else {
            print "<li><a id='navbarDropdownMenuLink' class='nav-link $hom' href='$slinks'>$ad</a></li>";
        }
    }
}
function alt_menyu($lang, $db_link){
    global $cid;
    global $home;
    global $dil;
    $db_link->where('sub_id', 0);
    $db_link->where('status', 'active');
    $db_link->orderby('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    if (empty($cid)) $hom = "active";
    //print "<li class='nav-item $hom'> <a href='/$lang/main/' class='nav-link' id='navbarDropdownMenuLink'>$home</a> </li>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = $line["name_" . $lang];
        $m_type = $line["type"];
        $slinks = "/$lang/$m_type/$id.html";
        $sub = $db_link->where('sub_id', $id)->where('status', 'active')->getValue("category", "count(id)");
        if ($id == $cid) $hom = "active"; else $hom = "";
        if ($sub) {
            print "<a class='nav-link $hom' href='#' id='navbarDropdown$id' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>$ad</a>";
            sub_menyu($id, $lang, $db_link);
        } else {
            print "<a id='navbarDropdownMenuLink' class='nav-link $hom' href='$slinks'>$ad</a>";
        }
    }
}

function sub_menyu($cid, $lang, $db_link)
{
    $db_link->where('sub_id', $cid);
    $db_link->where('status', 'active');
    $db_link->orderby('blok', 'asc');
    $cat_menyus = $db_link->get('category');
    print "<div class='dropdown-menu' aria-labelledby='navbarDropdown$cid'>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = $line["name_" . $lang];
        $m_type = $line["type"];
        $ad_slug = latin_slug($ad);
        if ($m_type === 'news' && (int)$id === 1) {
            $slinks = "/$lang/portfolio";
        } else {
            $slinks = "/$lang/$m_type/$ad_slug";
        }
        print "<a href='$slinks' id='navbarDropdownMenuLink' class='nav-link footer-list'>$ad</a>";
    }
    print "</div>";
}

function home_content_menyu($cid, $lang, $db_link)
{
    global $b_read_more, $home, $subid, $project;
    $catname = $db_link->where('id', $cid)->getValue("category", "name_$lang");
    $sub = $db_link->subQuery();
    $sub->where("sub_id", $cid)->where('status', 'active');
    $sub->get("category", null, 'id');
    $db_link->where('category_id', $sub, 'in');
    $cat_menyus = $db_link->get('content', array(0, 3));
    $tbl_category_img = $db_link->where("id", $cid)->getValue("category", "img1");
    if ($tbl_category_img) $tbl_category_img = "/uploads/menyu/$tbl_category_img"; else $tbl_category_img = "/images/background/1.jpg";
    //print $db_link->getLastQuery();
    print "<div class='row category-grid'>";
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = $line["name_" . $lang];
        $p_title = $line["p_title"];
        $content = strip_tags(substr(strip_tags(stripslashes($line["content_" . $lang])), 0, 100));
        $slinks = "/$lang/content/$id.html";
        $img = stripslashes($line["img"]);
        if ($img) $img = "/uploads/content/$img"; else $img = "/no_image.png";
        print "<div class='col-md-4 col-sm-6 col-12'>
        <div class='card'>

        <a href='$slinks'>
        <img class='card-img-top' src='$img' alt='$ad'>
        </a>

        <div class='card-block'>
        <a href='$slinks' class='text-white fs-20 mb-20 block'>$ad</a>
        <p class='fs-15 mb-20'>$content...</p>
        <a href='$slinks' class='text-muted text-white fs-15'>$b_read_more</a>
        </div>

        </div>
        </div>";

    }
    print "</div>";
}

function home_news_slider($category_id, $lang, $db_link){
    $tbl_news = $db_link->where('category_id', $category_id)->orderBy("id", "desc")->get('news', array(0, 15));
    print "<div id='carouselExampleControls' class='carousel slide' data-ride='carousel'>
    <div class='carousel-inner'>";
    $i = 0;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $content = strip_tags(stripslashes($line["content_" . $lang]));
        $read_count = stripslashes($line["read_count"]);
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        if ($i == 1) $act = "active"; else $act = "";
        print "<div class='carousel-item $act'>
        <div class='absolute'>$basliq</div>
        <div class='absolute-second'>$content</div>
        <img class='d-block w-100' src='/uploads/news/$category_id/$img' alt='First slide'>
        <div class='carousel-caption d-none d-md-block ml-0 aboutcourse'>
        </div>
        </div>";


    }
    print "
    </div>
    <a class='carousel-control-prev' href='#carouselExampleControls' role='button' data-slide='prev'>
    <span class='carousel-control-prev-icon' aria-hidden='true'></span>
    <span class='sr-only'>Previous</span>
    </a>
    <a class='carousel-control-next' href='#carouselExampleControls' role='button' data-slide='next'>
    <span class='carousel-control-next-icon' aria-hidden='true'></span>
    <span class='sr-only'>Next</span>
    </a>
    </div>";
}

function next_news_blok($cid, $lang, $db_link){
    global $b_read_more,$cl_next;

    print "<section>
    <div class='ptf-post-navigation ptf-post-navigation--style-2'>
   
    <div class='container-xxl'>
     <div class='ptf-animated-block' data-aos='fade' data-aos-delay=0>
    <h2 class='h2 d-inline-flex' style='margin-bottom: 30px;'>$cl_next</h2>
    </div>
    <div class='row'>
    <div class='col-lg-12'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <div class='row'>
    ";

    $tbl_news = $db_link->where('service', $cid)->orderBy("RAND ()")->get('news', array(0, 2));
    $i = 0;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $content = explode("<!-- pagebreak -->", $line["content_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        $category_id = stripslashes($line["category_id"]);
        if ($img) $img = "/imageg_600_400_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");

        $imgAttrs = "decoding='async'";

        print "<div class='col-lg-6'>
        <article class='ptf-work ptf-work--style-1'>
        <div class='ptf-work__media'><a class='ptf-work__link'  href='$nlink'></a><img src='$img' alt='$basliq' width='600' height='400' $imgAttrs>
        </div>
        <div class='ptf-work__meta'>
        <h3 class='ptf-work__title'><a href='$nlink'>$basliq</a></h3>
        <div class='ptf-work__category'>{$content[0]}</div>
        
        </div>
        </article>
        </div>";
    }

    print "
    </div>
    </div>
    <div class='ptf-spacer' style=' --ptf-xxl: 6.25rem; --ptf-md: 3.125rem;border-bottom:1px solid #d7d7d7;'></div>
    </div>
    </div>
    </div>
    </div></section>";
}

function home_news_blok_all($lang, $db_link){
    global $b_read_more,$allprojects,$cid;
    if (!isset($cid)) $cid = 0;

    print "<div class='ptf-spacer' style=' --ptf-xxl: 5rem; --ptf-md: 4.0625rem;' id='cat_$cid'></div>
    <div class='container-xxl'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <h2 class='h2 d-inline-flex sonisler'>Son işlərimizə göz atın</h2>
    <!--<a class='ptf-link-with-arrow d-lg-inline-flex' href='/$lang/news/1.html' style='margin-left: 3.125rem;'>$allprojects <i class='lnil lnil-chevron-right'>--!>
    </i></a>
    </div>
    <div class='ptf-spacer' style='></div>
    </div>

    <div class='container-xxl'>
    <div class='row'>
    <div class='col-lg-12'>
    <div class='row cards'>
    ";

    
    $tbl_news = $db_link->orderBy("id", "desc")->where("service", 0,">")->get('news', array(0, 8));
    $i = 0;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $content = explode("<!-- pagebreak -->", $line["content_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        $category_id = stripslashes($line["category_id"]);
        $client = stripslashes($line["client"]);
        if ($img) $img = "/imageg_1200_800_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");

        $imgAttrs = "decoding='async'";

        //$tbl_content = $db_link->where('id', $cid)->getOne('news');

        print "<div class='col-sm-6 card'>
        <!--Portfolio Item-->
        <article class='ptf-work ptf-work--style-1 ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
        <div class='ptf-work__media'><a class='ptf-work__link'  href='$nlink'></a><img src='$img' alt='$basliq' width='1200' height='800' $imgAttrs>
        </div>
        <div class='ptf-work__meta'>
        <a href='$nlink'>
        <div class='ptf-work__category'>$client<span class='noqte'> • </span> $basliq</div>
        <h3 class='ptf-work__title'>{$content[0]}</h3>
        </a>
        </div>
        </article>
        </div>";

    }

    print "
    </div>
    </div>
    <div class='ptf-spacer' style=' --ptf-xxl: 6.25rem; --ptf-md: 3.125rem;'></div>
    </div>
    </div>";
}
function home_news_blok($cid, $lang, $db_link){
    global $b_read_more,$allprojects;

    $catname = $db_link->where('id', $cid)->getValue("services_service", "title_$lang");
    $catcontent = $db_link->where('id', $cid)->getValue("services_service", "content_$lang");
    //$catcontent = $db_link->where('id', $cid)->getValue("services_service", "content_$lang");

    $service_slug = latin_slug((string)$catname);

    print "<div class='ptf-spacer' style=' --ptf-xxl: 5rem; --ptf-md: 4.0625rem;' id='cat_$cid'></div>
    <div class='container-xxl'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <h2 class='h2 d-inline-flex'>$catname</h2>
    <a class='ptf-link-with-arrow d-lg-inline-flex' href='/$lang/portfolio/$service_slug' style='margin-left: 3.125rem;'>$allprojects <i class='lnil lnil-chevron-right'>
    </i></a>
    </div>
    <div class='ptf-spacer' style=' --ptf-xxl: 2rem; --ptf-md: 2.8125rem;'></div>
    </div>



    <div class='container-xxl'>
    <div class='row'>
    <div class='col-lg-12'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <div class='row'>
    ";

    
    $tbl_news = $db_link->where('service', $cid)->orderBy("id", "desc")->get('news', array(0, 2));
    $i = 0;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $content = explode("<!-- pagebreak -->", $line["content_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        $category_id = stripslashes($line["category_id"]);
        if ($img) $img = "/imageg_600_400_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");

        $tbl_content = $db_link->where('id', $cid)->getOne('news');

        $imgAttrs = "decoding='async'";

        print "<div class='col-lg-6'>
        <!--Portfolio Item-->
        <article class='ptf-work ptf-work--style-1'>
        <div class='ptf-work__media'><a class='ptf-work__link'  href='$nlink'></a><img src='$img' alt='$basliq' width='600' height='400' $imgAttrs>
        </div>
        <div class='ptf-work__meta'>
        <a href='$nlink'>
        <div class='ptf-work__category'>{$tbl_content["client"]}<span class='noqte'> • </span> $basliq</div>
        <h3 class='ptf-work__title'>{$content[0]}</h3>
        </a>
        </div>
        </article>
        </div>";
        //print "<div class='col-4'><a href='$nlink'><img class='portimage' src='$img' alt='$basliq'><div class='tagsinfo'>$basliq</div></a></div>";

    }

    print "
    </div>
    </div>
    <div class='ptf-spacer' style=' --ptf-xxl: 6.25rem; --ptf-md: 3.125rem; border-bottom:1px solid #d7d7d7;'></div>
    </div>
    </div>
    </div>";
}

function home_news($category_id, $lang, $db_link){
    global $b_read_more;
    $tbl_news = $db_link->where('category_id', $category_id)->orderBy("id", "desc")->get('news', array(0, 6));
    foreach ($tbl_news as $line) {
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $content = explode("<!-- pagebreak -->", $line["content_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        $category_id = stripslashes($line["category_id"]);
        if ($img) $img = "/imageg_1200_900_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
        /*        print "<div class='col-lg-4 col-md-6 wow fadeInLeft delay-04s'>
        <div >
        <img src='$img' alt='blog' class='img-fluid'>
        <div class='detail'>
        <div class='date-box'>
        <h5>$create_date</h5>
        </div>
        <h3>
        <a href='$nlink'>$basliq</a>
        </h3>
        <p>{$content[0]}</p>
        </div>
        </div>
        </div>"; */


        print "<div class='col-4'><a href='$nlink'><img class='portimage' src='$img' alt='$basliq'><div class='tagsinfo'>$basliq</div></a></div>";

    }
}

function home_projects($category_id, $lang, $db_link)
{
    global $b_read_more;
    //$tbl_news = $db_link->where('category_id', $category_id)->orderBy("id", "desc")->get('projects', array(0, 4));
    $tbl_news = $db_link->where('category_id', $category_id)->orderBy("id", "desc")->get('news', array(0, 4));
    foreach ($tbl_news as $line) {
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $content = explode("<!-- pagebreak -->", $line["content_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        $category_id = stripslashes($line["category_id"]);
        if ($img) $img = "/imageg_500_300_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
        print "<div class='col-lg-3 col-md-6 col-sm-6 wow fadeInLeft delay-04s'>
        <div class='card property-box-2'>
        <!-- property img -->
        <div class='property-thumbnail'>
        <a href='$nlink' class='property-img'>
        <img src='$img'
        alt='property-3' class='img-fluid'>
        </a>
        <div class='property-overlay'>
        <a href='$nlink' class='overlay-link'>
        <i class='fa fa-link'></i>
        </a>
        </div>
        </div>
        <!-- detail -->
        <div class='detail'>
        <h5 class='title'><a href='$nlink'>$basliq</a></h5>
        </div>
        </div>
        </div>";

    }
}

function home_ourteam($category_id, $lang, $db_link)
{
    global $b_read_more;
    $tbl_news = $db_link->where('category_id', $category_id)->orderBy("sira", "asc")->get('ourteam', array(0, 12));
    foreach ($tbl_news as $line) {
        $nomre = $line["id"];
        $facebook = $line["facebook"];
        $twitter = $line["twitter"];
        $instagram = $line["instagram"];
        $linkedin = $line["linkedin"];
        $basliq = stripslashes($line["title_" . $lang]);
        $content_s = stripslashes($line["contents_" . $lang]);
        $content = stripslashes($line["content_" . $lang]);
        $nlink = "/$lang/openourteam/$nomre.html";
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_500_300_" . $img . "_ourteam_" . $category_id . ".jpg"; else $img = "/no_image.png";
        print "<div class='col-xl-4 col-lg-4 col-md-6 col-sm-6 wow fadeInLeft delay-04s'>
        <div class='agent-2'>
        <div class='agent-photo'>
        <img src='$img' alt='avatar-5'
        class='img-fluid'>
        </div>
        <div class='agent-details'>
        <h5><!--<a href='$nlink'></a>-->$basliq</h5>
        <p>$content_s</p>
        <!--<ul class='social-list clearfix'>
        <li><a href='$facebook' class='facebook'><i class='fa fa-facebook'></i></a></li>
        <li><a href='$twitter' class='twitter'><i class='fa fa-twitter'></i></a></li>
        <li><a href='$instagram' class='instagram'><i class='fa fa-instagram'></i></a></li>
        <li><a href='$linkedin' class='linkedin'><i class='fa fa-linkedin'></i></a></li>
        </ul>-->
        </div>
        </div>
        </div>";

    }
}

function getFileType($file)
{
    if (function_exists('finfo_open')) {
        if ($info = finfo_open(defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME)) {
            $mimeType = finfo_file($info, $file);
        }
    } elseif (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($file);
    }
    if (strstr($mimeType, 'image/')) {
        return 'image';
    } else if (strstr($mimeType, 'video/')) {
        return 'video';
    } else if (strstr($mimeType, 'audio/')) {
        return 'audio';
    } else if (strstr($mimeType, 'application/msword')) {
        return 'word';
    } else if (strstr($mimeType, 'application/vnd.ms-excel')) {
        return 'excel';
    } else if (strstr($mimeType, 'application/vnd.ms-powerpoint')) {
        return 'powerpoint';
    } else if (strstr($mimeType, 'application/pdf')) {
        return 'pdf';
    } else {
        return null;
    }
}

if (!function_exists('mime_content_type')) {
    function mime_content_type($filename)
    {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }
}

function content_menyu($cid, $lang, $db_link){
    global $b_read_more, $home, $subid, $project;

    $catname = $db_link->where('id', $cid)->getValue("category", "name_$lang");
    $tbl_category = $db_link->where("id", $cid)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $cid)->getValue("category", "img1");

    $catname = $db_link->where('id', $cid)->getValue("category", "name_$lang");
    /*$sub = $db_link->subQuery();
    $sub->where("sub_id", $cid)->where('status', 'active');
    $sub->get("category", null, 'id');
    $db_link->where('category_id', $sub, 'in');  
    $cat_menyus = $db_link->get('content');  */

    $tbl_category_img = $db_link->where("id", $cid)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }

    print "<div class='sub-banner overview-bgi' $bg_style>
    <div class='container'>
    <div class='breadcrumb-area'>
    <h2>$tbl_category</h2>
    <ul class='breadcrumbs'>
    <li><a href='/$lang/main/'>$home</a></li>
    <li class='active'>$tbl_category</li>
    </ul>
    </div>
    </div>
    </div>";
    print "<div class='about-us content-area-8 bg-white pb-5'><div class='container'><div class='row'><div class='col-lg-12 align-self-center'>";

    $cat_menyus = $db_link->where("sub_id", $cid)->get('category');
    foreach ($cat_menyus as $line) {
        $id = $line["id"];
        $ad = $line["name_" . $lang];
        //$p_title = $line["p_title"];
        $slinks = "/$lang/content/$id.html";
        $img = stripslashes($line["img1"]);
        if ($img) $img = "/uploads/menyu/$img"; else $img = "/no_image.png";
        print "<div class='row mb-4 col-lg-6 float-left'>
        <div class='col-lg-3 col-xs-3'><a href='$slinks'><img style='width:100%;' src='$img' alt='$ad'></a></div>
        <div class='col-lg-9 col-xs-9'><a href='$slinks'><h5>$ad</h5></a></div>
        </div>";

    }
    print "</div></div></div></div>";
}

function files($category_id, $seh, $lang, $db_link)
{
    global $siteName, $home;
    $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
    print "
    <section class='title'><div class='container'><h2 class='wow fadeIn' data-wow-delay='0.1s'>$catname</h2></div></section>
    <section id='pdf'><div class='container'><div class='row'>
    ";
    $db_link->pageLimit = 8;
    $tbl_news = $db_link->arraybuilder()->where('m_id', $category_id)->orderBy("id", "desc")->paginate("files", $seh);
    $totalpage = $db_link->totalPages;

    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["name_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $file = stripslashes($line["file_az"]);
        $content = strip_tags(substr(strip_tags(stripslashes($line["content_" . $lang])), 0, 300));
        $nlink = "http://$siteName/uploads/files/$category_id/$file";
        $FileType = getFileType("uploads/files/$category_id/$file");
        $gview = "https://docs.google.com/gview?url=";
        if ($nlink) $nlink = $gview . $nlink . "&embedded=true";

        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_300_200_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";

        print "<div class='col-md-4 wow fadeInUp' data-wow-delay='0." . $i . "s'><a target='_blank' rel='noopener noreferrer' href='$nlink'><div class='hovicon effect-8 icon-pdf'><span>$basliq</span></div></a></div>";
    }
    print "</div>";
    print "</div>";
    print "</section>";

}

function projects($category_id, $lang, $db_link)
{
    global $b_read_more, $home, $subid, $project;
    $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
    $tbl_category = $db_link->where("id", $category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }

    print "<div class='sub-banner overview-bgi' $bg_style>
    <div class='container'>
    <div class='breadcrumb-area'>
    <h2>$tbl_category</h2>
    <ul class='breadcrumbs'>
    <li><a href='/$lang/main/'>$home</a></li>
    <li class='active'>$tbl_category</li>
    </ul>
    </div>
    </div>
    </div>";

    print "<div class='map-content content-area container-fluid'>
    <div class='row'>
    <div class='col-lg-12'>
    <div class='row'>
    <div id='map'></div>
    </div>
    </div>
    </div>
    </div>";
    print "<div class='intro-section'>
    <div class='container'>
    <div class='row'>
    <div class='col-lg-9 col-md-7 col-sm-12'>
    <div class='intro-text'>
    <h3>Do you have any question?</h3>
    </div>
    </div>
    <div class='col-lg-3 col-md-3 col-sm-12'>
    <a href='#' class='btn btn-md sn'>Contact now</a>
    </div>
    </div>
    </div>
    </div>";
    print "<script> var properties = {\"data\":";
    //$db_link->pageLimit = 6;
    //$tbl_news = $db_link->arraybuilder()->where('category_id', $category_id)->paginate("projects", $page);
    $tbl_news = $db_link->JsonBuilder()->where('category_id', $category_id)->get("projects", array(0, 12),
        array(
            "id as id",
            "title_" . $lang . " as title",
            "'Sale' as listing_for",
            "'Caspian' as author",
            "'5 days ago' as date",
            "'true' as is_featured",
            "latitude as latitude",
            "longitude as longitude",
            "img as image",
            "'/assets/img/building.png' as type_icon",
            "content_" . $lang . " as description"
        )
    );
    print $tbl_news;
    print "};</script>"; 
}

function openprojects($cid, $lang, $db_link)
{
    global $home, $security_test, $subid;
    $tbl_category_id = $db_link->where("id", $cid)->getValue("projects", "category_id");
    $tbl_category = $db_link->where("id", $tbl_category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $tbl_category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }
    $tbl_content = $db_link->where('id', $cid)->get('projects');
    foreach ($tbl_content as $line) {
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $read_count = stripslashes($line["read_count"]);
        $category_id = $line["category_id"];
        $news_title = strip_tags(stripslashes($line["title_" . $lang]));
        $content = stripslashes($line["content_" . $lang]);
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_1500_1500_" . $img . "_projects_" . $category_id . ".jpg";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");

        print "<div class='sub-banner overview-bgi' $bg_style>
        <div class='container'>
        <div class='breadcrumb-area'>
        <h2>$news_title</h2>
        <ul class='breadcrumbs'>
        <li><a href='/$lang/main/'>$home</a></li>
        <li class='active'>$tbl_category</li>
        </ul>
        </div>
        </div>
        </div>";
        print "<div class='blog-section content-area-7'><div class='container'><div class='row'><div class='col-lg-12'><div >";
        if ($img) print "<img class='blog-theme img-fluid' src='$img' alt='$news_title' />";
        print "<div class='detail'>$content</div>";
        print "</div></div></div></div></div>";
    }

    $db_link->where('id', $cid)->update('news', array('read_count' => $db_link->inc(1)));
}

function services($category_id, $page, $lang, $db_link)
{
    global $b_read_more, $home, $subid,$quote, $chooseservice;
    $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
    $tbl_category = $db_link->where("id", $category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }

    print "<div class='sub-banner overview-bgi' $bg_style>
    <div class='container'>
    <div class='breadcrumb-area'>
    <h2>$tbl_category</h2>
    <ul class='breadcrumbs'>
    <li><a href='/$lang/main/'>$home</a></li>
    <li class='active'>$tbl_category</li>
    </ul>
    </div>
    </div>
    </div>";

    print "<div class='blog-section content-area-2' style='padding-top: 30px;'><div class='container'><div class='row'>


    <div class='search-area' id='search-area-1' style='background: #fff; padding: 0 0 5px; margin-bottom: 20px;'>
    <div class='sidebar'>";



    print "<div class='col-md-12'>
    <div class='search-area-inner'>
    <div class='search-contents '>
    <form action='/$lang/quote/' method='POST'>
    <div class='row'>
    <div class='col-6 col-lg-3 col-md-3'>
    <h4 style='padding-top: 15px;'>$chooseservice</h4>
    </div>
    </div>
    <div class='row'>
    <div class='col-6 col-lg-3 col-md-3'>
    <div class='form-group'>";
    combo_service(null,$db_link);
    print "</div>
    </div>
    <div class='col-6 col-lg-3 col-md-3'>
    <div class='form-group'>";
    combo_industry(null,$db_link);
    print "</div>
    </div>
    <div class='col-6 col-lg-3 col-md-3'>
    <div class='form-group'>";
    combo_solution(null,$db_link);
    print "</div>
    </div>
    <div class='col-6 col-lg-3 col-md-3'>
    <div class='form-group'>
    <input class='search-button btn-md btn-color' name='quote' type='submit' value='$quote'>
    </div>
    </div>
    </div>

    </form>
    </div>
    </div>
    </div>   


    </div>
    </div>    
    <div class='col-lg-12 col-md-12'>
    ";

    $db_link->pageLimit = 6;
    $tbl_news = $db_link->arraybuilder()->where('category_id', $category_id)->orderBy("id", "asc")->paginate("services", $page);
    $totalpage = $db_link->totalPages;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $content = strip_tags(substr(strip_tags(stripslashes($line["content_" . $lang])), 0, 150)) . "...";
        $nlink = "/$lang/openservices/$nomre.html";
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_1200_900_" . $img . "_services_" . $category_id . ".jpg"; else $img = "/no_image.png";
        print "<div class='col-lg-3 col-md-6 col-sm-6 float-left'>
        <div >
        <img class='card-img-top' src='$img' alt='$basliq'>
        <div class='detail' style='padding: 10px;'>
        <a href='$nlink' class='text-black fs-20 mb-20 block'>$basliq</a>
        </div>
        </div>
        </div>";
    }

    print "</div></div></div></div>";
}

function openservices($cid, $lang, $db_link)
{
    global $home, $security_test, $subid;
    $tbl_category_id = $db_link->where("id", $cid)->getValue("services", "category_id");
    $tbl_category = $db_link->where("id", $tbl_category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $tbl_category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }
    $tbl_content = $db_link->where('id', $cid)->get('services');
    foreach ($tbl_content as $line) {
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $read_count = stripslashes($line["read_count"]);
        $category_id = $line["category_id"];
        $news_title = strip_tags(stripslashes($line["title_" . $lang]));
        $content = stripslashes($line["content_" . $lang]);
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_1500_1500_" . $img . "_services_" . $category_id . ".jpg";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");

        print "<div class='sub-banner overview-bgi' $bg_style>
        <div class='container'>
        <div class='breadcrumb-area'>
        <h2>$news_title</h2>
        <ul class='breadcrumbs'>
        <li><a href='/$lang/main/'>$home</a></li>
        <li class='active'>$tbl_category</li>
        </ul>
        </div>
        </div>
        </div>";

        print "<div class='blog-section content-area-7'><div class='container'><div class='row'><div class='col-lg-12'><div >";
        if ($img) print "<img class='blog-theme img-fluid' src='$img' alt='$news_title' />";
        print "<div class='detail'>$content</div>";
        print "</div></div></div></div></div>";
    }

    $db_link->where('id', $cid)->update('news', array('read_count' => $db_link->inc(1)));
}

function xeberlercol($category_id, $page, $lang, $db_link)
{
    global $b_read_more, $home, $subid, $project;
    $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
    $tbl_category = $db_link->where("id", $category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }

    print "<div class='sub-banner overview-bgi' $bg_style>
    <div class='container'>
    <div class='breadcrumb-area'>
    <h2>$tbl_category</h2>
    <ul class='breadcrumbs'>
    <li><a href='/$lang/main/'>$home</a></li>
    <li class='active'>$tbl_category</li>
    </ul>
    </div>
    </div>
    </div>";

    print "<div class='blog-section content-area-2'><div class='container'>
    <div class='row'>
    <div class='col-lg-12 align-self-center'>
    <div class='row mb-4'>
    <div class='col-lg-2 col-xs-2'><img src='/assets/img/global-goals_01_1.jpg'  style='width:100%;'></div><div class='col-lg-10 col-xs-10'><h2>$tbl_category</h2></div>
    </div>
    </div>
    </div>

    <div class='row'>
    <div class='col-lg-12 align-self-center'>
    <div class='about-text more-info'>
    <div id='faq' class='faq-accordion'>
    <div class='card m-b-0'>
    ";

    $db_link->pageLimit = 6;
    $tbl_news = $db_link->arraybuilder()->where('category_id', $category_id)->orderBy("id", "desc")->paginate("news", $page);
    $totalpage = $db_link->totalPages;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $content = strip_tags(stripslashes($line["content_" . $lang]));
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_1200_900_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";

        print "<div class='card-header'>
        <a class='card-title collapsed' data-toggle='collapse' data-parent='#faq' href='#collapse$nomre'>
        $basliq
        </a>
        </div>

        <div id='collapse$nomre' class='card-block collapse'>
        <p>$content</p>
        </div>";       


        /*print "<div class='col-lg-4 col-md-6 col-sm-6'>
        <div >
        <img class='card-img-top' src='$img' alt='$basliq'>
        <div class='detail'>
        <a href='$nlink' class='text-black fs-20 mb-20 block'>$basliq</a>
        <p class='fs-15 mb-20'>$content</p>
        <a href='$nlink' class='text-muted fs-15'>$b_read_more</a>
        </div>
        </div>
        </div>"; */
    }

    print "</div></div></div></div>";
    print "</div></div></div>";
}

function xeberler($category_id, $page, $lang, $db_link)
{
    global $b_read_more, $home, $subid, $project,$allsectors;
    $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
    $tbl_category = $db_link->where("id", $category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }


    print '<article class="ptf-page ptf-page--portfolio-masonry">';

    print "<section>
    <div class='ptf-spacer' style=' --ptf-xxl: 10rem; --ptf-md: 2rem;'></div>
    <div class='container-xxl'>
    <div class='row'>
    <div class='col-xl-12'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>";
home_content_s(16,$lang,$db_link);
print"
    <div class='ptf-spacer' style=' --ptf-xxl: 4.375rem;'></div>";
    print "<ul class='ptf-filters ptf-filters--style-1' id='ptf-filter-masonry'>
    <li class='filter-item' data-filter='*'><a href='/$lang/portfolio' class='kats'>$allsectors</a></li>";
    menyu_service(2,$db_link);
    print "
    </ul>"; 
    print "<div class='ptf-spacer' style=' --ptf-xxl: 6.25rem; --ptf-md: 3.125rem;'></div>
    </div>
    </div>
    </div>
    </div>
    </section>";



    print "<section>
    <div class='container-xxl'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <div class='row'>";

    $db_link->pageLimit = 1000000;
    $db_link->where('category_id', $category_id);

    $service_filter_id = (int)$subid;
    if ($service_filter_id <= 0 && isset($_GET['subid'])) {
        $service_filter_id = (int)preg_replace('/\D+/', '', (string)$_GET['subid']);
    }
    if ($service_filter_id <= 0) {
        $request_path = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
        if (preg_match('~^/'.preg_quote($lang, '~').'/news/'.(int)$category_id.'-(\d+)\.html$~', $request_path, $m)) {
            $service_filter_id = (int)$m[1];
        } elseif (preg_match('~^/'.preg_quote($lang, '~').'/news/'.(int)$category_id.'-(\d+)/(\d+)\.html$~', $request_path, $m)) {
            $service_filter_id = (int)$m[1];
        }
    }

    if($service_filter_id > 0) $db_link->where('service', $service_filter_id);
    $tbl_news = $db_link->orderBy("news_date", "desc")->paginate("news", $page);
    $totalpage = $db_link->totalPages;
    $i = 0;
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $basliq = stripslashes($line["title_" . $lang]);
        $read_count = stripslashes($line["read_count"]);
        $content = explode("<!-- pagebreak -->", $line["content_".$lang]);
        $service_id = (int)($line['service'] ?? 0);
        $service_title = '';
        if ($service_id > 0) {
            $service_title = (string)$db_link->where('id', $service_id)->getValue('services_service', 'title_'.$lang);
        }
        $service_slug = latin_slug($service_title);
        $project_slug = latin_slug(strip_tags((string)$basliq));
        if ($service_slug !== '' && $project_slug !== '') {
            $nlink = "/$lang/portfolio/$service_slug/$project_slug";
        } else {
            $nlink = "/$lang/opennews/$nomre.html";
        }
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_1200_900_" . $img . "_news_" . $category_id . ".jpg"; else $img = "/no_image.png";

        $imgAttrs = "decoding='async'";

        print "<div class='col-lg-6 ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
        <article class='ptf-work ptf-work--style-1'>
        <div class='ptf-work__media'><a class='ptf-work__link' href='$nlink'></a><img src='$img' alt='$basliq' width='1200' height='900' $imgAttrs>
        </div>
        <div class='ptf-work__meta'>
        <h3 class='ptf-work__title'><a href='$nlink'>$basliq</a></h3>
        <div class='ptf-work__category'>{$content[0]}</div>
        </div>
        </article>
        </div>";

    }

    print "</div>
    </div>
    </div>

    </section>

    </article>
    </main>
    </div";


}

function xeber_ac($cid, $lang, $db_link)
{
    global $home, $security_test, $subid,$musteri,$servis;
    $tbl_category_id = $db_link->where("id", $cid)->getValue("news", "category_id");
    $tbl_category = $db_link->where("id", $tbl_category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $tbl_category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }

    $tbl_content = $db_link->where('id', $cid)->getOne('news');

    $catname = $db_link->where('id', $tbl_content["service"])->getValue("services_service", "title_$lang");

    print '<article class="ptf-page ptf-page--single-work-1">';
    print "<section>
    <div class='ptf-spacer' style=' --ptf-xxl: 10rem; --ptf-md: 2rem;'></div>
    <div class='container-xxl'>
    <div class='row'>
    <div class='col-xl-9'>

<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <h3 class='large-heading'>{$tbl_content["title_" . $lang]}</h3>

    <div class='ptf-spacer' style=' --ptf-xxl: 5rem; --ptf-md: 2.5rem;'></div>
    </div>

    <div class='ptf-spacer' style=' --ptf-lg: 4.375rem; --ptf-md: 2.1875rem;'></div>
    </div>
    <div class='col-xl-3'>

    <div class='ptf-animated-block' data-aos='fade' data-aos-delay='300'>
    <h5 class='fz-14 text-uppercase has-3-color fw-normal'>$musteri</h5>
    <!--Spacer-->
    <div class='ptf-spacer' style=' --ptf-xxl: 1.25rem;'></div>
    <a href=''><p class='fz-20 lh-1p5 has-black-color'>{$tbl_content["client"]}</p></a>
    </div>

    <div class='ptf-spacer' style=' --ptf-xxl: 4.375rem; --ptf-md: 2.1875rem;'></div>

    <div class='ptf-animated-block' data-aos='fade' data-aos-delay='400'>
    <h5 class='fz-14 text-uppercase has-3-color fw-normal'>$servis</h5>
    <!--Spacer-->
    <div class='ptf-spacer' style=' --ptf-xxl: 1.25rem;'></div>
    <a href=''><p class='fz-20 lh-1p5 has-black-color'>$catname</p></a>
    </div>
    </div>
    </div>
    </div>
    </section>



    <section>
    <div class='ptf-spacer' style=' --ptf-xxl: 10rem; --ptf-md: 3.125rem;'></div>
    <div class='container-xxl'>
    <div class='ptf-animated-block' data-aos='fade' data-aos-delay=0>
    {$tbl_content["content_" . $lang]}
    ";


    print '

    </div>
    </div>
    </section>';

    next_news_blok($tbl_content["service"], $lang, $db_link);

    print "</article>";    

    $db_link->where('id', $cid)->update('news', array('read_count' => $db_link->inc(1)));
}

function openourteam($cid, $lang, $db_link)
{
    global $home, $security_test, $subid;
    $tbl_category_id = $db_link->where("id", $cid)->getValue("ourteam", "category_id");
    $tbl_category = $db_link->where("id", $tbl_category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $tbl_category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }
    print "<div class='sub-banner overview-bgi' $bg_style>
    <div class='container'>
    <div class='breadcrumb-area'>
    <h2>$tbl_category</h2>
    <ul class='breadcrumbs'>
    <li><a href='/$lang/main/'>$home</a></li>
    <li class='active'>$tbl_category</li>
    </ul>
    </div>
    </div>
    </div>";

    print "<div class='blog-section content-area-7'><div class='container'><div class='row'><div class='col-lg-12'><div >";
    $tbl_content = $db_link->where('id', $cid)->get('ourteam');
    foreach ($tbl_content as $line) {
        $nomre = $line["id"];
        $create_date = getTheDay($line["news_date"]);
        $read_count = stripslashes($line["read_count"]);
        $category_id = $line["category_id"];
        $news_title = strip_tags(stripslashes($line["title_" . $lang]));
        $content = stripslashes($line["content_" . $lang]);
        $img = stripslashes($line["img"]);
        if ($img) $img = "/imageg_1500_1500_" . $img . "_ourteam_" . $category_id . ".jpg";
        $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");

        if ($img) print "<img class='blog-theme img-fluid' src='$img' alt='$news_title' />";
        print "<div class='detail'>$content</div>";
    }
    print "</div></div></div></div></div>";
    $db_link->where('id', $cid)->update('news', array('read_count' => $db_link->inc(1)));
}

function home_content($cid, $lang, $db_link){
    global $home, $subid, $b_read_more;
    $tbl_content = $db_link->where('id', $cid)->get('content');
    foreach ($tbl_content as $content) {
        $id = $content["id"];
        $name = $content["name_" . $lang];
        $img = $content["img"];
        $contents = explode("<!-- pagebreak -->", $content["content_" . $lang]);
        $contents = strip_tags($contents[0]);


        //if ($img) $img = "/imageg_500_300_" . $img . "_content_" . $cid . ".jpg"; else $img = "/no_image.png";
        print "<div class='container'>
        <div class='main-title'>
        <h3>$name</h3>
        </div>
        <div class='col-lg-12'>
        <div class='row property-box-6'>
        <div class='col-lg-6 col-pad'>
        <img src='/uploads/content/{$img}' class='img-fluid' alt='$name'>
        </div>
        <div class='col-lg-6 col-pad align-self-center'>
        <div class='info'>
        <p>{$contents}</p>
        <a href='/$lang/content/1.html' class='btn btn-sm btn-color'>$b_read_more</a>
        </div>
        </div>
        </div>
        </div>
        </div>";

    }
}

function home_content_s($cid, $lang, $db_link)
{
    global $home, $subid;
    $tbl_content = $db_link->where('id', $cid)->get('content');
    foreach ($tbl_content as $content) {
        $id = $content["id"];
        $contents = $content["content_" . $lang];
        print $contents;

    }
}

function contact_content_s($cid, $lang, $db_link)
{
    /*    global $home, $subid;
    $tbl_content = $db_link->where('id', $cid)->get('content');
    foreach ($tbl_content as $content) {
    $id = $content["id"];
    $contents = $content["content_" . $lang];
    print $contents;

    } */

    print "<section id='section-contact' class='contact-us'>
    <div class='container'>
    <div class='row justify-content-center js-inview'>
    <div class='col-12'>
    <div class='voffset-120'></div>
    <div class='section__header'>
    <h2 class='section__title js-inview_h tra20 delay01'>İdeyanız var?</h2>

    </div>
    </div>
    </div>
    </div>
    <div class='container contact-info'>
    <div class='row js-inview'>
    <div class='col-4'>
    <div class='contact-address'>
    <div class='voffset-50'></div>
    <h3 class='js-inview_h tra20 delay06'>Baş ofis</h3>
    <p class='js-inview_h tra20 delay07'>121 Street, Melbourne Victoria - London
    <br>Email: <a href='#'>hello@mansonagency.com</a>
    <br>Phone: +44 3 7890 - 123</p>
    <div class='voffset-30'></div>
    <h4 class='js-inview_h tra20 delay06'>Follow us:</h4>
    <ul>
    <li class='nav-social'>
    <ul>
    <li><a href='https://www.facebook.com/customartech/' class='nav-link'><i class='fab fa-facebook-f'></i></a></li>
    <li><a href='https://www.behance.net/customar' class='nav-link'><i class='fab fa-behance'></i></a></li>
    <li><a href='https://www.youtube.com/channel/UCqu2yBo6cBy9oaSWDuAR6Zg#' class='nav-link'><i class='fab fa-youtube'></i></a></li>
    <li><a href='https://www.instagram.com/customartech/' class='nav-link'><i class='fab fa-instagram'></i></a></li>
    </ul>
    </li>
    </ul>
    </div>
    </div>
    <div class='col-8'>
    <div class='voffset-60'></div>
    <form action='mail.php' method='post' id='contactform' class='contact-form'>
    <div class='form-group'>
    <input name='firstname' type='text' class='form-control  js-inview_h tra20 delay01' id='input-name' placeholder='First name'>
    </div>
    <div class='form-group'>
    <input name='lastname' type='text' class='form-control  js-inview_h tra20 delay02' id='input-lastname' placeholder='Last name'>
    </div>
    <div class='form-group'>
    <input name='email' type='text' class='form-control  js-inview_h tra20 delay03' id='input-email' placeholder='Your email address'>
    </div>
    <div class='form-group'>
    <input name='phone' type='text' class='form-control  js-inview_h tra20 delay03' id='input-phone' placeholder='Where are you from?'>
    </div>
    <div class='form-group js-inview_h tra20 delay04'>
    <textarea name='message' class='form-control' id='input-message' rows='1' placeholder='Enter your message'></textarea>
    </div>
    <div class='voffset-20'></div>
    <input type='submit' value='SEND MESSAGE' class='btn btn-primary js-inview_h tra20 delay05'>
    </form>
    <div class='voffset-180'></div>

    </div>
    </div>
    </div>

    </section>";
}

function content($cid, $lang, $db_link)
{
    global $home, $subid;
    $tbl_category = $db_link->where("id", $cid)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $cid)->getValue("category", "img1");
    $tbl_content = $db_link->where('id', $cid)->getOne('content');
    if (empty($tbl_content)) {
        $tbl_content = $db_link->where('category_id', $cid)->orderBy('id', 'asc')->getOne('content');
    }
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }
    print '<article class="ptf-page ptf-page--home-agency">';

    print "<section>
    <div class='ptf-spacer' style=' --ptf-xxl: 8.125rem; --ptf-md: 4.0625rem;'></div>
    <div class='container-xxl'>
    <div class='row'>
    <div class='col-xl-7'>
<div class='ptf-animated-block' data-aos='fade-up' data-aos-delay='500' data-aos-duration='1500'>
    <h3 class='h1 large-heading has-accent-1'>".(string)($tbl_content["name_" . $lang] ?? '')."</h3>
    </div>
    </div>
    <div class='col-xl-5 d-none d-xl-block'>
    <div class='ptf-animated-block' data-aos='fade' data-aos-delay='100'>
    <div class='has-black-color fz-90 lh-1 text-end'><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' style='height:1em' viewBox='0 0 17 17'>
    <path d='M16 .997V10h-1V2.703L4.683 13l-.707-.708L14.291 1.997H6.975v-1H16z' />
    </svg></div>
    </div>
    </div>
    </div>
    </div>
    </section>";



    print "<section class='ptf-custom--3993 jarallax'>
    <div class='container-xxl'>

    ".(string)($tbl_content["content_" . $lang] ?? '')."

    </div>
    </section>";

    print '</article>';

    testimonial($db_link, $lang);
}

function content_s($cid, $lang, $db_link)
{
    global $home, $subid;
    $tbl_category = $db_link->where("id", $cid)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $cid)->getValue("category", "img1");
    $tbl_content = $db_link->where('id', $cid)->getOne('content');
    print "$tbl_content";

}

function home_slider_photos($category_id, $lang, $db_link)
{
    global $siteName, $home, $b_read_more;
    $tbl_news = $db_link->arraybuilder()->where('m_id', $category_id)->orderBy("id", "asc")->get("photos");
    //print $db_link->getLastQuery();
    foreach ($tbl_news as $line) {
        $i++;
        if ($i == 1) $cl = 'active'; else $cl = '';
        $id = $line["id"];
        $file = stripslashes($line["file_az"]);
        $link = stripslashes($line["link_" . $lang]);
        $name = stripslashes($line["name_" . $lang]);
        $description = stripslashes($line["description"]);
        if ($file) $img = "/uploads/photos/$category_id/$file"; else $img = "/no_image.png";

        print "<div class='carousel-item $cl'>
        <img class='d-block w-100' src='$img' alt='$name'>
        <div class='carousel-caption banner-slider-inner d-flex h-100 text-center'>
        <div class='carousel-content container'>
        <div class='t-center'>
        <h2 data-animation='animated fadeInDown delay-05s'>$name</h2>
        <p data-animation='animated fadeInUp delay-10s'>$description</p>
        <a data-animation='animated fadeInUp delay-10s' href='#' class='btn btn-lg btn-round btn-theme'>$b_read_more</a>
        </div>
        </div>
        </div>
        </div>";
    }
}

function home_photos($category_id, $lang, $db_link)
{
    global $siteName, $home;
    $i = 0;
    $tbl_news = $db_link->arraybuilder()->where('m_id', $category_id)->orderBy("id", "desc")->get("photos");
    $totalpage = $db_link->totalPages;
    foreach ($tbl_news as $line) {
        $i++;
        $id = $line["id"];
        $file = preg_replace('/\s+/', '', trim(stripslashes($line["file_az"])));
        $link = stripslashes($line["link_az"]);
        $name = stripslashes($line["name_az"]);
        //if ($file) $img = "/uploads/photos/$category_id/$file"; else $img = "/no_image.png";  
        if ($file) $img = "/imageg_100_100_" . $file . "_photos_" . $category_id . ".jpg"; else $img = "/no_image.png";
        print "<div class='slide'><a href='$link' target='_blank' rel='noopener noreferrer'><img src='$img' alt='$name'></a></div>";
    }
}

function team_photos($category_id, $lang, $db_link)
{
    global $siteName, $home;
    $i = 0;
    //print "<div class='col-md-8 offset-md-2'><div class='row'>";
    $db_link->pageLimit = 15;
    $tbl_news = $db_link->arraybuilder()->where('m_id', $category_id)->orderBy("id", "asc")->paginate("photos", 1);
    $totalpage = $db_link->totalPages;
    //print $db_link->getLastQuery();
    foreach ($tbl_news as $line) {
        $i++;
        $id = $line["id"];
        $file = preg_replace('/\s+/', '', trim(stripslashes($line["file_az"])));
        $link = stripslashes($line["link_az"]);
        $name = stripslashes($line["name_az"]);
        $description = stripslashes($line["description"]);
        if ($file) $img = "/uploads/photos/$category_id/$file"; else $img = "/no_image.png";
        print "<div class='staff-item'>
        <div class='staff-item-wrapper'>
        <div class='staff-info'>
        <a href='JavaScript:;' class='staff-avatar'><img
        src='$img' alt='$name'
        class='img-responsive'/></a><a href='#' class='staff-name'>$name</a>
        <div class='staff-job'>$description</div>
        <div class='staff-desctiption'>$link</div>
        </div>
        </div>
        </div>";


    }
}

function photos($category_id, $page, $lang, $db_link)
{
    global $siteName, $home;
    $catname = $db_link->where('id', $category_id)->getValue("category", "name_$lang");
    $tbl_category = $db_link->where("id", $category_id)->getValue("category", "name_" . $lang);
    $tbl_category_img = $db_link->where("id", $category_id)->getValue("category", "img1");
    if ($tbl_category_img) {
        $tbl_category_img = "/uploads/menyu/$tbl_category_img";
        $bg_style=" style='background: url($tbl_category_img) top left no-repeat; background-repeat: no-repeat; background-size: 100%;'";
    }else{
        $bg_style="";
    }

    print "<div class='sub-banner overview-bgi' $bg_style>
    <div class='container'>
    <div class='breadcrumb-area'>
    <h2>$tbl_category</h2>
    <ul class='breadcrumbs'>
    <li><a href='/$lang/main/'>$home</a></li>
    <li class='active'>$tbl_category</li>
    </ul>
    </div>
    </div>
    </div>";

    print "<div class='blog-section content-area-2'><div class='container'><div class='row lightbox' data-plugin-options='{\"delegate\": \"a\", \"gallery\": {\"enabled\": true}}'>";

    $db_link->pageLimit = 50;
    $tbl_news = $db_link->arraybuilder()->where('m_id', $category_id)->orderBy("id", "desc")->paginate("photos", $page);
    $totalpage = $db_link->totalPages;
    //print $db_link->getLastQuery();
    foreach ($tbl_news as $line) {
        $i++;
        $id = $line["id"];
        $file = stripslashes($line["file_az"]);
        $link = stripslashes($line["link_" . $lang]);
        $name = stripslashes($line["name_" . $lang]);
        if ($file) {
            $img = "/imageg_1000_1000_" . $file . "_photos_" . $category_id . ".jpg";
            $img_s = "/imageg_350_280_" . $file . "_photos_" . $category_id . ".jpg";
        } else $img = "/no_image.png";

        print "<div class='col-lg-4 col-md-6 col-sm-6'>
        <div >
        <a href='$img'><img class='card-img-top' src='$img_s' alt='$name'></a>
        <div class='detail'>
        $name 
        </div>
        </div>
        </div>";
    }

    print "</div></div></div>";

}

function multimedia($cid, $seh, $lang, $db_link)
{
    print "<div class='container gallery-container'><div class='tz-gallery'><div class='row'>";
    $i = 0;
    $tbl_news = $db_link->where('category_id', $cid)->orderBy("id", "desc")->get('multimedia');
    foreach ($tbl_news as $line) {
        $i++;
        $nomre = $line["id"];
        $name = stripslashes($line["name_" . $lang]);
        $type = $line["file_type"];

        if ($type == 'youtube') {
            $file = $db_link->where('m_id', $nomre)->getValue("multimedia_file", "file");
            $img = "http://img.youtube.com/vi/$file/0.jpg";
            $rel = " class='video'";
            $link = "http://www.youtube.com/watch?v=$file";
        } else {
            $img = $db_link->where('m_id', $nomre)->where('cover', 1)->getValue("multimedia_file", "file");
            if ($img) $img = "/imageg_400_300_" . $img . "_multimedia_" . $nomre . ".jpg"; else $img = "/no_image.png";
            $rel = "";
            $link = "/$lang-multimedia/$type-$nomre.html";
        }

        print "<div class='col-sm-6 col-md-4'>
        <div class='thumbnail'> <a $rel href='$link'> <img src='$img' alt='$name'> </a>
        <div class='caption'>
        <h3>$name</h3>
        </div>
        </div>
        </div>";
    }
    print "</div></div></div>";

    /*if($totalpage>1){
    print "<div class='course-pagination'>
    <ul class='pagination'>"; 
    if($page) $page=$page; else $page=1;
    for ($pn = 1; $pn <= $totalpage; $pn++) {
    //print "<li class='tg-prevpage'><a href='#'><i class='fa fa-angle-left'></i></a></li>";
    if($page==$pn)
    print "<li class='page-item active'><a class='page-link' href='/$lang/news/$category_id/$pn.html'>$pn</a></li>";
    else
    print "<li class='page-item'><a class='page-link' href='/$lang/news/$category_id/$pn.html'>$pn</a></li>";
    //print "<li class='tg-nextpage'><a href='#'><i class='fa fa-angle-right'></i></a></li>"; 
    }
    print "</ul>
    </div>"; 
    }*/
}

function image($m_id, $seh, $lang, $db_link)
{
    print "<div class='container gallery-container'><div class='tz-gallery'><div class='row'>";
    $i = 0;
    $tbl_news = $db_link->where('m_id', $m_id)->orderBy("id", "desc")->get('multimedia_file');
    foreach ($tbl_news as $line) {
        $id = $line["id"];
        $file = stripslashes($line["file"]);
        $name = stripslashes($line["name_" . $lang]);
        if ($file) $img = "/imageg_400_300_" . $file . "_multimedia_" . $m_id . ".jpg"; else $img = "/no_image.png";
        if ($file) $img1 = "/imageg_1000_1000_" . $file . "_multimedia_" . $m_id . ".jpg"; else $img1 = "/no_image.png";
        print "<div class='col-sm-6 col-md-4'>
        <div class='thumbnail'> <a class='lightbox' href='$img1'> <img src='$img' alt='$name'> </a>
        <div class='caption'>
        <h3>$name</h3>
        </div>
        </div>
        </div>";
    }
    print "</div></div></div>";
}

function substr_unicode($str, $s, $l = null)
{
    return join("", array_slice(
        preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}

function get_date($s)
{
    $s = explode(" ", $s);
    if ($s[0]) {
        $myarray = explode("-", $s[0]);
        if ($myarray[1] == 0) {
            return "";
        }
        return $myarray[2] . "." . $myarray[1] . "." . $myarray[0];
    }
    return "";
}

function get_date1($s)
{
    $myarray = explode("-", $s[0]);
    if ($myarray[1] == 0) {
        return "";
    }
    return $myarray[2] . "." . $myarray[1] . "." . $myarray[0];
}

function get_hour($s)
{
    if ($s) {
        $myarray = explode(":", $s);
        //if ($myarray[1]==0){ return "";}
        return $myarray[0] . ":" . $myarray[1];
    }
    return "";
}

function aylar($mon, $lang)
{
    if ($lang == 'az') {
        if ($mon == "01") {
            return "Yanvar";
        }
        if ($mon == "02") {
            return "Fevral";
        }
        if ($mon == "03") {
            return "Mart";
        }
        if ($mon == "04") {
            return "Aprel";
        }
        if ($mon == "05") {
            return "May";
        }
        if ($mon == "06") {
            return "Iyun";
        }
        if ($mon == "07") {
            return "Iyul";
        }
        if ($mon == "08") {
            return "Avqust";
        }
        if ($mon == "09") {
            return "Senyabr";
        }
        if ($mon == "10") {
            return "Oktyabr";
        }
        if ($mon == "11") {
            return "Noyabr";
        }
        if ($mon == "12") {
            return "Dekabr";
        }
    }
    if ($lang == 'ru') {
        if ($mon == "01") {
            return "Январь";
        }
        if ($mon == "02") {
            return "Февраль";
        }
        if ($mon == "03") {
            return "Март";
        }
        if ($mon == "04") {
            return "Апрель";
        }
        if ($mon == "05") {
            return "Май";
        }
        if ($mon == "06") {
            return "Июнь";
        }
        if ($mon == "07") {
            return "Июль";
        }
        if ($mon == "08") {
            return "Август";
        }
        if ($mon == "09") {
            return "Сентябрь";
        }
        if ($mon == "10") {
            return "Октябрь";
        }
        if ($mon == "11") {
            return "Ноябрь";
        }
        if ($mon == "12") {
            return "Декабрь";
        }
    }
    if ($lang == 'en') {
        if ($mon == "01") {
            return "January";
        }
        if ($mon == "02") {
            return "February";
        }
        if ($mon == "03") {
            return "March";
        }
        if ($mon == "04") {
            return "April";
        }
        if ($mon == "05") {
            return "May";
        }
        if ($mon == "06") {
            return "June";
        }
        if ($mon == "07") {
            return "July";
        }
        if ($mon == "08") {
            return "August";
        }
        if ($mon == "09") {
            return "September";
        }
        if ($mon == "10") {
            return "October";
        }
        if ($mon == "11") {
            return "November";
        }
        if ($mon == "12") {
            return "December";
        }
    }
}

function file_size($file, $path = "")
{
    define("DOCUMENT_ROOT", dirname(__FILE__));
    $bytes = array("B", "KB", "MB", "GB", "TB", "PB");
    $file_with_path = DOCUMENT_ROOT . "/" . $path . "/" . $file;
    $file_with_path = str_replace("//", "/", $file_with_path);
    $size = filesize($file_with_path);
    $i = 0;
    while ($size >= 1024) {
        $size = $size / 1024;
        $i++;
    }
    if ($i > 1) {
        return round($size, 1) . " " . $bytes[$i];
    } else {
        return round($size, 0) . " " . $bytes[$i];
    }

    //echo file_size("example.txt", "myFolder");
}

function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

function valid_email($str)
{
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

function valid_reg_email($str)
{
    global $db_link;
    //$email=@mysql_result(mysql_query("SELECT `email` FROM qeydiyyat where `Email`='$str'",$db_link), 0, 0);
    return ($email) ? FALSE : TRUE;
}

function tarix($vaxt)
{
    $gunler_az = array('Bazar', 'Bazar ertəsi', 'Çərşənbə axşamı', 'Çərşənbə', 'Cümə axşamı', 'Cümə', 'Şənbə');
    $aylar_az = array('', 'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'İyun', 'İyul', 'Ağustos', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr');
    return date("d ", $vaxt) . $aylar_az[date("n", $vaxt)] . ' ' . $gunler_az[date("w", $vaxt)];
}

function tarixen($vaxt)
{
    $gunler_en = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $aylar_en = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    return date("d ", $vaxt) . $aylar_en[date("n", $vaxt)] . ' ' . $gunler_en[date("w", $vaxt)];
}

function tarixru($vaxt)
{
    $gunler_ru = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
    $aylar_ru = array('', 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
    return date("d ", $vaxt) . $aylar_ru[date("n", $vaxt)] . ' ' . $gunler_ru[date("w", $vaxt)];
}

function get_day_wo_zero($str)
{
    $str = ltrim($str, '0');
    return $str;
}

function conv2utf8($encoding, $str)
{
    $strutf8 = iconv("windows-1251", "utf-8", $encoding);
}

function isMobile()
{
    return preg_match("/(android|iPhone|iphone|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|palm|phone|pie|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function getTheDay($date)
{
    $datetime = date('Y-m-d H:i:s', strtotime($date));
    list ($date, $time) = explode(' ', $datetime);
    $time = substr($time, 0, 5);
    list ($hour, $minute) = explode(':', $time);
    $return = ' ' . $hour . ':' . $minute;

    list ($year, $month, $day) = explode('-', $date);
    list ($year2, $month2, $day2) = explode(' ', date('Y m d'));

    if ($year == $year2 && $month == $month2 && $day == $day2) {
        return 'Bu gün ' . $return;
    }
    if ($year == $year2 && $month == $month2 && $day == $day2 - 1) {
        return 'Dünən ' . $return;
    }
    if ($year == $year2 && $month == $month2 && $day == $day2 - 2) {
        return '2 gün əvvəl ' . $return;
    }
    /*    if ($year == $year2 && $month == $month2 && $day == $day2 - 3){
    return '3 gün əvvəl ' . $return;
    }*/

    $msaat = '';
    $s = explode(" ", trim($datetime));
    if (isset($s[1]) && $s[1]) {
        $ms = explode(":", $s[1]);
        $msaat = ($ms[0] ?? '') . ":" . ($ms[1] ?? '');
    }

    if (isset($s[0]) && $s[0]) {
        $myarray = explode("-", $s[0]);
        if ($myarray[1] == 0) {
            return "";
        }
        return $myarray[2] . "." . $myarray[1] . "." . $myarray[0] . " " . $msaat;
    } else {
        return "";
    }
    //return $date . ' ' . $return;
}

function _bot_detected()
{
    return (isset($_SERVER['HTTP_USER_AGENT'])
        && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
    );
}

?>