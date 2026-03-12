<?php
session_start();
error_reporting(0);
include('SimpleImage.php'); 
$type  = isset($_GET['type']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', $_GET['type']) : '';
$cid  = isset($_GET['cid']) ? preg_replace('/[^0-9]/', '', $_GET['cid']) : '';
$save = $_GET['save'] ?? null;
$img = isset($_GET['img']) ? trim($_GET['img']) : '';
$img = preg_replace('/\s+/', '', $img);
$path = "uploads/$type/$cid/" . $img;
if ($_GET["w"]) $width = (int)$_GET["w"]; else $width = 640;
if ($_GET["h"]) $heigt = (int)$_GET["h"]; else $heigt = 480;

if (!$type || !$cid || !$img) {
    http_response_code(404);
    exit;
}

if (!is_file($path)) {
    http_response_code(404);
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

// SimpleImage can't read SVG. Serve the original SVG directly.
if ($ext === 'svg' || $ext === 'svgz') {
    header('Content-Type: image/svg+xml');
    header('X-Content-Type-Options: nosniff');
    readfile($path);
    exit;
}
$ext = pathinfo($path, PATHINFO_EXTENSION);
$new_image=str_replace(".".$ext,"_t.".$ext,$path);
try {
    $image = new \claviska\SimpleImage();
    $image->fromFile($path);                     // load image.jpg
    //->autoOrient()                              // adjust orientation based on exif data
    $image->fitToWidth($width);                          // resize to 320x200 pixels
    //->flip('x')                                 // flip horizontally
    //->colorize('DarkBlue')                      // tint dark blue
    //->border('black', 10)                       // add a 10 pixel black border
    //->overlay('watermark.png', 'bottom right')  // add a watermark image
    if($save==1) $image->toFile($new_image);      // convert to PNG and save a copy to new-image.png
    if($save==2) $image->toFile($path);      // convert to PNG and save a copy to new-image.png
    //$image->toScreen();
    $image->toScreen('image/webp',75);                               // output to the screen
} catch(Exception $err) {
    echo $err->getMessage();
}


/*$type  = $_GET['type'];
$cid  = $_GET['cid'];
$save  = $_GET['save'];
$path = $_GET['img'];
if($_GET["w"])$width=$_GET["w"];else $width=400;
if($_GET["h"])$heigt=$_GET["h"];else $heigt=300;
$image = new abeautifulsite\SimpleImage($path); 
$image->resize($width, $heigt); 
$image->opacity(.5); 
if($save) $image->save($path."_t.jpg"); else $image->output(); */


?>