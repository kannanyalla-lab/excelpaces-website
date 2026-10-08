<section class="phead"><div class="wrap"><p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / Gallery</p><h1><?= e($page['title']) ?></h1><p class="lead"><?= e($page['subtitle']) ?></p></div></section>
<section class="sec"><div class="wrap">
<?php if (!$photos): ?><p class="muted">Photos will appear here soon.</p><?php endif; ?>
<div class="gal"><?php foreach ($photos as $p): ?><a href="<?= e(media($p['file'])) ?>" class="gal-i reveal" data-lb="<?= e(str_contains($p['title'], ' ') ? $p['title'] : '') ?>"><img src="<?= e(media($p['file'])) ?>" alt="<?= e(str_contains($p['title'], ' ') ? $p['title'] : 'Excel Paces course photo') ?>" loading="lazy"></a><?php endforeach; ?></div>
</div></section>
