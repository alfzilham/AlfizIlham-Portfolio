<section class="education" id="education">

  <div class="education-eyebrow">
    <span class="education-eyebrow-line"></span>
    <span><?php echo i18n::t('education_eyebrow'); ?></span>
    <span class="education-eyebrow-line"></span>
  </div>

  <h1 class="education-title"><?php echo i18n::t('education_heading'); ?></h1>

  <div class="education-diagram">

    <!-- Ghost numbers -->
    <span class="education-ghost-num education-ghost-num-1">01</span>
    <span class="education-ghost-num education-ghost-num-2">02</span>
    <span class="education-ghost-num education-ghost-num-3">03</span>
    <span class="education-ghost-num education-ghost-num-4">04</span>

    <!-- SVG connecting line -->
    <svg class="education-wave" viewBox="0 0 1080 320" preserveAspectRatio="none">
      <path d="M 90 240 C 220 240, 260 100, 400 90 C 520 82, 560 200, 660 190 C 760 182, 800 145, 940 140" />
    </svg>

    <!-- Nodes (icons) -->
    <div class="education-node education-node-1">
      <svg viewBox="0 0 24 24" fill="none" stroke="#0e0f12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
      </svg>
    </div>

    <div class="education-node education-node-2">
      <svg viewBox="0 0 24 24" fill="none" stroke="#0e0f12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        <line x1="8" y1="7" x2="16" y2="7"/>
        <line x1="8" y1="11" x2="14" y2="11"/>
      </svg>
    </div>

    <div class="education-node education-node-3">
      <svg viewBox="0 0 24 24" fill="none" stroke="#0e0f12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="14" width="4" height="6"/>
        <rect x="10" y="9" width="4" height="11"/>
        <rect x="16" y="4" width="4" height="16"/>
      </svg>
    </div>

    <div class="education-node education-node-4">
      <svg viewBox="0 0 24 24" fill="none" stroke="#0e0f12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
      </svg>
    </div>

    <!-- Step content -->
    <div class="education-step education-step-1">
      <h3><?php echo i18n::t('education_step1_title'); ?></h3>
      <span class="education-step-location"><?php echo i18n::t('education_step1_location'); ?></span>
      <p><?php echo i18n::t('education_step1_desc'); ?></p>
    </div>

    <div class="education-step education-step-2">
      <h3><?php echo i18n::t('education_step2_title'); ?></h3>
      <span class="education-step-location"><?php echo i18n::t('education_step2_location'); ?></span>
      <p><?php echo i18n::t('education_step2_desc'); ?></p>
    </div>

    <div class="education-step education-step-3">
      <h3><?php echo i18n::t('education_step3_title'); ?></h3>
      <span class="education-step-location"><?php echo i18n::t('education_step3_location'); ?></span>
      <p><?php echo i18n::t('education_step3_desc'); ?></p>
    </div>

    <div class="education-step education-step-4">
      <h3><?php echo i18n::t('education_step4_title'); ?></h3>
      <span class="education-step-location"><?php echo i18n::t('education_step4_location'); ?></span>
      <p><?php echo i18n::t('education_step4_desc'); ?></p>
    </div>

  </div>

</section>
