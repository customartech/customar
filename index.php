<?php
//error_reporting(E_ALL);
//ini_set('display_errors', false);
//ini_set('display_startup_errors', false);
session_start();
$security_test = 1;

$request_path = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');

if (empty($_GET['lang']) || empty($_GET['type']) || !isset($_GET['cid']) || !isset($_GET['subid']) || !isset($_GET['seh']) || empty($_GET['view'])) {
    if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
    } elseif (preg_match('~^/([A-Za-z]+)-([A-Za-z]+)/$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
    } elseif (preg_match('~^/([A-Za-z]+)-([A-Za-z]+)-(\d+)/$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
        if (!isset($_GET['seh']) || $_GET['seh'] === '') $_GET['seh'] = $m[3];
    } elseif (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)\.html$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
        if (!isset($_GET['cid']) || $_GET['cid'] === '') $_GET['cid'] = $m[3];
    } elseif (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)/(\d+)\.html$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
        if (!isset($_GET['cid']) || $_GET['cid'] === '') $_GET['cid'] = $m[3];
        if (!isset($_GET['seh']) || $_GET['seh'] === '') $_GET['seh'] = $m[4];
    } elseif (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)-(\d+)\.html$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
        if (!isset($_GET['cid']) || $_GET['cid'] === '') $_GET['cid'] = $m[3];
        if (!isset($_GET['subid']) || $_GET['subid'] === '') $_GET['subid'] = $m[4];
    } elseif (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)-(\d+)/(\d+)\.html$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
        if (!isset($_GET['cid']) || $_GET['cid'] === '') $_GET['cid'] = $m[3];
        if (!isset($_GET['subid']) || $_GET['subid'] === '') $_GET['subid'] = $m[4];
        if (!isset($_GET['seh']) || $_GET['seh'] === '') $_GET['seh'] = $m[5];
    } elseif (preg_match('~^/([A-Za-z]+)-([A-Za-z]+)/([A-Za-z]+)-(\d+)\.html$~', $request_path, $m)) {
        if (empty($_GET['lang'])) $_GET['lang'] = $m[1];
        if (empty($_GET['type'])) $_GET['type'] = $m[2];
        if (empty($_GET['view'])) $_GET['view'] = $m[3];
        if (!isset($_GET['cid']) || $_GET['cid'] === '') $_GET['cid'] = $m[4];
    }
}

include_once("sqlinj.php");
$sqlyoxlama = new sqlinj;
$sqlyoxlama->basla("aio", "all");

$get_lang = $_GET['lang'] ?? null;
if ($get_lang === 'ru') {
    $_SESSION['lang'] = 'ru';
} elseif ($get_lang === 'en') {
    $_SESSION['lang'] = 'en';
} elseif ($get_lang === 'az') {
    $_SESSION['lang'] = 'az';
} elseif (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = 'az';
}

$lang = $_SESSION['lang'] ?? 'az';
$_SESSION['SendMailMessage'] = 'sendno';

include("mpan/config.php");
include "lang.php";
include "function.php";

apply_slug_routes_and_redirect($request_path, $lang, $db_link);

$type = (string)($_GET['type'] ?? 'main');
$allowedTypes = [
    'main', 'content', 'multimedia', 'news', 'opennews', 'tedbir', 'file',
    'photos', 'openphotos', 'services', 'openservice', 'industry', 'openindustry',
    'solution', 'opensolution', 'allservices', 'axtar', 'notpage'
];
if (!in_array($type, $allowedTypes, true)) {
    $type = 'notpage';
}

$cid = isset($_GET['cid']) ? (int)$_GET['cid'] : null;
$subid = isset($_GET['subid']) ? (int)$_GET['subid'] : null;
$seh = isset($_GET['seh']) ? (int)$_GET['seh'] : 1;
if ($seh < 1) $seh = 1;

$view = isset($_GET['view']) ? preg_replace('/[^a-z0-9_\-]/i', '', (string)$_GET['view']) : null;
$vid = isset($_GET['vid']) ? preg_replace('/[^a-z0-9_\-]/i', '', (string)$_GET['vid']) : null;
$create_date = null;

if ($type === 'opennews' && !empty($cid)) {
    $exists = $db_link->where('id', $cid)->getValue('news', 'id');
    if (empty($exists)) {
        $type = 'notpage';
        $_GET['type'] = 'notpage';
    }
}
if ($type === 'content' && !empty($cid)) {
    $exists = $db_link->where('id', $cid)->getValue('category', 'id');
    if (empty($exists)) {
        $type = 'notpage';
        $_GET['type'] = 'notpage';
    }
}
if ($type === 'news' && !empty($cid)) {
    $exists = $db_link->where('id', $cid)->getValue('category', 'id');
    if (empty($exists)) {
        $type = 'notpage';
        $_GET['type'] = 'notpage';
    }
}

if ($type === 'notpage') {
    http_response_code(404);
}



if ($type == 'content') $home_title = $db_link->where('id', $cid)->getValue("category", "name_".$lang);
elseif ($type == 'multimedia') {
    if ($view == 'youtube')
        $home_title = $db_link->where('id', $cid)->getValue("multimedia", "name_".$lang);
    elseif ($view == 'image')
        $home_title = $db_link->where('id', $cid)->getValue("multimedia", "name_".$lang);
    else
        $home_title = $db_link->where('id', $cid)->getValue("category", "name_".$lang);
} elseif ($type == 'news'){ 
    $home_title = $db_link->where('id', $cid)->getValue("category", "name_".$lang);
    if($subid)
        $home_title = $home_title." ".$db_link->where('id', $subid)->getValue("services_service", "title_".$lang);

}elseif ($type == 'opennews'){
    $create_date = $db_link->where('id', $cid)->getValue("news", "news_date"); 
    $home_title = $db_link->where('id', $cid)->getValue("news", "title_".$lang);
    //$home_id = $db_link->where('id', $cid)->getValue("news", "category_id");
    ///$home_title = $home_title." ".$db_link->where('id', $home_id)->getValue("category", "name_".$lang);
}elseif ($type == 'tedbir') $home_title = $db_link->where('id', $cid)->getValue("category", "name_".$lang);
elseif ($type == 'file') $home_title = $db_link->where('id', $cid)->getValue("category", "name_".$lang);
else $home_title = "Customar Interactive - Biznesiniz üçün rəqəmsal həllər";

if (empty($_SESSION['sessionid'])) $_SESSION['sessionid'] = date('Ymdhis') . yazi();
$sessionid = $_SESSION['sessionid'];
$home_title = (string)$home_title;
$home_title = str_replace('"', "", $home_title);
$home_title_meta = str_replace(" ", ",", $home_title);

if($create_date) $create_date=$create_date; else $create_date='2021-01-12 21:00:00';

$base_url = 'https://customar.tech';
$current_url = $base_url . ($_SERVER['REQUEST_URI'] ?? '/');
$seo_title = $home_title;
$seo_description = "Artırılmış Reallıq Azərbaycan, VR Azerbaijan, Virtual Reality, Mobile app, mobil tətbiqlər, sərgi stendləri, e-commerce, veb səhifələr, interaktiv stendlər, VR tur, 360 çəkiliş, 360 video";
$og_image_url = $base_url . '/assets/img/root/logo-white.png';

if ($type === 'opennews' && !empty($cid)) {
    $news_img = $db_link->where('id', $cid)->getValue('news', 'img');
    if (empty($news_img)) {
        $news_img = $db_link->where('id', $cid)->getValue('news', 'img1');
    }
    $news_cat = $db_link->where('id', $cid)->getValue('news', 'category_id');
    $news_content = $db_link->where('id', $cid)->getValue('news', 'content_' . $lang);

    $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string)$news_content)));
    if ($plain !== '') {
        $seo_description = mb_substr($plain, 0, 160);
    }

    if (!empty($news_img) && !empty($news_cat)) {
        $og_image_url = $base_url . "/imageg_1200_630_{$news_img}_news_{$news_cat}.jpg";
    }
}

?><!DOCTYPE html>
<html class="no-js ptf-is--home-studio ptf-is--header-style-2 ptf-is--footer-style-2 ptf-is--custom-cursor ptf-is--footer-fixed" lang="<?php print $_SESSION['lang'] ?>">
    <head>
        <link rel="canonical" href="https://customar.tech<?php print $_SERVER['REQUEST_URI']; ?>" />
        <title itemprop="name"><?php print $home_title; ?></title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
        <meta name="author" content="Customar Interactive"/>
        <meta http-equiv="Content-Language" content="<?php print $lang; ?>">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="ZgRmdawivLc9mjv_e9lEIJ1k3dqniKPS462y6yHIbGM" />
        <meta name="yandex-verification" content="a1dfd24d8a5796f0" />

        <meta name="description" content="<?php print htmlspecialchars($seo_description, ENT_QUOTES, 'UTF-8'); ?>">
        <meta name="keywords" content="<?php print $home_title_meta;?>">

        <meta property="og:site_name" content="Customar" />
        <meta property="og:type" content="<?php print ($type === 'opennews' ? 'article' : 'website'); ?>" />
        <meta property="og:url" content="<?php print htmlspecialchars($current_url, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta property="og:title" content="<?php print htmlspecialchars($seo_title, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta property="og:description" content="<?php print htmlspecialchars($seo_description, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta property="og:image" content="<?php print htmlspecialchars($og_image_url, ENT_QUOTES, 'UTF-8'); ?>" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="<?php print htmlspecialchars($seo_title, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="twitter:description" content="<?php print htmlspecialchars($seo_description, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="twitter:image" content="<?php print htmlspecialchars($og_image_url, ENT_QUOTES, 'UTF-8'); ?>" />

        <!--Favicon-->
        <link rel="apple-touch-icon" sizes="57x57" href="/fi/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/fi/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/fi/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/fi/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/fi/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/fi/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/fi/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/fi/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/fi/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/fi/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/fi/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/fi/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/fi/favicon-16x16.png">
        <link rel="manifest" href="fi/manifest.json"> 
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/fi/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <!--Framework-->
        <link rel="stylesheet" href="/assets/css/framework/bootstrap-reboot.min.css">
        <link rel="stylesheet" href="/assets/css/framework/bootstrap-grid.min.css">
        <link rel="stylesheet" href="/assets/css/framework/bootstrap-utilities.min.css">
        <!--Plugins-->

        <!--Fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap" rel="stylesheet">
        <!-- <link rel="stylesheet" href="/assets/fonts/CerebriSans/style.css"> -->
        <!--Icons-->
        <link rel="stylesheet" href="/assets/fonts/LineIcons-PRO/WebFonts/Pro-Light/font-css/LinIconsPro-Light.css">
        <link rel="stylesheet" href="/assets/fonts/LineIcons-PRO/WebFonts/Pro-Regular/font-css/LineIcons.css">
        <link rel="stylesheet" href="/assets/fonts/Socicons/socicon.css">
        <!--Style-->
        <link rel="stylesheet" href="/assets/css/ptf-main.min.css?v=2.4">
        <!--Custom-->

        <style>
            .ptf-is--home-studio .ptf-work__title,
            .ptf-is--home-studio .ptf-work__title *,
            .ptf-is--home-studio .ptf-work__title a {
                transition: color 250ms ease;
            }

            .ptf-is--home-studio .ptf-work:hover .ptf-work__title,
            .ptf-is--home-studio .ptf-work:hover .ptf-work__title *,
            .ptf-is--home-studio .ptf-work__title:hover,
            .ptf-is--home-studio .ptf-work__title:hover *,
            .ptf-is--home-studio .ptf-work__title a:hover,
            .ptf-is--home-studio .ptf-work__title a:hover * {
                color: #49c8f5 !important;
            }
        </style>

        <script type="application/ld+json">
            <?php
            $org = [
                '@type' => 'Organization',
                '@id' => $base_url . '/#organization',
                'name' => 'Customar',
                'url' => $base_url . '/',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $base_url . '/assets/img/root/logo-white.png',
                ],
                'sameAs' => [
                    'https://www.youtube.com/@CustomAr',
                    'https://www.facebook.com/customartech',
                    'https://www.instagram.com/customartech/',
                    'https://www.linkedin.com/company/customar',
                ],
            ];

            $website = [
                '@type' => 'WebSite',
                '@id' => $base_url . '/#website',
                'url' => $base_url . '/',
                'name' => 'Customar',
                'publisher' => ['@id' => $org['@id']],
                'inLanguage' => $lang,
            ];

            $webpage = [
                '@type' => 'WebPage',
                '@id' => $current_url . '#webpage',
                'url' => $current_url,
                'name' => $seo_title,
                'isPartOf' => ['@id' => $website['@id']],
                'about' => ['@id' => $org['@id']],
                'inLanguage' => $lang,
                'description' => $seo_description,
            ];

            $graph = [$org, $website, $webpage];

            if ($type === 'opennews') {
                $article = [
                    '@type' => 'NewsArticle',
                    'headline' => $seo_title,
                    'mainEntityOfPage' => ['@id' => $webpage['@id']],
                    'datePublished' => date(DATE_ATOM, strtotime($create_date)),
                    'dateModified' => date(DATE_ATOM, strtotime($create_date)),
                    'author' => ['@id' => $org['@id']],
                    'publisher' => ['@id' => $org['@id']],
                    'image' => [$og_image_url],
                    'description' => $seo_description,
                    'inLanguage' => $lang,
                ];
                $graph[] = $article;
            }

            print json_encode([
                '@context' => 'https://schema.org',
                '@graph' => $graph,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            ?>
        </script>
        <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
        
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>            
    </head>
    <!--Site Wrapper-->
    <div class="ptf-site-wrapper">
        <!--Site Wrapper Inner-->
        <div class="ptf-site-wrapper__inner">
            <!--Header-->
            <header class="ptf-header ptf-header--style-2 ptf-header--opaque">
                <div class="ptf-navbar ptf-navbar--main ptf-navbar--sticky">
                    <div class="container-xxl">
                        <div class="ptf-navbar-inner">
                            <!--Logo--><a class="ptf-navbar-logo" href="/index.html" aria-label="Home" title="Home">
                                <img class="black" src="/assets/svg/logo_dark.svg" width="150" alt="Customar Logo" decoding="async">
                                <img class="white" src="/assets/img/root/logo-white.png" alt="Customar Logo" decoding="async"></a>
                            <!--Navigation-->
                            <nav class="ptf-nav ptf-nav--default">
                                <!--Menu-->
                                <ul class="sf-menu">
                                    <li><a href="/<?php print $lang;?>/content/<?php print latin_slug($db_link->where('id', 6)->getValue('category', 'name_'.$lang)); ?>" aria-label="<?php print $haqq; ?>" title="<?php print $haqq; ?>"><span><?php print $haqq; ?></span></a></li>
                                    <li><a href="/<?php print $lang;?>/portfolio" aria-label="<?php print $projects; ?>" title="<?php print $projects; ?>"><span><?php print $projects; ?></span></a></li>
                                    <li><a href="/<?php print $lang;?>/content/<?php print latin_slug($db_link->where('id', 3)->getValue('category', 'name_'.$lang)); ?>" aria-label="<?php print $b_contact; ?>" title="<?php print $b_contact; ?>"><span><?php print $b_contact; ?></span></a></li>
                                    <!-- <li class="menu-item-has-children">
                                        <?php //if ($lang=='en') 
                                        //{ 
                                            //print "<a href='/az/main/'><span style='font-weight: 100;'>Az</span></a>"; 
                                        //} else {
                                            //print "<a href='/en/main/'><span style='font-weight: 100;'>En</span></a>";
                                        //}
                                        //?>
                                    </li> -->
                                    <li><a href="/<?php print $lang;?>/content/<?php print latin_slug($db_link->where('id', 3)->getValue('category', 'name_'.$lang)); ?>" aria-label="Layihən var?" title="Layihən var?"><span>Layihən var?</span></a></li>
                                </ul>
                            </nav>
                            <!--Offcanvas Menu Toggle--><a class="ptf-offcanvas-menu-icon js-offcanvas-menu-toggle" href="#" aria-label="Open menu" title="Open menu"><i class="lnir lnir-menu-alt-5"></i></a>
                        </div>
                    </div>
                </div>
            </header>
            <!--Site Overlay-->
            <div class="ptf-site-overlay"></div>
            <!--Offcanvas Menu-->
            <div class="ptf-offcanvas-menu">
                <div class="ptf-offcanvas-menu__header">
                    <div class="ptf-language-switcher"><?php if ($lang=='en') 
                        { 
                            print "<a href='/az/main/'><span style='font-weight: 100;'><img src='/az.png' alt='Azerbaijan' style='height: 30px;'> Azərbaycan versiyası</span></a>"; 
                        } else {
                            print "<a href='/en/main/'><span style='font-weight: 100;'><img src='/en.png' alt='English' style='height: 30px;'> English version</span></a>";
                        }
                    ?></div><a class="ptf-offcanvas-menu-icon js-offcanvas-menu-toggle" href="#" aria-label="Close menu" title="Close menu"><i class="lnir lnir-close"></i></a>
                </div>
                <div class="ptf-offcanvas-menu__navigation">
                    <!--Navigation-->
                    <nav class="ptf-nav ptf-nav--offcanvas">
                        <!--Menu-->
                        <ul class="sf-menu">
                            <li><a href="/<?php print $lang;?>/content/<?php print latin_slug($db_link->where('id', 6)->getValue('category', 'name_'.$lang)); ?>" aria-label="<?php print $haqq; ?>" title="<?php print $haqq; ?>"><span><?php print $haqq; ?></span></a></li>
                            <li><a href="/<?php print $lang;?>/portfolio" aria-label="<?php print $projects; ?>" title="<?php print $projects; ?>"><span><?php print $projects; ?></span></a></li>
                            <li><a href="/<?php print $lang;?>/content/<?php print latin_slug($db_link->where('id', 3)->getValue('category', 'name_'.$lang)); ?>" aria-label="<?php print $b_contact; ?>" title="<?php print $b_contact; ?>"><span><?php print $b_contact; ?></span></a></li>
                        </ul>
                    </nav>
                </div>
                <div class="ptf-offcanvas-menu__footer">
                    <p class="ptf-offcanvas-menu__copyright">© 2013 - <?php echo date("Y"); ?><span> Customar</span>. <?php print $b_all_rights_reserved; ?></p>
                    <div class="ptf-offcanvas-menu__socials">
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-1 youtube" href="https://www.youtube.com/@CustomAr"
                            target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube"><i class="socicon-youtube"></i></a>
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-1 facebook" href="https://www.facebook.com/customartech"
                            target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook"><i class="socicon-facebook"></i></a>
                        <!--Social Icon--><a class="ptf-social-icon ptf-social-icon--style-1 instagram" href="https://www.instagram.com/customartech/"
                            target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><i class="socicon-instagram"></i></a>
                    </div>
                </div>
            </div>
            <!--Main-->
            <!--Main-->
            <main class="ptf-main">

                <?php print "<h1 class='visually-hidden'>" . htmlspecialchars($seo_title, ENT_QUOTES, 'UTF-8') . "</h1>"; ?>

                <?php
                if (empty($type)) {
                    include "main.php";
                }

                if ($type == 'main') {
                    include "main.php";
                }

                if ($type == 'notpage') {
                    not_found_page($lang);
                }

                if ($type == 'content') {
                    if (empty($cid)) {
                        not_found_page($lang);
                    }
                    elseif ($cid == 3)
                        include "contact.php";
                    elseif ($cid == 12)
                        content_menyu($cid, $lang, $db_link);
                    else
                        content($cid, $lang, $db_link);
                }

                if ($type == 'opennews') {
                    if (empty($cid)) {
                        not_found_page($lang);
                    } else {
                        xeber_ac($cid, $lang, $db_link);
                    }
                }



                if ($type == 'news') {
                    if (empty($cid)) {
                        not_found_page($lang);
                    } else {
                        xeberler($cid, $seh, $lang, $db_link);
                    }
                }

                if ($type == 'photos')
                    photos($cid, $seh, $lang, $db_link);

                if ($type == 'multimedia') {
                    if ($view == 'youtube')
                        youtube($cid, $lang, $db_link);
                    elseif ($view == 'image')
                        image($cid, $seh, $lang, $db_link);
                    else
                        multimedia($cid, $seh, $lang, $db_link);
                }
                if ($type == 'axtar')
                    search($_POST['search_text'], $lang, $seh, $db_link);

                ?>


            </main>
        </div>
        <!--Footer-->
        <footer class="ptf-footer ptf-footer--style-2">
            <div class="container-xxl">
                <div class="ptf-footer__top">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <!--Animated Block-->
                            <div class="ptf-animated-block" data-aos="fade" data-aos-delay="200">
                                <div class="ptf-widget ptf-widget-text"><a class="fz-36 has-black-color"
                                        href="mailto:info@customar.tech">info@customar.tech</a>
                                    <!--Spacer-->
                                    <div class="ptf-spacer" style=" --ptf-xxl: 0.625rem;"></div>
                                    <p class="fz-18"><?php print $recebli; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <!--Animated Block-->
                            <div class="ptf-animated-block" data-aos="fade" data-aos-delay="200">
                                <div class="ptf-widget ptf-widget-text" style="display: flex;"><iframe width="150" height="150" src="https://shareables.clutch.co/share/badges/747622/101810?utm_source=clutch_top_company_badge&utm_medium=image_embed" title="Vr Development Company Azerbaijan 2024"></iframe>
                                    <!--Spacer-->
                                    <div class="ptf-spacer" style=" --ptf-xxl: 0.625rem;"></div>
                                    <iframe width="150" height="150" src="https://shareables.clutch.co/share/badges/747622/104998?utm_source=clutch_top_company_badge&utm_medium=image_embed" title="Top Clutch Digital Design Company Azerbaijan 2024"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ptf-footer__bottom">
                    <div class="row align-items-center">
                        <div class="col-12 col-md">
                            <p class="ptf-footer-copyright has-black-color">© 2014 - <?php echo date("Y"); ?><span> Customar</span>. <?php print $b_all_rights_reserved; ?></p>
                        </div>
                        <div class="col-12 col-md d-none d-xl-block"></div>
                        <div class="col-12 col-lg">
                            <div class="ptf-footer-socials has-black-color" style="float: right;">
                                <!--Social Icon-->
                                <a class="ptf-social-icon ptf-social-icon--style-1 youtube" href="https://www.youtube.com/@CustomAr" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube"><i class="socicon-youtube"></i></a>
                                <!--Social Icon-->
                                <a class="ptf-social-icon ptf-social-icon--style-1 facebook" href="https://www.facebook.com/customartech" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook"><i class="socicon-facebook"></i></a>
                                <!--Social Icon-->
                                <a class="ptf-social-icon ptf-social-icon--style-1 instagram" href="https://www.instagram.com/customartech/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><i class="socicon-instagram"></i></a>
                                <!--Social Icon-->
                                <a class="ptf-social-icon ptf-social-icon--style-1 instagram" href="https://www.linkedin.com/company/customar" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" title="LinkedIn"><i class="socicon-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="/assets/scripts/ptf-plugins.min.js"></script>
    <script src="/assets/scripts/ptf-helpers.js"></script>
    <script src="/assets/scripts/ptf-controllers.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
document.querySelectorAll('.sf-menu a').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); 
            event.stopImmediatePropagation(); 

           
            window.location.href = link.getAttribute('href');
        }, true); 
    });
});
</script>
<script>
  document.querySelectorAll('.ptf-work__meta').forEach(function (div) {
    div.addEventListener('click', function () {
      const link = div.querySelector('a');
      if (link && link.href) {
        window.location.href = link.href;
      }
    });
  });
</script>



    <script>
        $(document).ready(function(){
            $('.customer-logos').slick({
                slidesToShow: 8,
                autoplay: true,
                arrows: false,
                dots: false,
                pauseOnHover: false,


                autoplaySpeed: 0,
                speed: 4000,
                cssEase:'linear',
                infinite: true,
                focusOnSelect: false,

                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 6
                    }
                    }, {
                        breakpoint: 520,
                        settings: {
                            slidesToShow: 3
                        }
                }]
            });
        });
    </script>
    </body>

</html>