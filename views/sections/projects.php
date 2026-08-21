<?php
/** @var array $projects */
?>
<section class="projects-grid-section section-padding" id="project">
  <div class="container">
    <!-- Filter Tabs -->
    <div class="filter-bar filter-bar-center">
      <div class="filter-tabs" id="projectTabs" role="tablist">
        <button class="filter-pill active" data-filter="all" role="tab" aria-pressed="true"><?= i18n::t('projects_filter_all') ?></button>
        <button class="filter-pill" data-filter="website" role="tab" aria-pressed="false"><?= i18n::t('projects_filter_website') ?></button>
        <button class="filter-pill" data-filter="design" role="tab" aria-pressed="false"><?= i18n::t('projects_filter_design') ?></button>
        <button class="filter-pill" data-filter="calligraphy" role="tab" aria-pressed="false"><?= i18n::t('projects_filter_calligraphy') ?></button>
      </div>
    </div>

    <!-- Projects Grid (rendered by JS) -->
    <div class="project-grid" id="projectGrid"></div>

    <div class="text-center" style="margin-top:var(--space-6);">
      <button class="btn btn-outline" id="viewMoreBtn"><?= i18n::t('projects_view_more') ?></button>
    </div>
  </div>
</section>

<!-- Pass projects data to JS -->
<script>
  window.__PROJECTS = <?= json_encode($projects, JSON_UNESCAPED_UNICODE) ?>;
</script>
