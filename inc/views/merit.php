<section class="phead mhead">
  <div class="wrap mhead-in">
    <div>
      <p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / <?= e($page['title']) ?></p>
      <h1><?= e($page['title']) ?></h1>
      <?php if ($page['subtitle']): ?><p class="lead"><?= e($page['subtitle']) ?></p><?php endif; ?>
    </div>
    <ul class="mstats" aria-label="Course at a glance">
      <li><b>4</b><span>candidates per teacher</span></li>
      <li><b>5</b><span>PACES stations covered</span></li>
      <li><b>1</b><span>realistic mock exam</span></li>
      <li><b>0</b><span>wait for feedback: same day</span></li>
    </ul>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="mintro reveal<?= empty($page['image']) ? ' noimg' : '' ?>">
      <div class="prose"><?= clean_html($page['body']) ?></div>
      <?php if (!empty($page['image'])): ?><img class="mimg" src="<?= e(media($page['image'])) ?>" alt="Excel Paces course at KIMSHEALTH" loading="lazy"><?php endif; ?>
    </div>
    <div class="mgrid">
      <?php foreach ($merits as $i => $m): ?>
      <article class="mcard reveal">
        <span class="mnum"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
        <span class="ico"><?= icon($m['icon'] ?: 'check') ?></span>
        <h3><?= e($m['title']) ?></h3>
        <p><?= e($m['text']) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="sec tint">
  <div class="wrap">
    <header class="sec-h reveal"><p class="eyebrow eb">How the four days run</p><h2>Learn, practise, then test yourself</h2></header>
    <ol class="mflow">
      <li class="reveal"><i>Days 1–3</i><h3>Station-by-station teaching</h3><p>Intensive instruction on PACES stations 1, 2, 3, 4 and 5, in groups of four with one examiner.</p></li>
      <li class="reveal"><i>Every morning</i><h3>Special teaching sessions</h3><p>Dedicated sessions focusing on selected topics, led by the examiner faculty.</p></li>
      <li class="reveal"><i>Day 4</i><h3>Mock examination</h3><p>A realistic replica of the PACES exam, with results and feedback on the same day.</p></li>
    </ol>
    <p class="mrec reveal">Excel Paces is recognised as one of the leading PACES training programmes in India.</p>
    <p class="mcta reveal"><a class="btn btn-accent" href="<?= e(url('apply')) ?>">Apply now <?= icon('arrow') ?></a> <a class="btn btn-ghost" href="<?= e(url('testimonials')) ?>">Read candidate testimonials</a></p>
  </div>
</section>
