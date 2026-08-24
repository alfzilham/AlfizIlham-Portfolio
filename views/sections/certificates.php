<?php
/** @var array $certificates */
?>
<section class="certificates section-padding" id="certificate">
  <div class="container">
    <p class="eyebrow"><?= i18n::t('certificates_eyebrow') ?></p>
    <h1 class="certificates-heading"><?= i18n::t('certificates_heading') ?></h1>
    <p class="certificates-subtitle"><?= i18n::t('certificates_subtitle') ?></p>
  </div>
  <div class="certificates-grid-wrap">
    <div class="certificates-grid" id="certGrid"></div>
    <div class="cert-load-more" id="certLoadMoreWrap" hidden>
      <button type="button" id="certLoadMoreBtn" class="btn btn-outline"><?= i18n::t('projects_load_more') ?></button>
    </div>
  </div>
</section>

<!-- Pass certificates data to JS -->
<script>
  window.__CERTIFICATES = <?= json_encode($certificates, JSON_UNESCAPED_UNICODE) ?>;
</script>
