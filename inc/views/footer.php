</main>
<?php
$partners = all('SELECT * FROM partners WHERE active=1 ORDER BY sort,id');
$menu2 = all('SELECT * FROM menu WHERE active=1 AND cta=0 ORDER BY sort,id');
$mu = fn($u) => preg_match('#^(https?:|mailto:|tel:)#', $u) ? $u : ($u === '/' ? url() : url($u));
?>
<?php
$logoHtml = function ($p) { $inner = $p['logo'] ? '<img src="' . e(media($p['logo'])) . '" alt="' . e($p['name']) . '" loading="lazy">' : '<span class="logo-txt">' . e($p['name']) . '</span>';
    return '<li>' . ($p['url'] ? '<a href="' . e($p['url']) . '" target="_blank" rel="noopener" title="' . e($p['name']) . '">' . $inner . '</a>' : $inner) . '</li>'; };
$fedP = array_values(array_filter($partners, fn($p) => str_contains((string)$p['url'], 'thefederation.uk')));
$grpP = array_values(array_filter($partners, fn($p) => !str_contains((string)$p['url'], 'thefederation.uk')));
?>
<section class="partners" aria-label="Recognition and group organisations">
  <div class="wrap pgrid">
    <?php if ($fedP): ?><div class="pcol pcol-fed"><p class="partners-t">Recognised by</p><ul class="logos"><?php foreach ($fedP as $p) echo $logoHtml($p); ?></ul></div><?php endif; ?>
    <div class="pcol"><p class="partners-t"><?= e(setting('partners_title')) ?></p><ul class="logos"><?php foreach ($grpP as $p) echo $logoHtml($p); ?></ul></div>
  </div>
</section>
<footer class="ftr">
  <div class="wrap ftr-grid">
    <div>
      <?php $fl = setting('logo_main') ?: (is_file(ROOT . '/assets/img/logo.png') ? 'assets/img/logo.png' : ''); ?>
      <?php if ($fl): ?><div class="ftr-logo"><img src="<?= e(media($fl)) ?>" alt="<?= e(setting('site_name')) ?>"></div><small class="ftr-at">at KIMSHEALTH</small>
      <?php else: ?><div class="ftr-brand"><b>Excel<i>Paces</i></b><small>at KIMSHEALTH</small></div><?php endif; ?>
      <p><?= e(setting('footer_text')) ?></p>
    </div>
    <div><h4>Explore</h4><ul><?php foreach ($menu2 as $m): ?><li><a href="<?= e($mu($m['url'])) ?>"><?= e($m['label']) ?></a></li><?php endforeach; ?><li><a href="<?= e(url('venue')) ?>">Venue &amp; Resources</a></li><li><a href="<?= e(url('downloads')) ?>">Downloads</a></li></ul></div>
    <div><h4>Contact</h4>
      <ul class="ci">
        <?php if ($a = setting('address')): ?><li><?= icon('pin') ?><span><?= nl2br(e($a)) ?></span></li><?php endif; ?>
        <?php if ($t = setting('phone')): ?><li><?= icon('phone') ?><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', $t)) ?>"><?= e($t) ?></a></li><?php endif; ?>
        <?php if ($t = setting('email')): ?><li><?= icon('mail') ?><a href="mailto:<?= e($t) ?>"><?= e($t) ?></a></li><?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="wrap ftr-bar"><span><?= e(setting('copyright')) ?></span><span>Designed &amp; developed by KIMSHEALTH</span></div>
</footer>
<a class="sticky-apply" href="<?= e(url('apply')) ?>">Apply now <?= icon('arrow') ?></a>
<script src="<?= e(url('assets/js/site.js')) ?>?v=1" defer></script>
</body></html>
