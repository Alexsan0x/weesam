<?php

session_start();

$db_host = 'ep-calm-lab-alag0l8u-pooler.c-3.eu-central-1.aws.neon.tech';
$db_port = '5432';
$db_name = 'neondb';
$db_user = 'neondb_owner';
$db_pass = 'npg_PrOcKE2Za9Gh';

$google_maps_api_key = 'AIzaSyDb9lDX6ULJGKE74MK9iNJJFpdnKUgWThE';
$gemini_api_key = 'AIzaSyBTjZa_s5I6_satso78Mr2ryuxrQqMZhuU';

$site_name = 'Dalili';
$site_url = 'http://localhost/weesam';

date_default_timezone_set('Asia/Amman');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] === 'ar') ? 'ar' : 'en';
}
$lang = $_SESSION['lang'] ?? 'en';
$isArabic = ($lang === 'ar');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'] ?? 'user'
        ];
    }
    return null;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function t($en, $ar) {
    global $isArabic;
    return $isArabic ? $ar : $en;
}
