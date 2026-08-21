<?php
/** @var array $emailjs */
/** @var string $whatsapp */
?>
<section class="contact section-padding" id="contact">
  <div class="container">
    <div class="contact-header">
      <h1><?= i18n::t('contact_heading') ?></h1>
      <p class="contact-subtext"><?= i18n::t('contact_subtitle') ?></p>
    </div>
    <div class="contact-grid">
      <!-- Form -->
      <form class="contact-form" id="contactForm" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label for="contactName"><?= i18n::t('contact_name_label') ?></label>
            <input type="text" id="contactName" name="from_name" placeholder="<?= i18n::t('contact_name_placeholder') ?>" required minlength="2" />
            <span class="form-error" id="nameError"></span>
          </div>
          <div class="form-group">
            <label for="contactEmail"><?= i18n::t('contact_email_label') ?></label>
            <input type="email" id="contactEmail" name="from_email" placeholder="<?= i18n::t('contact_email_placeholder') ?>" required />
            <span class="form-error" id="emailError"></span>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="contactPhone"><?= i18n::t('contact_phone_label') ?></label>
            <input type="tel" id="contactPhone" name="phone" placeholder="<?= i18n::t('contact_phone_placeholder') ?>" required />
            <span class="form-error" id="phoneError"></span>
          </div>
          <div class="form-group">
            <label for="contactService"><?= i18n::t('contact_service_label') ?></label>
            <select id="contactService" name="service" required>
              <option value="" disabled selected><?= i18n::t('contact_service_default') ?></option>
              <?php foreach (i18n::t('contact_service_options') as $opt): ?>
                <option value="<?= sanitize($opt) ?>"><?= sanitize($opt) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="form-error" id="serviceError"></span>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="contactBudget"><?= i18n::t('contact_budget_label') ?></label>
            <input type="text" id="contactBudget" name="budget" placeholder="<?= i18n::t('contact_budget_placeholder') ?>" />
          </div>
          <div class="form-group">
            <label for="contactTimeline"><?= i18n::t('contact_timeline_label') ?></label>
            <input type="text" id="contactTimeline" name="timeline" placeholder="<?= i18n::t('contact_timeline_placeholder') ?>" />
          </div>
        </div>
        <div class="form-group">
          <label for="contactMessage"><?= i18n::t('contact_message_label') ?></label>
          <textarea id="contactMessage" name="message" rows="4" placeholder="<?= i18n::t('contact_message_placeholder') ?>" required minlength="10"></textarea>
          <span class="form-error" id="messageError"></span>
        </div>
        <!-- Hidden fields -->
        <input type="hidden" name="github_url" value="<?= config('social.github') ?>" />
        <input type="hidden" name="linkedin_url" value="<?= config('social.linkedin') ?>" />
        <input type="hidden" name="whatsapp_url" value="<?= $whatsapp ?>" />
        <input type="hidden" name="instagram_url" value="<?= config('social.instagram') ?>" />
        <button type="submit" class="btn btn-primary" id="submitBtn">
          <?= i18n::t('contact_submit') ?> <i data-lucide="external-link"></i>
        </button>
        <div class="form-status" id="formStatus" hidden></div>
      </form>

      <!-- Map -->
      <div class="contact-map-card">
        <div id="contact-map"></div>
      </div>
    </div>

    <!-- Contact Info Strip -->
    <div class="contact-info-strip">
      <div class="contact-info-item">
        <i data-lucide="phone"></i>
        <div>
          <strong><?= i18n::t('contact_call_title') ?></strong>
          <span><?= config('phone') ?></span>
        </div>
      </div>
      <div class="contact-info-item">
        <i data-lucide="clock"></i>
        <div>
          <strong><?= i18n::t('contact_response_title') ?></strong>
          <span><?= i18n::t('contact_response_time') ?></span>
        </div>
      </div>
      <div class="contact-info-item">
        <i data-lucide="send"></i>
        <div>
          <strong><?= i18n::t('contact_write_title') ?></strong>
          <span><?= config('email') ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Pass EmailJS config + i18n strings to JS -->
<script>
  window.__EMAILJS = <?= json_encode($emailjs, JSON_UNESCAPED_UNICODE) ?>;
  window.__CONTACT_LANG = <?= json_encode([
      'sending' => i18n::t('contact_sending'),
      'success' => i18n::t('contact_success'),
      'error' => i18n::t('contact_error'),
      'error_name' => i18n::t('error_name'),
      'error_email' => i18n::t('error_email'),
      'error_phone' => i18n::t('error_phone'),
      'error_service' => i18n::t('error_service'),
      'error_message' => i18n::t('error_message'),
  ], JSON_UNESCAPED_UNICODE) ?>;
</script>
