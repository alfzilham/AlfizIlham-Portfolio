<section class="cta-closing section-padding" id="cta-closing">
  <div class="cta-inner">
    <p class="cta-clock" id="liveClock"><?= i18n::t('cta_clock_prefix') ?> --:--:-- --</p>
    <h1 class="cta-headline"><?= i18n::t('cta_heading') ?></h1>

    <div class="cards-wrap">
      <div class="fan-card fan-card-1" data-skill="React" style="--tx: -260px; --rot: -16deg; --z: 1;">
        <img src="assets/image/icons/fullstack/react.svg" alt="React" loading="lazy" />
      </div>
      <div class="fan-card fan-card-2" data-skill="Node.js" style="--tx: -165px; --rot: -9deg; --z: 2;">
        <img src="assets/image/icons/fullstack/nodejs.svg" alt="Node.js" loading="lazy" />
      </div>
      <div class="fan-card fan-card-3" data-skill="Python" style="--tx: -70px; --rot: -3deg; --z: 3;">
        <img src="assets/image/icons/fullstack/python.svg" alt="Python" loading="lazy" />
      </div>
      <div class="fan-card fan-card-4" data-skill="Claude" style="--tx: 28px; --rot: 4deg; --z: 4;">
        <img src="assets/image/icons/ai/claude.svg" alt="Claude" loading="lazy" />
      </div>
      <div class="fan-card fan-card-5" data-skill="JavaScript" style="--tx: 128px; --rot: 10deg; --z: 3;">
        <img src="assets/image/icons/fullstack/javascript.svg" alt="JavaScript" loading="lazy" />
      </div>
      <div class="fan-card fan-card-6" data-skill="n8n" style="--tx: 225px; --rot: 17deg; --z: 2;">
        <img src="assets/image/icons/platform/n8n.svg" alt="n8n" loading="lazy" />
      </div>
    </div>

    <p class="cta-description"><?= i18n::t('cta_description') ?></p>

    <div class="cta-row">
      <a href="#contact" class="btn-pill"><?= i18n::t('cta_primary') ?></a>
      <a href="<?= config('whatsapp') ?>" target="_blank" rel="noopener" class="btn-text-link">
        <?= i18n::t('cta_whatsapp') ?>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
      </a>
    </div>
  </div>
</section>
