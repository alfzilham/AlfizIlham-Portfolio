<?php
/** @var array $services */
?>
<section class="services section-padding" id="service">
  <div class="container">
    <h1 class="services-heading"><?= i18n::t('services_heading') ?></h1>
    <div class="divider"></div>
    <div class="services-top">
      <p class="services-tagline"><?= i18n::t('services_tagline') ?></p>
      <div class="services-top-right">
        <a href="#contact" class="btn btn-outline"><?= i18n::t('services_cta') ?></a>
        <p class="services-caption"><?= i18n::t('services_caption') ?></p>
      </div>
    </div>
    <div class="divider"></div>
    <div class="services-grid" id="servicesGrid"></div>
  </div>
</section>

<!-- Pass services data to JS -->
<script>
  window.__SERVICES = <?= json_encode($services, JSON_UNESCAPED_UNICODE) ?>;
</script>
