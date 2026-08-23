<?php
/** @var array $projects */
/** @var array $showcase */

// Skip cards whose image file is missing (prevents broken cards + 404s)
$showcase = array_values(array_filter($showcase, function ($c) {
    return !empty($c['image']) && is_file(PUBLIC_PATH . '/' . ltrim($c['image'], '/'));
}));

$galleryItems = array_values(array_map(function ($p) {
    return ['image' => $p['image'], 'text' => $p['name']];
}, array_filter($projects, function ($p) {
    return $p['category'] !== 'website';
})));
$chromaPalette = [
    ['border' => '#4F46E5', 'gradient' => 'linear-gradient(145deg, #4F46E5, #000)'],
    ['border' => '#10B981', 'gradient' => 'linear-gradient(210deg, #10B981, #000)'],
    ['border' => '#F59E0B', 'gradient' => 'linear-gradient(165deg, #F59E0B, #000)'],
    ['border' => '#EF4444', 'gradient' => 'linear-gradient(195deg, #EF4444, #000)'],
    ['border' => '#8B5CF6', 'gradient' => 'linear-gradient(225deg, #8B5CF6, #000)'],
    ['border' => '#06B6D4', 'gradient' => 'linear-gradient(135deg, #06B6D4, #000)'],
];
?>
<section class="projects-teaser section-padding" id="project">
  <div class="container text-center">
    <p class="eyebrow"><?= i18n::t('projects_teaser_eyebrow') ?></p>
    <h1><?= i18n::t('projects_teaser_heading') ?></h1>
    <p class="section-subtitle"><?= i18n::t('projects_teaser_subtitle') ?></p>
  </div>

  <!-- ChromaGrid Showcase (admin-curated) -->
  <div class="chroma-grid-wrap" id="chromaGridWrap" <?= $showcase ? '' : 'hidden' ?>>
    <div class="chroma-grid" id="chromaGrid">
      <?php foreach ($showcase as $i => $card): ?>
        <?php $palette = $chromaPalette[$i % count($chromaPalette)]; ?>
        <article
          class="chroma-card"
          data-id="<?= (int) $card['id'] ?>"
          data-title="<?= sanitize($card['title']) ?>"
          data-description="<?= sanitize($card['description']) ?>"
          data-image="<?= sanitize($card['image']) ?>"
          data-link="<?= sanitize($card['link'] ?? '') ?>"
          style="--card-border: <?= $palette['border'] ?>; --card-gradient: <?= $palette['gradient'] ?>"
        >
          <button type="button" class="chroma-menu-btn" aria-label="<?= i18n::t('editor_card_menu') ?>">
            <i data-lucide="more-vertical"></i>
          </button>
          <div class="chroma-menu">
            <button type="button" data-action="edit"><?= i18n::t('editor_edit') ?></button>
            <button type="button" data-action="delete" class="danger"><?= i18n::t('editor_delete') ?></button>
          </div>
          <div class="chroma-img-wrapper">
            <img src="<?= sanitize($card['image']) ?>" alt="<?= sanitize($card['title']) ?>" loading="lazy" />
          </div>
          <footer class="chroma-info">
            <h3 class="name"><?= sanitize($card['title']) ?></h3>
            <p class="role"><?= sanitize($card['description']) ?></p>
          </footer>
        </article>
      <?php endforeach; ?>
      <div class="chroma-overlay"></div>
      <div class="chroma-fade"></div>
    </div>

    <!-- Load more (public, when cards > 6) -->
    <div class="chroma-load-more" id="chromaLoadMoreWrap" hidden>
      <button type="button" id="chromaLoadMoreBtn" class="btn btn-outline"><?= i18n::t('projects_load_more') ?></button>
    </div>
  </div>

  <!-- Stats strip -->
  <div class="container text-center showcase-stats">
    <div class="showcase-stat">
      <span class="stat-value"><?= count($showcase) ?></span>
      <span class="stat-label"><?= i18n::t('stats_featured_label') ?></span>
    </div>
    <p class="showcase-strip-sentence">
      <?= i18n::t('strip_sentence') ?>
      <i data-lucide="arrow-down"></i>
    </p>
    <div class="showcase-stat">
      <span class="stat-value"><?= count($galleryItems) ?></span>
      <span class="stat-label"><?= i18n::t('stats_gallery_label') ?></span>
    </div>
  </div>

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
</section>

<!-- Pass gallery items to JS -->
<script>
  window.__GALLERY_ITEMS = <?= json_encode($galleryItems, JSON_UNESCAPED_SLASHES) ?>;
</script>
