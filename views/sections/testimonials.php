<?php
/** @var array $testimonials */
?>
<section class="testimonials section-padding" id="testimonials">
  <div class="container">
    <div class="testimonials-grid" id="testimonialsGrid">
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <div class="testimonial-stars">
            <?= str_repeat('<i class="bi bi-star-fill"></i>', (int) $t['rating']) ?>
          </div>
          <p class="testimonial-text">"<?= sanitize($t['text']) ?>"</p>
          <div class="testimonial-author">
            <img class="testimonial-avatar" src="<?= sanitize($t['avatar']) ?>" alt="<?= sanitize($t['name']) ?>" loading="lazy" />
            <div>
              <div class="testimonial-name"><?= sanitize($t['name']) ?></div>
              <div class="testimonial-role"><?= sanitize($t['role']) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
