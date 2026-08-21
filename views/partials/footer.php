<?php
/**
 * Footer Partial
 * @var array $social
 * @var int   $visitorCount
 */
?>
<footer class="site-footer">
  <!-- Giant Wordmark -->
  <div class="footer-wordmark-wrapper">
    <div class="footer-wordmark">alfzilham</div>
  </div>

  <!-- Dark Footer Panel -->
  <div class="footer-dark">
    <div class="container footer-dark-inner">
      <!-- Col 1: CTA + Social -->
      <div class="footer-col">
        <h3><?= i18n::t('footer_heading') ?></h3>
        <a href="#contact" class="btn btn-outline-light"><?= i18n::t('footer_get_started') ?></a>
        <div class="footer-social">
          <a href="<?= $social['discord'] ?>" target="_blank" rel="noopener" aria-label="Discord"><i class="bi bi-discord"></i></a>
          <a href="<?= $social['threads'] ?>" target="_blank" rel="noopener" aria-label="Threads"><i class="bi bi-threads"></i></a>
          <a href="<?= $social['tiktok'] ?>" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          <a href="<?= $social['youtube'] ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          <a href="<?= $social['facebook'] ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="<?= $social['pinterest'] ?>" target="_blank" rel="noopener" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
          <a href="<?= config('whatsapp') ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
          <a href="<?= $social['lynkid'] ?>" target="_blank" rel="noopener" aria-label="LynkID"><i class="bi bi-link-45deg"></i></a>
        </div>
      </div>

      <!-- Col 2: Navigate -->
      <div class="footer-col">
        <h4><?= i18n::t('footer_navigate') ?></h4>
        <ul>
          <li><a href="#about"><?= i18n::t('nav_about') ?></a></li>
          <li><a href="#skills"><?= i18n::t('nav_skills') ?></a></li>
          <li><a href="#project"><?= i18n::t('nav_projects') ?></a></li>
          <li><a href="#service"><?= i18n::t('nav_services') ?></a></li>
          <li><a href="#gallery"><?= i18n::t('nav_gallery') ?></a></li>
          <li><a href="#faq"><?= i18n::t('nav_faq') ?></a></li>
        </ul>
      </div>

      <!-- Col 3: Services -->
      <div class="footer-col">
        <h4><?= i18n::t('footer_services') ?></h4>
        <ul>
          <li><a href="#service"><?= i18n::t('contact_service_options')[0] ?></a></li>
          <li><a href="#service"><?= i18n::t('contact_service_options')[1] ?></a></li>
          <li><a href="#service"><?= i18n::t('contact_service_options')[2] ?></a></li>
          <li><a href="#service"><?= i18n::t('contact_service_options')[3] ?></a></li>
        </ul>
      </div>

      <!-- Col 4: Visitors -->
      <div class="footer-col">
        <h4><?= i18n::t('footer_visitors') ?></h4>
        <div class="visitor-widget">
          <div class="visitor-widget-top">
            <span class="visitor-live-dot"></span>
            <span class="visitor-live-label"><?= i18n::t('footer_live') ?></span>
            <span class="visitor-count-text" id="visitorUnique"><?= $visitorCount ?> <?= i18n::t('footer_unique_visitors') ?></span>
          </div>
          <div class="visitor-widget-map">
            <svg viewBox="0 0 360 180" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M60 60 Q80 40 100 55 T140 50 T180 60 T220 45 T260 55 T300 50" stroke="#333" stroke-width="1" fill="none" opacity="0.3"/>
              <path d="M40 90 Q80 70 120 85 T200 75 T280 85 T340 80" stroke="#333" stroke-width="1" fill="none" opacity="0.3"/>
              <path d="M80 120 Q120 100 160 115 T240 105 T320 115" stroke="#333" stroke-width="1" fill="none" opacity="0.3"/>
              <circle cx="200" cy="85" r="2" fill="#22c55e"/>
            </svg>
          </div>
          <div class="visitor-widget-bottom">
            <span><?= i18n::t('footer_country') ?></span>
            <span class="visitor-badge" id="visitorCount"><?= $visitorCount ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="container footer-bottom">
      <span><?= i18n::t('footer_copyright') ?></span>
      <span><?= i18n::t('footer_rights') ?></span>
      <div class="footer-legal">
        <a href="#"><?= i18n::t('footer_privacy') ?></a>
        <span>&middot;</span>
        <a href="#"><?= i18n::t('footer_terms') ?></a>
      </div>
    </div>
  </div>
</footer>
