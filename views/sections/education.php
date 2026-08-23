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

    <!-- Step content -->
    <div class="education-step education-step-1">
      <h3><?php echo i18n::t('education_step1_title'); ?> <span class="education-step-location">(<?php echo i18n::t('education_step1_location'); ?>)</span></h3>
      <p><?php echo i18n::t('education_step1_desc'); ?></p>
    </div>

    <div class="education-step education-step-2">
      <h3><?php echo i18n::t('education_step2_title'); ?> <span class="education-step-location">(<?php echo i18n::t('education_step2_location'); ?>)</span></h3>
      <p><?php echo i18n::t('education_step2_desc'); ?></p>
    </div>

    <div class="education-step education-step-3">
      <h3><?php echo i18n::t('education_step3_title'); ?> <span class="education-step-location">(<?php echo i18n::t('education_step3_location'); ?>)</span></h3>
      <p><?php echo i18n::t('education_step3_desc'); ?></p>
    </div>

    <div class="education-step education-step-4">
      <h3><?php echo i18n::t('education_step4_title'); ?> <span class="education-step-location">(<?php echo i18n::t('education_step4_location'); ?>)</span></h3>
      <p><?php echo i18n::t('education_step4_desc'); ?></p>
    </div>

  </div>

</section>
