<?php $ini = mb_substr(preg_replace('/^(Dr\.?|Prof\.?)\s*/i', '', $f['name']), 0, 1); ?>
<article class="fac reveal" <?= $f['bio'] ? 'data-bio="1" tabindex="0" role="button"' : '' ?>>
  <div class="fac-ph"><?php if ($f['photo']): ?><img src="<?= e(media($f['photo'])) ?>" alt="<?= e($f['name']) ?>" loading="lazy"><?php else: ?><span><?= e($ini) ?></span><?php endif; ?></div>
  <h3><?= e($f['name']) ?></h3>
  <p class="role"><?= e($f['role']) ?></p>
  <?php if ($f['org']): ?><p class="org"><?= e($f['org']) ?></p><?php endif; ?>
  <?php if ($f['bio']): ?><div class="bio" hidden><?= clean_html($f['bio']) ?></div><span class="more">Read bio</span><?php endif; ?>
</article>
