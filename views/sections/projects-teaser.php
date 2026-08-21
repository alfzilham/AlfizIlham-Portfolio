<?php
/** @var array $counts */
?>
<section class="projects-teaser section-padding" id="projects-teaser">
  <div class="container text-center">
    <p class="eyebrow"><?= i18n::t('projects_teaser_eyebrow') ?></p>
    <h1><?= i18n::t('projects_teaser_heading') ?></h1>
    <p class="section-subtitle"><?= i18n::t('projects_teaser_subtitle') ?></p>
    <a href="#project" class="btn btn-outline"><?= i18n::t('projects_teaser_cta') ?></a>

    <!-- Category Stats -->
    <div class="project-categories">
      <div class="project-category">
        <h3><?= $counts['website'] ?> <?= i18n::t('projects_teaser_cat1_title') ?></h3>
        <p><?= i18n::t('projects_teaser_cat1_desc') ?></p>
      </div>
      <div class="project-category">
        <h3><?= $counts['design'] ?> <?= i18n::t('projects_teaser_cat2_title') ?></h3>
        <p><?= i18n::t('projects_teaser_cat2_desc') ?></p>
      </div>
      <div class="project-category">
        <h3><?= $counts['calligraphy'] ?> <?= i18n::t('projects_teaser_cat3_title') ?></h3>
        <p><?= i18n::t('projects_teaser_cat3_desc') ?></p>
      </div>
    </div>
  </div>
</section>
