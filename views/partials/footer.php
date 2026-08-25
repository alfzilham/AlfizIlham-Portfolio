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
          <a href="<?= $social['upwork'] ?>" target="_blank" rel="noopener" aria-label="UpWork"><i class="bi bi-briefcase-fill"></i></a>
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
          <?php foreach (i18n::t('contact_service_options') as $opt): ?>
            <li><a href="#service"><?= sanitize($opt) ?></a></li>
          <?php endforeach; ?>
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
          <div class="visitor-widget-chart">
            <canvas id="visitorChart"></canvas>
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
        <a href="privacy.php"><?= i18n::t('footer_privacy') ?></a>
        <span>&middot;</span>
        <a href="terms.php"><?= i18n::t('footer_terms') ?></a>
      </div>
    </div>
  </div>
</footer>
