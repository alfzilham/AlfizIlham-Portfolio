<?php
/** @var array $projects */
$galleryItems = array_values(array_map(function ($p) {
    return ['image' => $p['image'], 'text' => $p['name']];
}, array_filter($projects, function ($p) {
    return $p['category'] !== 'website';
})));
?>
<section class="projects-teaser section-padding" id="project">
  <div class="container">
    <p class="eyebrow"><?= i18n::t('projects_teaser_eyebrow') ?></p>
    <h1><?= i18n::t('projects_teaser_heading') ?></h1>
    <p class="section-subtitle"><?= i18n::t('projects_teaser_subtitle') ?></p>

    <!-- Circular WebGL Gallery -->
    <div class="circular-gallery-wrap">
      <div
        id="circularGallery"
        class="circular-gallery"
        tabindex="0"
        role="region"
        aria-label="Circular image gallery. Use Left and Right Arrow keys to navigate."
      ></div>
    </div>
  </div>
</section>

<!-- Pass gallery items to JS -->
<script>
  window.__GALLERY_ITEMS = <?= json_encode($galleryItems, JSON_UNESCAPED_SLASHES) ?>;
</script>
