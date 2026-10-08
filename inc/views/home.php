<?php $mu = fn($u) => preg_match('#^(https?:|mailto:|tel:)#', $u) ? $u : url($u); ?>
<section class="hero" id="hero"<?php if (!empty($slides[0]['image'])): ?> style="--hero-img:url('<?= e(media($slides[0]['image'])) ?>')"<?php endif; ?>>
  <div class="hero-bg" id="heroBg"></div>
  <svg class="ecg" viewBox="0 0 1600 200" preserveAspectRatio="none" aria-hidden="true"><path class="ecg-line" d="M0 110 H300 l30 -4 l22 -50 l30 120 l34 -140 l24 80 l30 -6 H760 l24 -6 l20 -30 l28 84 l30 -110 l22 60 H1100 l20 -8 l24 -34 l30 100 l30 -120 l20 70 H1600" /></svg>
  <div class="wrap hero-grid">
    <div class="hero-copy" id="slider" data-count="<?= count($slides) ?>">
      <?php foreach ($slides as $i => $s): ?>
      <div class="slide <?= $i === 0 ? 'on' : '' ?>"<?= $s['image'] ? ' data-img="' . e(media($s['image'])) . '"' : '' ?>>
        <p class="eyebrow"><?= e($s['eyebrow']) ?></p>
        <h1><?= e($s['title']) ?></h1>
        <p class="lead"><?= e($s['text']) ?></p>
        <?php if ($s['btn_label']): ?><a class="btn btn-accent" href="<?= e($mu($s['btn_url'])) ?>"><?= e($s['btn_label']) ?> <?= icon('arrow') ?></a><?php endif; ?>
        <a class="btn btn-ghost" href="<?= e(url('apply')) ?>">Apply now</a>
      </div>
      <?php endforeach; ?>
      <?php if (count($slides) > 1): ?><div class="dots" role="tablist"><?php foreach ($slides as $i => $s): ?><button class="<?= $i === 0 ? 'on' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?></div><?php endif; ?>
    </div>
    <aside class="next" aria-label="Next course">
      <?php if ($course): ?>
        <p class="next-k">Next course</p>
        <h2><?= e(date_range($course['start_date'], $course['end_date'])) ?></h2>
        <p class="next-v"><?= icon('pin') ?> <?= e($course['venue']) ?></p>
        <div class="count" data-date="<?= e($course['start_date']) ?>T08:00:00+05:30">
          <div><b data-u="d">00</b><span>days</span></div><div><b data-u="h">00</b><span>hrs</span></div><div><b data-u="m">00</b><span>min</span></div><div><b data-u="s">00</b><span>sec</span></div>
        </div>
        <?php if ($course['fee']): ?><p class="next-f">Course fee: <b><?= e($course['fee']) ?></b></p><?php endif; ?>
        <a class="btn btn-accent block" href="<?= e(url('apply')) ?>"><?= $course['status'] === 'full' ? 'Join the waiting list' : 'Reserve your seat' ?></a>
        <?php if ($course['status'] === 'full'): ?><p class="next-n">This course is fully booked.</p><?php endif; ?>
      <?php else: ?>
        <p class="next-k">Next course</p>
        <h2>Dates to be announced</h2>
        <p class="next-v">Register your interest and we will notify you first.</p>
        <a class="btn btn-accent block" href="<?= e(url('apply')) ?>">Register interest</a>
      <?php endif; ?>
    </aside>
  </div>
  <div class="wrap stats">
    <?php foreach ([1,2,3] as $n): if (!setting("hero_stat{$n}_n")) continue; ?>
      <div><b><?= e(setting("hero_stat{$n}_n")) ?></b><span><?= e(setting("hero_stat{$n}_l")) ?></span></div>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($highlights): ?>
<section class="sec">
  <div class="wrap">
    <header class="sec-h reveal"><p class="eyebrow eb">Why Excel Paces</p><h2>The final stage of MRCP (UK), prepared properly</h2></header>
    <div class="cards4">
      <?php foreach ($highlights as $h): ?>
      <article class="card reveal"><span class="ico"><?= icon($h['icon']) ?></span><h3><?= e($h['title']) ?></h3><p><?= e($h['text']) ?></p></article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($stations): ?>
<section class="sec tint">
  <div class="wrap">
    <header class="sec-h reveal"><p class="eyebrow eb">Five stations, fully covered</p><h2>Walk the PACES circuit with us</h2><p class="muted">Tap a station to see how we prepare you.</p></header>
    <div class="stations reveal" id="stations">
      <div class="st-tabs" role="tablist">
        <?php foreach ($stations as $i => $s): [$num, $name] = array_pad(explode(' · ', $s['title'], 2), 2, ''); ?>
        <button role="tab" class="<?= $i === 0 ? 'on' : '' ?>" data-i="<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><i><?= $i + 1 ?></i><span><?= e($name ?: $s['title']) ?></span></button>
        <?php endforeach; ?>
      </div>
      <div class="st-panels">
        <?php foreach ($stations as $i => $s): [$num, $name] = array_pad(explode(' · ', $s['title'], 2), 2, ''); ?>
        <div class="st-panel <?= $i === 0 ? 'on' : '' ?>" role="tabpanel">
          <span class="big"><?= icon($s['icon']) ?></span>
          <p class="eyebrow eb"><?= e($num) ?></p>
          <h3><?= e($name ?: $s['title']) ?></h3>
          <p><?= e($s['text']) ?></p>
          <a class="link" href="<?= e(url('course')) ?>">See the full course <?= icon('arrow') ?></a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (setting('endorse_quote')): ?>
<section class="sec quote-sec">
  <div class="wrap reveal">
    <blockquote class="endorse"><p>“<?= e(setting('endorse_quote')) ?>”</p>
      <footer><?php if ($p = setting('endorse_photo')): ?><img src="<?= e(media($p)) ?>" alt=""><?php endif; ?><div><b><?= e(setting('endorse_name')) ?></b><span><?= e(setting('endorse_role')) ?></span></div></footer>
    </blockquote>
  </div>
</section>
<?php endif; ?>

<?php if ($why): ?>
<section class="sec dark">
  <div class="wrap">
    <header class="sec-h reveal"><p class="eyebrow">Recognised &amp; trusted</p><h2>Built to the standard examiners expect</h2></header>
    <div class="why">
      <?php foreach ($why as $w): ?>
      <div class="why-i reveal"><span class="ico"><?= icon($w['icon']) ?></span><div><h3><?= e($w['title']) ?></h3><p><?= e($w['text']) ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($faculty): ?>
<section class="sec">
  <div class="wrap">
    <header class="sec-h reveal row-h"><div><p class="eyebrow eb">Faculty</p><h2>Taught by examiners</h2></div><a class="link" href="<?= e(url('faculty')) ?>">All faculty <?= icon('arrow') ?></a></header>
    <div class="fac-grid">
      <?php foreach ($faculty as $f): include ROOT . '/inc/views/_fac.php'; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($tests): ?>
<section class="sec tint">
  <div class="wrap">
    <header class="sec-h reveal row-h"><div><p class="eyebrow eb">Testimonials</p><h2>What attendees say</h2></div>
      <div class="car-btns"><button id="tPrev" aria-label="Previous"><?= icon('arrow') ?></button><button id="tNext" aria-label="Next"><?= icon('arrow') ?></button></div></header>
    <div class="tcar" id="tcar">
      <?php foreach ($tests as $t): ?>
      <figure class="tcard"><blockquote>“<?= e($t['quote']) ?>”</blockquote><figcaption><span class="av"><?= e(mb_substr(preg_replace('/^(Dr\.?|Prof\.?)\s*/i', '', $t['name']), 0, 1)) ?></span><div><b><?= e($t['name']) ?></b><span><?= e($t['role']) ?></span></div></figcaption></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($photos)): ?>
<section class="sec">
  <div class="wrap">
    <header class="sec-h reveal row-h"><div><p class="eyebrow eb">Gallery</p><h2>Inside the course</h2></div><a class="link" href="<?= e(url('gallery')) ?>">View gallery <?= icon('arrow') ?></a></header>
    <div class="strip">
      <?php foreach ($photos as $ph): ?><a href="<?= e(url('gallery')) ?>" class="strip-i reveal"><img src="<?= e(media($ph['file'])) ?>" alt="Excel Paces course photo" loading="lazy"></a><?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="sec cta-sec">
  <div class="wrap cta-box reveal">
    <div><h2><?= e(setting('cta_title')) ?></h2><p><?= e(setting('cta_text')) ?></p></div>
    <div class="cta-b"><a class="btn btn-accent" href="<?= e(url('apply')) ?>">Apply now <?= icon('arrow') ?></a><a class="btn btn-ghost" href="<?= e(url('contact')) ?>">Ask a question</a></div>
  </div>
</section>
<?php if (setting('map_embed')): ?>
<section class="map"><?= preg_match('#^<iframe#i', trim(setting('map_embed'))) ? preg_replace('/<iframe/i', '<iframe loading="lazy"', strip_tags(setting('map_embed'), '<iframe')) : '' ?></section>
<?php endif; ?>
