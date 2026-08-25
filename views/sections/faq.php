<?php
/** @var array $faqs */
/** @var array $faqCategories */
?>
<section class="faq section-padding" id="faq">
  <div class="container">
    <p class="eyebrow">&mdash; <?= i18n::t('faq_eyebrow') ?></p>
    <h1><?= i18n::t('faq_heading') ?></h1>
    <div class="faq-layout">
      <!-- Sidebar -->
      <aside class="faq-sidebar" id="faqSidebar">
        <button class="faq-category active" data-category="all">&mdash; <?= i18n::t('faq_all') ?></button>
        <button class="faq-category" data-category="general">&bull; <?= i18n::t('faq_general') ?></button>
        <button class="faq-category" data-category="services">&bull; <?= i18n::t('faq_services') ?></button>
        <button class="faq-category" data-category="pricing">&bull; <?= i18n::t('faq_pricing') ?></button>
        <button class="faq-category" data-category="process">&bull; <?= i18n::t('faq_process') ?></button>
        <button class="faq-category" data-category="contact">&bull; <?= i18n::t('faq_contact') ?></button>
      </aside>
      <!-- Accordion Panel (rendered by JS) -->
      <div class="faq-panel" id="faqPanel" data-lenis-prevent></div>
    </div>
  </div>
</section>

<!-- Pass FAQ data to JS -->
<script>
  window.__FAQS = <?= json_encode($faqs, JSON_UNESCAPED_UNICODE) ?>;
</script>
