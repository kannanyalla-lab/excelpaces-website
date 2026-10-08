<?php
$menu = all('SELECT * FROM menu WHERE active=1 ORDER BY sort,id');
$cur = trim((string)($_GET['route'] ?? ''), '/');
$mu = fn($u) => preg_match('#^(https?:|mailto:|tel:)#', $u) ? $u : ($u === '/' ? url() : url($u));
$logo = setting('logo_main') ?: (is_file(ROOT . '/assets/img/logo.png') ? 'assets/img/logo.png' : '');
$col1 = setting('color_primary', '#12348A'); $col2 = setting('color_accent', '#E2231A'); $col3 = setting('color_ink', '#0A1A44');
$ok = fn($c) => preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? $c : '#12348A';
$nextCourse = open_course();
?><!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>"><meta property="og:description" content="<?= e($metaDesc) ?>"><meta property="og:type" content="website">
<meta name="theme-color" content="<?= e($ok($col1)) ?>">
<?php if ($fav = setting('favicon')): ?><link rel="icon" href="<?= e(media($fav)) ?>"><?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(url('assets/css/site.css')) ?>?v=<?= (int)@filemtime(ROOT . '/assets/css/site.css') ?>">
<style>:root{--brand:<?= e($ok($col1)) ?>;--accent:<?= e($ok($col2)) ?>;--ink:<?= e($ok($col3)) ?>}</style>
<?= setting('head_code') /* admin-controlled analytics snippet */ ?>
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip" href="#main">Skip to content</a>
<header class="hdr" id="hdr">
  <div class="wrap hdr-in">
    <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e(setting('site_name')) ?> home">
      <?php if ($logo): ?><img src="<?= e(media($logo)) ?>" alt="<?= e(setting('site_name')) ?>">
      <?php else: ?>
        <svg class="mark" viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="13" fill="var(--brand)"/><path d="M5 27h9l4-10 6 20 5-14 3 4h11" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="40" cy="12" r="3.2" fill="var(--accent)"/></svg>
        <span class="wm"><b>Excel<i>Paces</i></b><small>at KIMSHEALTH</small></span>
      <?php endif; ?>
    </a>
    <?php $kl = setting('logo_kims') ?: (is_file(ROOT . '/assets/img/kimshealth.png') ? 'assets/img/kimshealth.png' : ''); if ($kl): ?>
    <span class="brand-sep" aria-hidden="true"></span>
    <a class="brand-kims" href="<?= e(setting('kims_url', 'https://www.kimshealth.org')) ?>" target="_blank" rel="noopener" aria-label="KIMSHEALTH"><img src="<?= e(media($kl)) ?>" alt="KIMSHEALTH"></a>
    <?php endif; ?>
    <nav class="nav" id="nav" aria-label="Main">
      <?php foreach ($menu as $m): $active = ($m['url']==='/' ? $cur==='' : $cur===trim($m['url'],'/')); ?>
        <a href="<?= e($mu($m['url'])) ?>" class="<?= $m['cta'] ? 'cta' : '' ?> <?= $active ? 'on' : '' ?>"><?= e($m['label']) ?></a>
      <?php endforeach; ?>
    </nav>
    <button class="burger" id="burger" aria-label="Menu" aria-expanded="false"><span></span><span></span><span></span></button>
  </div>
</header>
<main id="main">
