<section class="hero" id="hero">
  <!-- Aurora blobs -->
  <div class="aurora-blob aurora-blob-left" aria-hidden="true"></div>
  <div class="aurora-blob aurora-blob-right" aria-hidden="true"></div>

  <!-- Photo -->
  <div class="hero-magnet">
    <div class="hero-photo">
      <img src="assets/image/avatars/hero-avatar.webp" alt="Alfiz Ilham mascot" class="hero-avatar" />
    </div>
  </div>

  <!-- Floating icons -->
  <div class="floating-icon hero-icon-1"><img src="assets/image/icons/ai-productivity/claude.svg" alt="Claude" /></div>
  <div class="floating-icon hero-icon-2"><img src="assets/image/icons/ai-productivity/obsidian.svg" alt="Obsidian" /></div>
  <div class="floating-icon hero-icon-3"><img src="assets/image/icons/fundamentals/react.svg" alt="React" /></div>

  <!-- Foreground content -->
  <div class="container hero-content">
    <p class="hero-preheading reveal-item" data-delay="0"><?= i18n::t('hero_preheading') ?></p>
    <h1 class="hero-name reveal-item" data-delay="100"><?= i18n::t('hero_name_line1') ?><br /><?= i18n::t('hero_name_line2') ?></h1>
    <div class="hero-tagline-row reveal-item" data-delay="250">
      <p class="hero-tagline"><?= i18n::t('hero_tagline') ?></p>
      <span class="hero-badge"><span class="hero-badge-dot"></span><?= i18n::t('hero_badge') ?></span>
    </div>
    <div class="hero-ctas reveal-item" data-delay="350">
      <a href="#contact" class="btn btn-primary"><?= i18n::t('hero_cta_primary') ?></a>
      <a href="#project" class="btn btn-ghost"><?= i18n::t('hero_cta_secondary') ?> <i data-lucide="arrow-right"></i></a>
    </div>
  </div>

  <!-- Info card (stats) -->
  <div class="hero-stats-card reveal-item" data-delay="500">
    <div class="hero-stats-avatars">
      <img src="assets/image/people/atasidqi.webp" alt="" />
      <img src="assets/image/people/niswatulchaira.webp" alt="" />
      <img src="assets/image/people/imamfuadi.webp" alt="" />
    </div>
    <div class="hero-stats-info">
      <strong><?= i18n::t('hero_stats_title') ?></strong>
      <ul>
        <li><?= i18n::t('hero_stats_item1') ?></li>
        <li><?= i18n::t('hero_stats_item2') ?></li>
      </ul>
    </div>
  </div>
</section>
