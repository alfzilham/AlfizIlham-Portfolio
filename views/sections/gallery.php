<?php
/** @var array $gallery */
?>
<section class="gallery section-padding" id="gallery">
  <div class="container">
    <div class="gallery-masonry" id="galleryGrid">
      <?php foreach ($gallery as $item): ?>
        <div class="gallery-item" data-desc="<?= sanitize($item['description']) ?>">
          <img src="<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['description']) ?>" width="600" height="400" loading="eager" />
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="gallery-tooltip" id="galleryTooltip" hidden></div>
</section>
