<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $pageTitle; ?></title>
  <meta name="robots" content="noindex, nofollow" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/global.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <link rel="stylesheet" href="assets/css/responsive.css" />
</head>
<body>
  <div class="container" style="max-width:720px;padding-top:80px;padding-bottom:80px;">
    <p class="eyebrow"><?= i18n::t('legal_label') ?></p>
    <h1 style="font-size:clamp(28px,4vw,48px);font-weight:800;margin-bottom:32px;"><?php echo $heading; ?></h1>
    <p style="color:var(--color-text-muted);margin-bottom:24px;font-size:14px;"><?= i18n::t('legal_updated') ?></p>

    <?php echo $contentHtml; ?>

    <div style="margin-top:48px;padding-top:24px;border-top:1px solid var(--color-border);">
      <a href="/" style="color:var(--color-text);text-decoration:none;font-weight:600;">&larr; <?= i18n::t('legal_back') ?></a>
    </div>
  </div>
</body>
</html>
