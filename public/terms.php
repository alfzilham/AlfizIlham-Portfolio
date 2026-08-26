<?php
session_start();
require_once dirname(__DIR__) . '/bootstrap.php';
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!in_array($lang, ['en', 'id'], true)) $lang = 'en';

echo View::render('layouts/legal', [
    'lang' => $lang,
    'pageTitle' => 'Terms of Service — Alfiz Ilham',
    'heading' => 'Terms of Service',
    'contentHtml' => View::render('pages/terms'),
]);
