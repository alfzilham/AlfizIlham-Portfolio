<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $config['title']; ?></title>
  <meta name="description" content="<?php echo $config['description']; ?>" />
  <meta property="og:title" content="<?php echo $config['title']; ?>" />
  <meta property="og:description" content="<?php echo $config['description']; ?>" />
  <meta property="og:image" content="assets/favicon/Logo.png" />
  <meta property="og:type" content="website" />
  <meta name="theme-color" content="#0a0a0a" />
  <link rel="icon" type="image/x-icon" href="assets/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <!-- Bootstrap Icons (icon font only) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <!-- App CSS -->
  <link rel="stylesheet" href="assets/css/global.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <link rel="stylesheet" href="assets/css/responsive.css" />
</head>
<body>

  <div id="scrollProgress" aria-hidden="true"></div>

  <!-- NAVBAR -->
  <?php echo View::partial('navbar', [
      'lang' => $lang,
      'social' => $config['social'],
  ]); ?>

  <main>

    <!-- SECTION 1: INTRO -->
    <?php echo View::section('intro'); ?>

    <!-- SECTION 2: HERO -->
    <?php echo View::section('hero'); ?>

    <!-- SECTION 3: CURVED MARQUEE -->
    <?php echo View::section('curved-marquee'); ?>

    <!-- SECTION 4: ABOUT ME DETAIL -->
    <?php echo View::section('about'); ?>

    <!-- SECTION 5: BIO STATEMENT + STATS -->
    <?php echo View::section('bio-stats'); ?>

    <!-- SECTION 6: MY SKILL SET -->
    <?php echo View::section('skills', ['tools' => $tools]); ?>

    <!-- SECTION 7: PROJECTS TEASER -->
    <?php echo View::section('projects-teaser', ['counts' => $projectCounts]); ?>

    <!-- SECTION 8: PROJECTS GRID -->
    <?php echo View::section('projects', ['projects' => $projects]); ?>

    <!-- SECTION 9: SERVICES -->
    <?php echo View::section('services', ['services' => $services]); ?>

    <!-- SECTION 10: TECH MARQUEE -->
    <?php echo View::section('tech-marquee'); ?>

    <!-- SECTION 11: GALLERY -->
    <?php echo View::section('gallery', ['gallery' => $gallery]); ?>

    <!-- SECTION 12: TESTIMONIALS -->
    <?php echo View::section('testimonials', ['testimonials' => $testimonials]); ?>

    <!-- SECTION 13: FAQ -->
    <?php echo View::section('faq', ['faqs' => $faqs, 'faqCategories' => $faqCategories]); ?>

    <!-- SECTION 14: CONTACT -->
    <?php echo View::section('contact', [
        'emailjs' => $config['emailjs'],
        'whatsapp' => $config['whatsapp'],
    ]); ?>

    <!-- SECTION 15: CLOSING CTA -->
    <?php echo View::section('cta-closing'); ?>

  </main>

  <!-- FOOTER -->
  <?php echo View::partial('footer', [
      'social' => $config['social'],
      'visitorCount' => $visitorCount,
  ]); ?>

  <!-- Scripts -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <script src="https://unpkg.com/lenis/dist/lenis.min.js"></script>

  <!-- i18n data for JS -->
  <script>
    window.__LANG = '<?php echo $lang; ?>';
    window.__LANG_DATA = <?php echo json_encode([
        'cta_clock_prefix' => i18n::t('cta_clock_prefix'),
    ], JSON_UNESCAPED_UNICODE); ?>;
  </script>

  <script src="assets/js/main.js"></script>
</body>
</html>
