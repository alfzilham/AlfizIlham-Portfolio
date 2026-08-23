<?php
/** @var array $projects */
$carouselProjects = array_values(array_filter($projects, function ($p) {
    return $p['category'] !== 'website';
}));
?>
<section class="projects-teaser section-padding" id="project">
  <div class="container">
    <div class="project-showcase">
      <div class="project-showcase-content">
        <span class="eyebrow-pill"><?= i18n::t('projects_teaser_eyebrow') ?></span>
        <h1><?= i18n::t('projects_teaser_heading') ?></h1>
        <p class="project-showcase-subtitle"><?= i18n::t('projects_teaser_subtitle') ?></p>
        <div class="project-showcase-ctas">
          <a href="#contact" class="btn btn-primary"><?= i18n::t('projects_cta_start') ?></a>
          <a href="<?= config('whatsapp') ?>" target="_blank" rel="noopener" class="btn btn-ghost"><?= i18n::t('projects_cta_whatsapp') ?></a>
          <a href="#" class="btn btn-secondary"><?= i18n::t('projects_cta_view') ?></a>
        </div>
      </div>

      <div class="project-showcase-visual">
        <div
          class="depth-carousel"
          id="depthCarousel"
          role="group"
          aria-roledescription="carousel"
          aria-label="Project showcase"
          tabindex="0"
        >
          <div class="depth-carousel__stage">
            <?php foreach ($carouselProjects as $item): ?>
              <div class="depth-carousel__card">
                <img
                  class="depth-carousel__img"
                  src="<?= sanitize($item['image']) ?>"
                  alt="<?= sanitize($item['name']) ?>"
                  loading="lazy"
                  decoding="async"
                  draggable="false"
                />
                <span class="depth-carousel__tint"></span>
              </div>
            <?php endforeach; ?>
          </div>

          <button type="button" class="depth-carousel__arrow depth-carousel__arrow--prev" aria-label="Previous slide">
            <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
              <path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <button type="button" class="depth-carousel__arrow depth-carousel__arrow--next" aria-label="Next slide">
            <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
              <path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>

          <div class="depth-carousel__dots" role="tablist" aria-label="Slides"></div>
        </div>
        <p class="showcase-hint"><?= i18n::t('projects_hint') ?></p>
      </div>
    </div>
  </div>
</section>
