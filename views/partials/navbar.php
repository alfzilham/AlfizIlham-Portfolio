<?php
/**
 * Navbar Partial
 * @var string $lang
 * @var array  $social
 */
?>
<nav class="navbar" id="navbar">
  <div class="container navbar-inner">
    <a href="#" class="navbar-logo">Alfiz.</a>

    <ul class="navbar-links" id="navLinks">
      <li><a href="#about"><?= i18n::t('nav_about') ?></a></li>
      <li><a href="#skills"><?= i18n::t('nav_skills') ?></a></li>
      <li><a href="#project"><?= i18n::t('nav_projects') ?></a></li>
      <li><a href="#service"><?= i18n::t('nav_services') ?></a></li>
      <li><a href="#gallery"><?= i18n::t('nav_gallery') ?></a></li>
      <li><a href="#faq"><?= i18n::t('nav_faq') ?></a></li>
      <li><a href="#contact"><?= i18n::t('nav_contact') ?></a></li>
    </ul>

    <div class="navbar-right">
      <div class="lang-toggle" id="langToggle">
        <button class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" data-lang="en" onclick="switchLang('en')">EN</button>
        <button class="lang-btn <?= $lang === 'id' ? 'active' : '' ?>" data-lang="id" onclick="switchLang('id')">ID</button>
      </div>
      <div class="navbar-social">
        <a href="<?= $social['instagram'] ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="<?= $social['linkedin'] ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
        <a href="<?= $social['github'] ?>" target="_blank" rel="noopener" aria-label="GitHub"><i class="bi bi-github"></i></a>
      </div>
      <button class="navbar-hamburger" id="hamburger" aria-label="Toggle menu">
        <i data-lucide="menu"></i>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu" id="mobileMenu">
  <div class="mobile-menu-header">
    <a href="#" class="navbar-logo">Alfiz.</a>
    <button class="mobile-menu-close" id="mobileClose" aria-label="Close menu">
      <i data-lucide="x"></i>
    </button>
  </div>
  <ul class="mobile-menu-links">
    <li><a href="#about"><?= i18n::t('nav_about') ?></a></li>
    <li><a href="#skills"><?= i18n::t('nav_skills') ?></a></li>
    <li><a href="#project"><?= i18n::t('nav_projects') ?></a></li>
    <li><a href="#service"><?= i18n::t('nav_services') ?></a></li>
    <li><a href="#gallery"><?= i18n::t('nav_gallery') ?></a></li>
    <li><a href="#faq"><?= i18n::t('nav_faq') ?></a></li>
    <li><a href="#contact"><?= i18n::t('nav_contact') ?></a></li>
  </ul>
  <div class="mobile-menu-lang">
    <button class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" data-lang="en" onclick="switchLang('en')">EN</button>
    <button class="lang-btn <?= $lang === 'id' ? 'active' : '' ?>" data-lang="id" onclick="switchLang('id')">ID</button>
  </div>
  <div class="mobile-menu-social">
    <a href="<?= $social['instagram'] ?>" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a>
    <a href="<?= $social['linkedin'] ?>" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a>
    <a href="<?= $social['github'] ?>" target="_blank" rel="noopener"><i class="bi bi-github"></i></a>
  </div>
</div>

<script>
function switchLang(lang) {
  window.location.href = '?lang=' + lang;
}
</script>
