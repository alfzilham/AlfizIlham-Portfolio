<?php
/** @var array $tools */
?>
<section class="skills section-padding" id="skills">
  <div class="container">
    <div class="section-header text-center">
      <h1><?= i18n::t('skills_heading') ?></h1>
      <p class="section-subtitle"><?= i18n::t('skills_subtitle') ?></p>
    </div>

    <!-- Icon Grid (decorative) -->
    <div class="icon-grid" id="iconGrid">
      <!-- Rendered by JS from PHP data -->
    </div>

    <!-- Filter Tabs + Search -->
    <div class="filter-bar">
      <div class="filter-tabs" id="skillTabs" role="tablist">
        <button class="filter-pill active" data-filter="all" role="tab" aria-pressed="true"><?= i18n::t('skills_filter_all') ?></button>
        <button class="filter-pill" data-filter="languages" role="tab" aria-pressed="false"><?= i18n::t('skills_filter_languages') ?></button>
        <button class="filter-pill" data-filter="frontend" role="tab" aria-pressed="false"><?= i18n::t('skills_filter_frontend') ?></button>
        <button class="filter-pill" data-filter="backend-devops" role="tab" aria-pressed="false"><?= i18n::t('skills_filter_backend') ?></button>
        <button class="filter-pill" data-filter="ai-ml" role="tab" aria-pressed="false"><?= i18n::t('skills_filter_ai') ?></button>
        <button class="filter-pill" data-filter="design" role="tab" aria-pressed="false"><?= i18n::t('skills_filter_design') ?></button>
        <button class="filter-pill" data-filter="tools-platform" role="tab" aria-pressed="false"><?= i18n::t('skills_filter_tools') ?></button>
      </div>
      <div class="search-box">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" id="skillSearch" placeholder="<?= i18n::t('skills_search_placeholder') ?>" aria-label="Search tools" />
      </div>
    </div>

    <!-- Tool Cards Grid (rendered by JS) -->
    <div class="tool-grid" id="toolGrid"></div>
    <p class="empty-state" id="skillEmptyState" hidden><?= i18n::t('skills_empty') ?></p>
  </div>
</section>

<!-- Pass tools data to JS -->
<script>
  window.__TOOLS = <?= json_encode($tools, JSON_UNESCAPED_UNICODE) ?>;
</script>
