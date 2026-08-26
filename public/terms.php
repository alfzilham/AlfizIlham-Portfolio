<?php
require_once dirname(__DIR__) . '/bootstrap.php';
secure_session_start();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!in_array($lang, ['en', 'id'], true)) $lang = 'en';

echo View::render('layouts/legal', [
    'lang' => $lang,
    'pageTitle' => 'Terms of Service — Alfiz Ilham',
    'heading' => 'Terms of Service',
    'contentHtml' => View::render('pages/terms'),
]);
