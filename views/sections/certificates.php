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
    <div class="certificates-grid">
      <div class="certificates-wheel-wrap">
        <div id="certOptionWheel" class="option-wheel" data-lenis-prevent role="listbox" aria-label="Certificate list" tabindex="0"></div>
      </div>
      <div class="cert-carousel-wrap">
        <div id="certDepthCarousel" class="depth-carousel" data-lenis-prevent role="group" aria-roledescription="carousel" aria-label="Certificate carousel" tabindex="0"></div>
      </div>
    </div>
  </div>
</section>

<!-- Pass certificates data to JS -->
<script>
  window.__CERTIFICATES = <?= json_encode($certificates, JSON_UNESCAPED_UNICODE) ?>;
</script>
