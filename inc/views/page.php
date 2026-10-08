<section class="phead">
  <div class="wrap"><p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / <?= e($page['title']) ?></p><h1><?= e($page['title']) ?></h1><?php if ($page['subtitle']): ?><p class="lead"><?= e($page['subtitle']) ?></p><?php endif; ?></div>
</section>
<section class="sec"><div class="wrap narrow">
  <?php if (!empty($page['image'])): ?><img class="pimg" src="<?= e(media($page['image'])) ?>" alt=""><?php endif; ?>
  <article class="prose"><?= clean_html($page['body']) ?></article>
  <?php if (in_array($page['slug'] ?? '', ['about','course','merit','venue'], true)): ?><p style="margin-top:36px"><a class="btn btn-accent" href="<?= e(url('apply')) ?>">Apply now <?= icon('arrow') ?></a></p><?php endif; ?>
</div></section>
