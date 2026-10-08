<section class="phead"><div class="wrap"><p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / Testimonials</p><h1><?= e($page['title']) ?></h1><p class="lead"><?= e($page['subtitle']) ?></p></div></section>
<section class="sec"><div class="wrap"><div class="masonry">
<?php foreach ($tests as $t): ?><figure class="tcard reveal"><blockquote>“<?= nl2br(e($t['quote'])) ?>”</blockquote><figcaption><span class="av"><?= e(mb_substr(preg_replace('/^(Dr\.?|Prof\.?)\s*/i', '', $t['name']), 0, 1)) ?></span><div><b><?= e($t['name']) ?></b><span><?= e($t['role']) ?></span></div></figcaption></figure><?php endforeach; ?>
</div></div></section>
<section class="sec tint" id="share"><div class="wrap tsub">
  <header class="sec-h"><p class="eyebrow eb">Attended the course?</p><h2>Share your experience</h2><p class="muted">Your testimonial is reviewed by our team before it appears on this page. Your e-mail address is never shown.</p></header>
  <div class="formcard">
    <?php if ($sent): ?><div class="notice ok"><b>Thank you.</b> Your testimonial has been received and will appear here once approved.</div>
    <?php else: ?>
    <?php foreach ($errs as $x): ?><div class="notice err"><?= e($x) ?></div><?php endforeach; ?>
    <form method="post" class="form" action="<?= e(url('testimonials')) ?>#share"><?= csrf_field() ?>
      <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="g2"><label>Your name<input name="name" required value="<?= e($old['name'] ?? '') ?>"></label><label>Designation / course batch<input name="role" placeholder="e.g. Registrar, Oct 2025 batch" value="<?= e($old['role'] ?? '') ?>"></label></div>
      <label>E-mail (kept private)<input type="email" name="email" required value="<?= e($old['email'] ?? '') ?>"></label>
      <label>Your testimonial<textarea name="quote" rows="5" required maxlength="1200"><?= e($old['quote'] ?? '') ?></textarea></label>
      <button class="btn btn-accent">Submit testimonial <?= icon('arrow') ?></button>
    </form>
    <?php endif; ?>
  </div>
</div></section>
