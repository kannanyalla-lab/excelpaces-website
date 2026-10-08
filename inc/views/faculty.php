<section class="phead"><div class="wrap"><p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / Faculty</p><h1><?= e($page['title']) ?></h1><p class="lead"><?= e($page['subtitle']) ?></p></div></section>
<section class="sec"><div class="wrap">
  <div class="prose narrow-l"><?= clean_html($page['body']) ?></div>
  <div class="fac-grid" style="margin-top:34px"><?php foreach ($faculty as $f): include ROOT . '/inc/views/_fac.php'; endforeach; ?></div>
</div></section>
