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
        <!-- Row 1: Name | Email -->
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

        <!-- Row 2: Phone Number | Service -->
        <div class="form-row">
          <div class="form-group">
            <label><?= i18n::t('contact_phone_label') ?></label>
            <div class="phone-input-group">
              <div class="custom-dropdown" id="countryCodeDropdown" data-type="country" data-lenis-prevent>
                <button type="button" class="dropdown-trigger" tabindex="-1">
                  <span class="dropdown-value" data-field="country_code">🇮🇩 +62</span>
                  <svg class="dropdown-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="dropdown-popup" hidden>
                  <input type="text" class="dropdown-search" placeholder="<?= i18n::t('contact_search_placeholder') ?>" />
                  <div class="dropdown-items" id="countryCodeItems"></div>
                </div>
              </div>
              <input type="hidden" name="country_code" id="countryCodeValue" value="+62" />
              <input type="tel" id="contactPhone" name="phone" placeholder="852 1389 6460" inputmode="numeric" required />
            </div>
            <span class="form-error" id="phoneError"></span>
          </div>
          <div class="form-group">
            <label><?= i18n::t('contact_service_label') ?></label>
            <div class="custom-dropdown" id="serviceDropdown" data-type="service" data-lenis-prevent>
              <button type="button" class="dropdown-trigger" tabindex="-1">
                <span class="dropdown-value" data-field="service"><?= i18n::t('contact_service_default') ?></span>
                <svg class="dropdown-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="dropdown-popup" hidden>
                <input type="text" class="dropdown-search" placeholder="<?= i18n::t('contact_search_placeholder') ?>" />
                <div class="dropdown-items" id="serviceItems"></div>
              </div>
            </div>
            <input type="hidden" name="service" id="serviceValue" value="" required />
            <span class="form-error" id="serviceError"></span>
          </div>
        </div>

        <!-- Row 3: Budget | Timeline -->
        <div class="form-row">
          <div class="form-group">
            <label><?= i18n::t('contact_budget_label') ?></label>
            <div class="budget-input-group">
              <input type="number" id="contactBudget" name="budget" placeholder="0" inputmode="numeric" min="0" />
              <div class="custom-dropdown" id="currencyDropdown" data-type="currency" data-lenis-prevent>
                <button type="button" class="dropdown-trigger" tabindex="-1">
                  <span class="dropdown-value" data-field="currency">USD</span>
                  <svg class="dropdown-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="dropdown-popup" hidden>
                  <input type="text" class="dropdown-search" placeholder="<?= i18n::t('contact_search_placeholder') ?>" />
                  <div class="dropdown-items" id="currencyItems"></div>
                </div>
              </div>
              <input type="hidden" name="currency" id="currencyValue" value="USD" />
            </div>
          </div>
          <div class="form-group">
            <label for="contactTimeline"><?= i18n::t('contact_timeline_label') ?></label>
            <input type="text" id="contactTimeline" name="timeline" placeholder="<?= i18n::t('contact_timeline_placeholder') ?>" />
          </div>
        </div>

        <!-- Row 4: Message (full width) -->
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
