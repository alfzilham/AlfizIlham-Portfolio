<section class="chatbot" aria-label="Portfolio assistant">
  <button type="button" class="chatbot-toggle" id="chatbotToggle" aria-expanded="false" aria-controls="chatbotPanel">
    <i data-lucide="message-circle"></i><span><?= i18n::t('chat_open') ?></span>
  </button>
  <div class="chatbot-panel t-panel-slide" id="chatbotPanel" data-open="false" hidden role="dialog" aria-modal="false" aria-labelledby="chatbotTitle">
    <div class="chatbot-header">
      <div><strong id="chatbotTitle"><?= i18n::t('chat_title') ?></strong><p><?= i18n::t('chat_subtitle') ?></p></div>
      <button type="button" id="chatbotClose" aria-label="<?= i18n::t('chat_close') ?>"><i data-lucide="x"></i></button>
    </div>
    <div class="chatbot-messages" id="chatbotMessages" role="log" aria-live="polite">
      <p class="chatbot-message chatbot-message--assistant"><?= i18n::t('chat_welcome') ?></p>
    </div>
    <form class="chatbot-form" id="chatbotForm" novalidate>
      <textarea id="chatbotInput" aria-label="<?= i18n::t('chat_input_label') ?>" maxlength="1000" rows="1" placeholder="<?= i18n::t('chat_placeholder') ?>"></textarea>
      <button type="submit" aria-label="<?= i18n::t('chat_send') ?>"><i data-lucide="send"></i></button>
    </form>
  </div>
</section>
<script>
window.__CHAT_LANG = <?= json_encode(['sending' => i18n::t('chat_sending'), 'error' => i18n::t('chat_error')], JSON_UNESCAPED_UNICODE) ?>;
</script>
