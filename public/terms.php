<?php
require_once dirname(__DIR__) . '/bootstrap.php';
secure_session_start();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? config('default_language', 'id');
if (!in_array($lang, ['en', 'id'], true)) $lang = config('default_language', 'id');
i18n::load($lang);
$legalView = $lang === 'id' ? 'pages/terms-id' : 'pages/terms';

echo View::render('layouts/legal', [
    'lang' => $lang,
    'pageTitle' => i18n::t('legal_terms_title') . ' — Alfiz Ilham',
    'heading' => i18n::t('legal_terms_title'),
    'contentHtml' => View::render($legalView),
]);
