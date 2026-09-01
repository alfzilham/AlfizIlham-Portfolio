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

  <!-- Bootstrap Icons (icon font only) — pinned + SRI -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous" />

  <!-- Leaflet CSS — pinned + SRI -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous" />

  <!-- App CSS -->
  <link rel="stylesheet" href="assets/css/global.css?v=<?= filemtime(PUBLIC_PATH . '/assets/css/global.css') ?>" />
  <link rel="stylesheet" href="assets/css/components.css?v=<?= filemtime(PUBLIC_PATH . '/assets/css/components.css') ?>" />
  <link rel="stylesheet" href="assets/css/responsive.css?v=<?= filemtime(PUBLIC_PATH . '/assets/css/responsive.css') ?>" />
</head>
<body>

  <div class="loading-overlay" id="pageLoadingOverlay" role="status" aria-label="Loading">
    <div class="honeycomb" aria-hidden="true">
      <div></div><div></div><div></div><div></div><div></div><div></div><div></div>
    </div>
  </div>

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

    <!-- SECTION 6: EDUCATION -->
    <?php echo View::section('education'); ?>

    <!-- SECTION 7: MY SKILL SET -->
    <?php echo View::section('skills', ['tools' => $tools]); ?>

    <!-- SECTION 7: PROJECTS TEASER -->
    <?php echo View::section('projects-teaser', ['projects' => $projects, 'showcase' => $showcase]); ?>

    <!-- SECTION 9: SERVICES -->
    <?php echo View::section('services', ['services' => $services]); ?>

    <!-- SECTION 10: TECH MARQUEE -->
    <?php echo View::section('tech-marquee'); ?>

    <!-- SECTION 11: GALLERY -->
    <?php echo View::section('gallery', ['gallery' => $gallery]); ?>

    <!-- SECTION 12: TESTIMONIALS -->
    <?php echo View::section('testimonials', ['testimonials' => $testimonials]); ?>

    <!-- SECTION 13: CERTIFICATES -->
    <?php echo View::section('certificates', ['certificates' => $certificates]); ?>

    <!-- SECTION 14: FAQ -->
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

  <?php echo View::partial('chatbot'); ?>

  <!-- Editor mode: login modal -->
  <div class="editor-overlay" id="editorLoginOverlay" data-lenis-prevent hidden>
    <div class="editor-modal" role="dialog" aria-modal="true" aria-labelledby="editorLoginTitle">
      <button type="button" class="modal-close" data-close-modal aria-label="<?= i18n::t('lightbox_close') ?>">&times;</button>
      <h2 id="editorLoginTitle"><?= i18n::t('editor_login_title') ?></h2>
      <p class="editor-modal-sub"><?= i18n::t('editor_login_sub') ?></p>
      <form id="editorLoginForm" novalidate>
        <input type="password" id="editorPassword" name="password" placeholder="<?= i18n::t('editor_password_placeholder') ?>" autocomplete="current-password" required />
        <p class="form-error" id="editorLoginError" aria-live="polite"></p>
        <button type="submit" class="btn btn-primary btn-block"><?= i18n::t('editor_login_btn') ?></button>
      </form>
    </div>
  </div>

  <!-- Editor mode: card form modal (create/edit) -->
  <div class="editor-overlay" id="cardFormOverlay" data-lenis-prevent hidden>
    <div class="editor-modal editor-modal-wide" role="dialog" aria-modal="true" aria-labelledby="cardFormTitle">
      <button type="button" class="modal-close" data-close-modal aria-label="<?= i18n::t('lightbox_close') ?>">&times;</button>
      <h2 id="cardFormTitle"><?= i18n::t('form_add_title') ?></h2>
      <form id="cardForm" novalidate>
        <label for="cardTitle"><?= i18n::t('form_title_label') ?></label>
        <input type="text" id="cardTitle" name="title" placeholder="<?= i18n::t('form_title_placeholder') ?>" required />
        <span class="form-error" id="cardTitleError" aria-live="polite"></span>

        <label for="cardDescription"><?= i18n::t('form_desc_label') ?></label>
        <textarea id="cardDescription" name="description" rows="3" placeholder="<?= i18n::t('form_desc_placeholder') ?>" required></textarea>
        <span class="form-error" id="cardDescError" aria-live="polite"></span>

        <label for="cardLink"><?= i18n::t('form_link_label') ?></label>
        <input type="url" id="cardLink" name="link" placeholder="<?= i18n::t('form_link_placeholder') ?>" />
        <span class="form-error" id="cardLinkError" aria-live="polite"></span>

        <label><?= i18n::t('form_image_label') ?></label>
        <div class="dropzone" id="dropzone">
          <input type="file" id="cardImage" name="image" accept="image/jpeg,image/png,image/webp,image/gif" hidden />
          <div class="dropzone-empty" id="dropzoneEmpty">
            <i data-lucide="upload-cloud"></i>
            <p><?= i18n::t('upload_drop_text') ?></p>
            <span class="dropzone-browse"><?= i18n::t('upload_browse') ?></span>
          </div>
          <div class="dropzone-preview" id="dropzonePreview" title="<?= i18n::t('upload_change_hint') ?>" hidden>
            <img id="dropzonePreviewImg" alt="" />
            <span class="dropzone-name" id="dropzoneName"></span>
          </div>
        </div>
        <span class="form-error" id="cardImageError" aria-live="polite"></span>

        <button type="submit" class="btn btn-primary btn-block" id="cardFormSubmit"><?= i18n::t('editor_submit') ?></button>
      </form>
    </div>
  </div>

  <!-- Editor mode: delete confirmation modal -->
  <div class="editor-overlay" id="deleteOverlay" data-lenis-prevent hidden>
    <div class="editor-modal editor-modal-sm" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
      <div class="delete-icon"><i data-lucide="triangle-alert"></i></div>
      <h2 id="deleteModalTitle"><?= i18n::t('delete_modal_title') ?></h2>
      <p class="delete-card-name" id="deleteCardName"></p>
      <div class="delete-actions">
        <button type="button" class="btn btn-secondary" data-close-delete><?= i18n::t('delete_cancel') ?></button>
        <button type="button" class="btn btn-danger" id="deleteConfirmBtn"><?= i18n::t('editor_delete') ?></button>
      </div>
    </div>
  </div>

  <!-- Editor mode: certificate form modal -->
  <div class="editor-overlay" id="certFormOverlay" data-lenis-prevent hidden>
    <div class="editor-modal editor-modal-wide" role="dialog" aria-modal="true" aria-labelledby="certFormTitle">
      <button type="button" class="modal-close" data-close-modal aria-label="<?= i18n::t('lightbox_close') ?>">&times;</button>
      <h2 id="certFormTitle"><?= i18n::t('form_add_title') ?></h2>
      <form id="certForm" novalidate>
        <label for="certTitle"><?= i18n::t('form_title_label') ?></label>
        <input type="text" id="certTitle" name="title" placeholder="<?= i18n::t('form_title_placeholder') ?>" required />
        <span class="form-error" id="certTitleError" aria-live="polite"></span>

        <label for="certCompany"><?= i18n::t('form_company_label') ?></label>
        <input type="text" id="certCompany" name="company" placeholder="<?= i18n::t('form_company_placeholder') ?>" />
        <span class="form-error" id="certCompanyError" aria-live="polite"></span>

        <label for="certCredentialId"><?= i18n::t('form_credential_id_label') ?></label>
        <input type="text" id="certCredentialId" name="credential_id" placeholder="<?= i18n::t('form_credential_id_placeholder') ?>" />
        <span class="form-error" id="certCredentialIdError" aria-live="polite"></span>

        <label for="certCredentialLink"><?= i18n::t('form_credential_link_label') ?></label>
        <input type="url" id="certCredentialLink" name="credential_link" placeholder="<?= i18n::t('form_credential_link_placeholder') ?>" />
        <span class="form-error" id="certCredentialLinkError" aria-live="polite"></span>

        <label><?= i18n::t('form_image_label') ?></label>
        <div class="dropzone" id="certDropzone">
          <input type="file" id="certImage" name="image" accept="image/jpeg,image/png,image/webp,image/gif" hidden />
          <div class="dropzone-empty" id="certDropzoneEmpty">
            <i data-lucide="upload-cloud"></i>
            <p><?= i18n::t('upload_drop_text') ?></p>
            <span class="dropzone-browse"><?= i18n::t('upload_browse') ?></span>
          </div>
          <div class="dropzone-preview" id="certDropzonePreview" title="<?= i18n::t('upload_change_hint') ?>" hidden>
            <img id="certDropzonePreviewImg" alt="" />
            <span class="dropzone-name" id="certDropzoneName"></span>
          </div>
        </div>
        <span class="form-error" id="certImageError" aria-live="polite"></span>

        <button type="submit" class="btn btn-primary btn-block" id="certFormSubmit"><?= i18n::t('editor_submit') ?></button>
      </form>
    </div>
  </div>

  <!-- Lightbox (macOS-style viewer) -->
  <div class="lb-viewer" id="lightbox" data-lenis-prevent hidden role="dialog" aria-modal="true" aria-label="Project viewer">
    <div class="lb-counter" id="lbCounter">1 / 1</div>

    <div class="lb-actions">
      <a href="#" id="lbLinkBtn" class="lb-action" target="_blank" rel="noopener" hidden aria-label="Visit live site">
        <i data-lucide="external-link"></i>
      </a>
      <button type="button" class="lb-action" data-close-lightbox aria-label="<?= i18n::t('lightbox_close') ?>">
        <i data-lucide="x"></i>
      </button>
    </div>

    <button type="button" class="lb-arrow lb-arrow--prev" id="lbPrev" aria-label="Previous">
      <i data-lucide="chevron-left"></i>
    </button>

    <figure class="lb-stage">
      <img id="lightboxImage" src="" alt="" />
      <figcaption class="lb-scrim">
        <h3 id="lightboxTitle"></h3>
        <p id="lightboxDescription"></p>
        <a href="#" id="lbVisitLink" target="_blank" rel="noopener" hidden>
          <?= i18n::t('lb_visit') ?> <i data-lucide="external-link"></i>
        </a>
      </figcaption>
    </figure>

    <button type="button" class="lb-arrow lb-arrow--next" id="lbNext" aria-label="Next">
      <i data-lucide="chevron-right"></i>
    </button>

    <div class="lb-filmstrip" id="lbFilmstrip"></div>
  </div>

  <!-- Editor mode floating toolbar -->
  <div class="editor-toolbar" id="editorToolbar">
    <div class="editor-toolbar-status">
      <span class="dot"></span>
      <span class="editor-toolbar-label"><?= i18n::t('editor_mode_badge') ?></span>
    </div>
    <span class="editor-toolbar-divider"></span>
    <button type="button" id="addProjectBtn" class="editor-toolbar-cta">
      <i data-lucide="plus"></i><?= i18n::t('editor_add_project') ?>
    </button>
    <span class="editor-toolbar-divider"></span>
    <button type="button" id="addCertBtn" class="editor-toolbar-cta">
      <i data-lucide="plus"></i><?= i18n::t('editor_add_certificate') ?>
    </button>
    <span class="editor-toolbar-divider"></span>
    <button type="button" id="bulkImportBtn" class="editor-toolbar-cta">Import JSON</button>
    <span class="editor-toolbar-divider"></span>
    <button type="button" id="exportProjectBtn" class="editor-toolbar-btn"><i data-lucide="download"></i>Projects JSON</button>
    <button type="button" id="exportCertBtn" class="editor-toolbar-btn"><i data-lucide="download"></i>Certificates JSON</button>
    <span class="editor-toolbar-divider"></span>
    <button type="button" id="editorLogoutBtn" class="editor-toolbar-btn editor-toolbar-muted">
      <i data-lucide="user-x"></i><?= i18n::t('editor_logout') ?>
    </button>
  </div>

  <div class="editor-overlay" id="bulkImportOverlay" data-lenis-prevent hidden>
    <div class="editor-modal bulk-import-modal" role="dialog" aria-modal="true" aria-labelledby="bulkImportTitle">
      <button type="button" class="modal-close" data-close-bulk-import aria-label="Close">&times;</button>
      <p class="bulk-import-eyebrow">EDITOR TOOLS</p>
      <h2 id="bulkImportTitle">Bulk import JSON</h2>
      <p class="editor-modal-sub">Import up to 50 records. Existing titles are skipped. Images must be public HTTP(S) URLs and are downloaded as local WebP files.</p>
      <div class="bulk-import-field">
        <label for="bulkImportType">Content type</label>
        <div class="bulk-import-select-wrap">
          <button type="button" class="bulk-import-select-trigger" id="bulkImportTypeTrigger" aria-haspopup="listbox" aria-expanded="false">
            <span id="bulkImportTypeValue">Projects</span>
            <i data-lucide="chevron-down" aria-hidden="true"></i>
          </button>
          <div class="bulk-import-options" id="bulkImportTypeOptions" role="listbox" hidden>
            <button type="button" role="option" aria-selected="true" data-value="projects">Projects</button>
            <button type="button" role="option" aria-selected="false" data-value="certificates">Certificates</button>
          </div>
          <input type="hidden" id="bulkImportType" value="projects" />
        </div>
      </div>
      <div class="bulk-import-field">
        <span class="bulk-import-label">JSON file</span>
        <div class="bulk-import-dropzone" id="bulkImportDropzone">
          <input type="file" id="bulkImportFile" class="bulk-import-file-input" accept="application/json,.json" />
          <div class="bulk-import-dropzone-inner">
            <span class="bulk-import-dropzone-icon"><i data-lucide="upload-cloud" aria-hidden="true"></i></span>
            <span class="bulk-import-dropzone-text"><strong>Drag & drop a JSON file here</strong></span>
            <span class="bulk-import-dropzone-sub">or click to browse · Maximum 50 records · .json only</span>
            <span class="bulk-import-dropzone-fileinfo">
              <span id="bulkImportFileName">No file selected</span>
              <span id="bulkImportFileSize" class="bulk-import-filesize"></span>
            </span>
          </div>
        </div>
      </div>
      <p class="form-error" id="bulkImportError" aria-live="polite"></p>
      <button type="button" class="btn btn-primary btn-block bulk-import-submit" id="bulkImportSubmit"><i data-lucide="upload"></i>Import records</button>
      <div class="form-status" id="bulkImportResult" role="status" hidden></div>
    </div>
  </div>

  <!-- Scripts -->
  <!-- Third-party libraries — pinned versions + SRI -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/lucide@1.34.0/dist/umd/lucide.js" integrity="sha384-frWQkjuw7X/yo4G2C2YnAAbkHHi7uBnMwf577mOjXDG0me9gl8Yrdyu1BhvS7WX5" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/lenis@1.3.26/dist/lenis.min.js" integrity="sha384-jqpi9VmOdhyLoLURgjCn7EpnG9BbnHW57ibIZoeaIU+erWDH3k8fQQg0xH2ySjnw" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/gsap@3.15.0/dist/gsap.min.js" integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.js" integrity="sha384-hfkuqrKeWFmnTMWN31VWyoe8xgdTADD11kgxmdpx2uyE6j5Az5uZq6u6AKYYmAOw" crossorigin="anonymous"></script>

  <!-- i18n data for JS -->
  <script>
    window.__LANG = '<?php echo $lang; ?>';
    window.__IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;
    window.__CSRF_TOKEN = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>';
    window.__LANG_DATA = <?php echo json_encode([
        'cta_clock_prefix' => i18n::t('cta_clock_prefix'),
    ], JSON_UNESCAPED_UNICODE); ?>;
  </script>

  <script src="assets/js/main.js?v=<?= filemtime(PUBLIC_PATH . '/assets/js/main.js') ?>"></script>
</body>
</html>
