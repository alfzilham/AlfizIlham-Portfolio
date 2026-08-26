<?php
session_start();
require_once dirname(__DIR__) . '/bootstrap.php';
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!in_array($lang, ['en', 'id'], true)) $lang = 'en';

echo View::render('layouts/legal', [
    'lang' => $lang,
    'pageTitle' => 'Privacy Policy — Alfiz Ilham',
    'heading' => 'Privacy Policy',
    'contentHtml' => View::render('pages/privacy'),
]);
