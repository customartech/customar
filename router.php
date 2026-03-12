<?php
// Router for PHP built-in server to emulate .htaccess rewrites
// Usage: php -S 127.0.0.1:8001 -t . router.php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$path = __DIR__ . $uri;

// Serve existing files as-is
if ($uri !== '/' && is_file($path)) {
    return false;
}

// Helper to route to a php script with $_GET params
$run = static function (string $script, array $params = []): void {
    foreach ($params as $k => $v) {
        $_GET[$k] = $v;
    }
    require __DIR__ . '/' . ltrim($script, '/');
};

// index.html -> index.php
if (preg_match('~^/index\.html$~', $uri)) {
    $run('index.php');
    return;
}

// logout.html, forgot.html
if (preg_match('~^/logout\.html$~', $uri)) {
    $run('logout.php');
    return;
}
if (preg_match('~^/forgot\.html$~', $uri)) {
    $run('forgot.php');
    return;
}

// captcha.jpg
if (preg_match('~^/captcha\.jpg$~', $uri)) {
    $run('img_captcha.php');
    return;
}

// feed.xml
if (preg_match('~^/feed\.xml$~', $uri)) {
    $run('rss.php');
    return;
}

// axtar.html
if (preg_match('~^/axtar\.html$~', $uri)) {
    $run('index.php', ['lang' => 'az', 'type' => 'axtar']);
    return;
}

// image rewrites
if (preg_match('~^/image_(\\d+)_(\\d+)_(.*)\\.jpg$~', $uri, $m)) {
    $run('image.php', ['img' => $m[3], 'w' => $m[1], 'h' => $m[2]]);
    return;
}
if (preg_match('~^/imageg_(\\d+)_(\\d+)_(.+)_([A-Za-z]+)_(\\d+)\\.jpg$~', $uri, $m)) {
    $run('image.php', ['img' => $m[3], 'w' => $m[1], 'h' => $m[2], 'type' => $m[4], 'cid' => $m[5]]);
    return;
}
if (preg_match('~^/imagen_(\\d+)_(\\d+)_(.+)_([A-Za-z]+)\\.jpg$~', $uri, $m)) {
    $run('image.php', ['img' => $m[3], 'w' => $m[1], 'h' => $m[2], 'type' => $m[4]]);
    return;
}
if (preg_match('~^/img_(\\d+)_(\\d+)_(.*)$~', $uri, $m)) {
    $run('imgcr.php', ['img' => $m[3], 'w' => $m[1], 'h' => $m[2]]);
    return;
}

// token confirmation
if (preg_match('~^/tesdiq_(.*)\.html$~', $uri, $m)) {
    $run('tesdiq.php', ['token' => $m[1]]);
    return;
}

// Pretty routes -> index.php
if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)-([A-Za-z]+)/$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)-([A-Za-z]+)-(\d+)/$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2], 'seh' => $m[3]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)\.html$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2], 'cid' => $m[3]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)/(\d+)\.html$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2], 'cid' => $m[3], 'seh' => $m[4]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)-(\d+)\.html$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2], 'cid' => $m[3], 'subid' => $m[4]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)/([A-Za-z]+)/(\d+)-(\d+)/(\d+)\.html$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2], 'cid' => $m[3], 'subid' => $m[4], 'seh' => $m[5]]);
    return;
}
if (preg_match('~^/([A-Za-z]+)-([A-Za-z]+)/([A-Za-z]+)-(\d+)\.html$~', $uri, $m)) {
    $run('index.php', ['lang' => $m[1], 'type' => $m[2], 'view' => $m[3], 'cid' => $m[4]]);
    return;
}

// Default: route all unknowns to index.php so ErrorDocument works similarly
$run('index.php');
