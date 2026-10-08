<section class="phead"><div class="wrap"><p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / Contact</p><h1><?= e($page['title']) ?></h1><p class="lead"><?= e($page['subtitle']) ?></p></div></section>
<section class="sec"><div class="wrap two">
  <div>
    <div class="prose"><?= clean_html($page['body']) ?></div>
    <ul class="ci big">
      <?php if ($a = setting('address')): ?><li><?= icon('pin') ?><span><?= nl2br(e($a)) ?></span></li><?php endif; ?>
      <?php foreach (['phone','phone2'] as $k) if ($t = setting($k)): ?><li><?= icon('phone') ?><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', $t)) ?>"><?= e($t) ?></a></li><?php endif; ?>
      <?php if ($t = setting('email')): ?><li><?= icon('mail') ?><a href="mailto:<?= e($t) ?>"><?= e($t) ?></a></li><?php endif; ?>
    </ul>
  </div>
  <div class="formcard">
    <?php if ($sent): ?><div class="notice ok"><b>Thank you.</b> Your message has been received and our team will reply soon.</div>
    <?php else: ?>
    <?php foreach ($errs as $x): ?><div class="notice err"><?= e($x) ?></div><?php endforeach; ?>
    <form method="post" class="form"><?= csrf_field() ?>
      <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
      <label>Full name<input name="name" required value="<?= e($old['name'] ?? '') ?>"></label>
      <div class="g2"><label>E-mail<input type="email" name="email" required value="<?= e($old['email'] ?? '') ?>"></label><label>Phone<input name="phone" value="<?= e($old['phone'] ?? '') ?>"></label></div>
      <label>Subject<input name="subject" value="<?= e($old['subject'] ?? '') ?>"></label>
      <label>Message<textarea name="body" rows="5" required><?= e($old['body'] ?? '') ?></textarea></label>
      <button class="btn btn-accent">Send message <?= icon('arrow') ?></button>
    </form>
    <?php endif; ?>
  </div>
</div></section>
