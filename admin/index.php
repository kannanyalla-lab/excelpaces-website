<?php
declare(strict_types=1);
require dirname(__DIR__) . '/inc/bootstrap.php';
require __DIR__ . '/modules.php';
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

$a = $_GET['a'] ?? 'dash';
$self = url('admin/index.php');
function aurl(string $q = ''): string { return url('admin/index.php') . ($q ? '?' . $q : ''); }

/* ---------- auth ---------- */
if ($a === 'logout') { $_SESSION = []; session_destroy(); redirect(aurl('a=login')); }
if ($a === 'login') {
    $ipk = 'lf_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    [$fails, $t] = array_pad(explode('|', setting($ipk, '0|0')), 2, 0);
    $locked = (int)$fails >= 6 && time() - (int)$t < 600;
    $err = $locked ? 'Too many attempts. Try again in 10 minutes.' : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
        csrf_check();
        $u = one('SELECT * FROM users WHERE email=?', [strtolower(post('email'))]);
        if ($u && password_verify((string)($_POST['pass'] ?? ''), $u['pass'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>(int)$u['id'],'name'=>$u['name'],'email'=>$u['email']];
            set_setting($ipk, '0|0');
            redirect(aurl());
        }
        set_setting($ipk, ((int)$fails + 1) . '|' . time()); usleep(600000);
        $err = 'Incorrect e-mail or password.';
    }
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>CMS Login</title>
    <link rel="stylesheet" href="<?= e(url('admin/admin.css')) ?>"></head><body class="login"><form method="post" class="lcard"><?= csrf_field() ?>
    <div class="lmark">XP</div><h1>Excel Paces CMS</h1><p>Sign in to manage the website</p>
    <?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
    <label>E-mail<input type="email" name="email" required autofocus></label><label>Password<input type="password" name="pass" required></label>
    <button class="btn p">Sign in</button><a class="back" href="<?= e(url()) ?>">← Back to website</a></form></body></html><?php
    exit;
}
require_login();

/* ---------- helpers ---------- */
function field_input(array $f, $val, ?array $row): string {
    [$n, $label, $type] = $f; $h = $f['help'] ?? ''; $o = $f['opts'] ?? [];
    $id = 'f_' . $n; $v = e((string)$val);
    $out = '<div class="fld t-' . $type . '"><label for="' . $id . '">' . e($label) . '</label>';
    switch ($type) {
        case 'textarea': $out .= "<textarea id=\"$id\" name=\"$n\" rows=\"4\">$v</textarea>"; break;
        case 'rich': $out .= "<div class=\"rte\" data-name=\"$n\"><textarea name=\"$n\" id=\"$id\" hidden>$v</textarea></div>"; break;
        case 'check': $out = '<div class="fld t-check"><label class="chk"><input type="hidden" name="' . $n . '" value="0"><input type="checkbox" name="' . $n . '" value="1" ' . ($val === null || (int)$val ? 'checked' : '') . '> ' . e($label) . '</label>'; break;
        case 'number': $out .= "<input type=\"number\" id=\"$id\" name=\"$n\" value=\"" . e((string)($val ?? 0)) . "\">"; break;
        case 'date': $out .= "<input type=\"date\" id=\"$id\" name=\"$n\" value=\"$v\">"; break;
        case 'color': $out .= "<div class=\"colorrow\"><input type=\"color\" id=\"$id\" name=\"$n\" value=\"" . e($val ?: '#12348A') . "\"><code>" . e($val ?: '') . "</code></div>"; break;
        case 'password': $out .= "<input type=\"password\" id=\"$id\" name=\"$n\" autocomplete=\"new-password\" placeholder=\"" . ($row ? 'Leave blank to keep current' : '') . "\">"; break;
        case 'select':
            $out .= "<select id=\"$id\" name=\"$n\">"; foreach ($o as $k => $l) $out .= '<option value="' . e((string)$k) . '"' . ((string)$val === (string)$k ? ' selected' : '') . '>' . e($l) . '</option>'; $out .= '</select>'; break;
        case 'icon':
            $out .= "<div class=\"icons\">"; foreach (ICONS as $ic) $out .= '<label title="' . $ic . '"><input type="radio" name="' . $n . '" value="' . $ic . '"' . (($val ?: 'star') === $ic ? ' checked' : '') . '><span>' . icon($ic) . '</span></label>'; $out .= '</div>'; break;
        case 'image':
            $out .= '<div class="imgrow">' . ($val ? '<img src="' . e(media((string)$val)) . '" alt="">' : '<div class="noimg">No image</div>') . '<div><input type="file" id="' . $id . '" name="' . $n . '" accept="image/jpeg,image/png,image/webp,image/gif">' . ($val ? '<label class="chk small"><input type="checkbox" name="' . $n . '__del" value="1"> Remove image</label>' : '') . '</div></div>'; break;
        default: $out .= "<input type=\"" . ($type === 'email' ? 'email' : 'text') . "\" id=\"$id\" name=\"$n\" value=\"$v\">";
    }
    if ($h) $out .= '<small>' . e($h) . '</small>';
    return $out . '</div>';
}
/* collect posted fields -> row data; handles uploads */
function collect(array $fields, ?array $old, array &$errs): array {
    $d = [];
    foreach ($fields as $f) {
        [$n, , $type] = $f;
        switch ($type) {
            case 'check': $d[$n] = !empty($_POST[$n]) && $_POST[$n] !== '0' ? 1 : 0; break;
            case 'number': $d[$n] = (int)($_POST[$n] ?? 0); break;
            case 'rich': $d[$n] = clean_html((string)($_POST[$n] ?? '')); break;
            case 'slug': $d[$n] = slugify(post($n)); break;
            case 'password': if (post($n) !== '') { if (strlen((string)$_POST[$n]) < 8) $errs[] = 'Password must be at least 8 characters.'; else $d[$n] = password_hash((string)$_POST[$n], PASSWORD_DEFAULT); } break;
            case 'image':
                $e = null; $new = upload_image($n, $e);
                if ($e) $errs[] = $e;
                if ($new) { if (!empty($old[$n])) delete_upload($old[$n]); $d[$n] = $new; }
                elseif (!empty($_POST[$n . '__del'])) { delete_upload($old[$n] ?? ''); $d[$n] = ''; }
                break;
            case 'email': $d[$n] = strtolower(post($n)); break;
            default: $d[$n] = post($n);
        }
    }
    return $d;
}
function cell($v, $n): string {
    if ($n === 'active' || $n === 'featured') return (int)$v ? '<span class="pill g">Yes</span>' : '<span class="pill">No</span>';
    if ($n === 'status') return '<span class="pill ' . ($v === 'open' ? 'g' : ($v === 'full' ? 'o' : '')) . '">' . e((string)$v) . '</span>';
    if ($n === 'logo' || $n === 'photo') return $v ? '<img class="thumb" src="' . e(media((string)$v)) . '" alt="">' : '<span class="muted">—</span>';
    $s = (string)$v; return e(mb_strlen($s) > 70 ? mb_substr($s, 0, 70) . '…' : $s);
}

/* ---------- layout ---------- */
function layout_start(string $title, string $active): void {
    global $MODULES; $u = user();
    $pendT = (int)val("SELECT COUNT(*) FROM testimonials WHERE status='pending'");
    $unread = (int)val('SELECT COUNT(*) FROM messages WHERE is_read=0'); $newapp = (int)val("SELECT COUNT(*) FROM applications WHERE status='new'");
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> · CMS</title>
    <link rel="stylesheet" href="<?= e(url('admin/admin.css')) ?>?v=1"></head><body>
    <aside class="side" id="side"><div class="logo"><span class="lmark">XP</span><div><b>Excel Paces</b><small>Content manager</small></div></div>
    <nav>
      <a href="<?= e(aurl()) ?>" class="<?= $active === 'dash' ? 'on' : '' ?>">🏠 Dashboard</a>
      <p>Inbox</p>
      <a href="<?= e(aurl('a=apps')) ?>" class="<?= $active === 'apps' ? 'on' : '' ?>">📝 Applications <?php if ($newapp): ?><em><?= $newapp ?></em><?php endif; ?></a>
      <a href="<?= e(aurl('a=msgs')) ?>" class="<?= $active === 'msgs' ? 'on' : '' ?>">✉️ Messages <?php if ($unread): ?><em><?= $unread ?></em><?php endif; ?></a>
      <?php $g = ''; foreach ($MODULES as $k => $m): if ($m['group'] !== $g) { $g = $m['group']; echo '<p>' . e($g) . '</p>'; } ?>
        <a href="<?= e(aurl('a=list&m=' . $k)) ?>" class="<?= $active === $k ? 'on' : '' ?>"><?= $m['icon'] ?> <?= e($m['title']) ?><?php if ($k === 'testimonials' && $pendT): ?> <em><?= $pendT ?></em><?php endif; ?></a>
        <?php if ($k === 'faculty'): ?><?php endif; endforeach; ?>
      <a href="<?= e(aurl('a=gallery')) ?>" class="<?= $active === 'gallery' ? 'on' : '' ?>">📷 Gallery</a>
      <a href="<?= e(aurl('a=settings')) ?>" class="<?= $active === 'settings' ? 'on' : '' ?>">⚙️ Site Settings</a>
      <a href="<?= e(aurl('a=import')) ?>" class="<?= $active === 'import' ? 'on' : '' ?>">⬇️ Import original photos</a>
    </nav>
    <div class="side-f"><a href="<?= e(url()) ?>" target="_blank">View website ↗</a><a href="<?= e(aurl('a=logout')) ?>">Sign out (<?= e($u['name']) ?>)</a></div></aside>
    <div class="main"><header class="top"><button class="menu-b" onclick="document.getElementById('side').classList.toggle('open')">☰</button><h1><?= e($title) ?></h1><a class="btn s" href="<?= e(url()) ?>" target="_blank">View site ↗</a></header>
    <div class="content"><?php foreach (flash() as [$t, $m]): ?><div class="flash <?= $t ?>"><?= e($m) ?></div><?php endforeach;
}
function layout_end(): void { ?></div></div><script src="<?= e(url('admin/admin.js')) ?>?v=1"></script></body></html><?php }

/* ---------- actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') csrf_check();

switch ($a) {
case 'dash':
    layout_start('Dashboard', 'dash');
    $stats = [['Applications', (int)val('SELECT COUNT(*) FROM applications'), 'apps'], ['New applications', (int)val("SELECT COUNT(*) FROM applications WHERE status='new'"), 'apps'], ['Messages', (int)val('SELECT COUNT(*) FROM messages'), 'msgs'], ['Faculty', (int)val('SELECT COUNT(*) FROM faculty WHERE active=1'), 'list&m=faculty']];
    $next = one("SELECT * FROM courses WHERE active=1 AND status IN ('open','full') AND start_date >= ? ORDER BY start_date LIMIT 1", [date('Y-m-d')]);
    ?><div class="stats"><?php foreach ($stats as $s): ?><a class="stat" href="<?= e(aurl('a=' . $s[2])) ?>"><b><?= $s[1] ?></b><span><?= e($s[0]) ?></span></a><?php endforeach; ?></div>
    <?php if ($pt = (int)val("SELECT COUNT(*) FROM testimonials WHERE status='pending'")): ?><div class="flash warn"><b><?= $pt ?> testimonial(s) waiting for approval.</b> <a href="<?= e(aurl('a=list&m=testimonials')) ?>">Review now →</a></div><?php endif; ?>
    <?php if (!$next): ?><div class="flash warn"><b>No open course.</b> The home page shows “Dates to be announced”. <a href="<?= e(aurl('a=edit&m=courses')) ?>">Add the next course →</a></div><?php else: ?>
    <div class="panel"><h2>Next course</h2><p><b><?= e($next['title']) ?></b> · <?= e(date_range($next['start_date'], $next['end_date'])) ?> · <?= e($next['status']) ?> · <a href="<?= e(aurl('a=edit&m=courses&id=' . $next['id'])) ?>">edit</a></p></div><?php endif; ?>
    <div class="panel"><h2>Latest applications</h2><?php $ap = all('SELECT * FROM applications ORDER BY id DESC LIMIT 6'); if (!$ap) echo '<p class="muted">None yet.</p>'; else { echo '<table class="tbl"><tr><th>Name</th><th>E-mail</th><th>Phone</th><th>Date</th><th>Status</th></tr>'; foreach ($ap as $r) echo '<tr><td><a href="' . e(aurl('a=app&id=' . $r['id'])) . '">' . e($r['name']) . '</a></td><td>' . e($r['email']) . '</td><td>' . e($r['phone']) . '</td><td>' . e(fmt_date($r['created'], 'j M, H:i')) . '</td><td><span class="pill ' . ($r['status'] === 'new' ? 'o' : 'g') . '">' . e($r['status']) . '</span></td></tr>'; echo '</table>'; } ?></div>
    <div class="panel quick"><h2>Quick actions</h2><a class="btn" href="<?= e(aurl('a=edit&m=courses')) ?>">+ Add course dates</a><a class="btn" href="<?= e(aurl('a=edit&m=testimonials')) ?>">+ Testimonial</a><a class="btn" href="<?= e(aurl('a=edit&m=faculty')) ?>">+ Faculty member</a><a class="btn" href="<?= e(aurl('a=gallery')) ?>">+ Gallery photos</a><a class="btn" href="<?= e(aurl('a=list&m=partners')) ?>">Footer logos</a></div>
    <?php layout_end(); break;

case 'list':
    $k = $_GET['m'] ?? ''; $m = $MODULES[$k] ?? null; if (!$m) redirect(aurl());
    $rows = all("SELECT * FROM {$m['table']} ORDER BY {$m['order']}");
    layout_start($m['title'], $k);
    ?><div class="bar"><p class="muted"><?= e($m['help'] ?? '') ?></p><a class="btn p" href="<?= e(aurl("a=edit&m=$k")) ?>">+ Add <?= e($m['single']) ?></a></div>
    <div class="panel np"><div class="tw"><table class="tbl"><tr><?php foreach ($m['cols'] as $c) echo '<th>' . e(ucwords(str_replace('_', ' ', $c))) . '</th>'; ?><th></th></tr>
    <?php foreach ($rows as $r): ?><tr><?php foreach ($m['cols'] as $i => $c): ?><td><?= $i === 0 ? '<a href="' . e(aurl("a=edit&m=$k&id={$r['id']}")) . '"><b>' . cell($r[$c], $c) . '</b></a>' : cell($r[$c], $c) ?></td><?php endforeach; ?>
      <td class="act"><?php if ($k === 'testimonials' && ($r['status'] ?? '') === 'pending'): ?><form method="post" action="<?= e(aurl("a=approve&id={$r['id']}")) ?>"><?= csrf_field() ?><button class="btn s p">Approve</button></form><?php endif; ?><a class="btn s" href="<?= e(aurl("a=edit&m=$k&id={$r['id']}")) ?>">Edit</a>
      <?php $prot = in_array($r['slug'] ?? '', $m['protect'] ?? [], true) || ($k === 'users' && (int)$r['id'] === user()['id']); if (!$prot): ?>
      <form method="post" action="<?= e(aurl("a=del&m=$k&id={$r['id']}")) ?>" onsubmit="return confirm('Delete this item permanently?')"><?= csrf_field() ?><button class="btn s d">Delete</button></form><?php endif; ?></td></tr><?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="9" class="muted">Nothing here yet.</td></tr><?php endif; ?></table></div></div>
    <?php layout_end(); break;

case 'edit':
    $k = $_GET['m'] ?? ''; $m = $MODULES[$k] ?? null; if (!$m) redirect(aurl());
    $id = (int)($_GET['id'] ?? 0); $row = $id ? one("SELECT * FROM {$m['table']} WHERE id=?", [$id]) : null; if ($id && !$row) redirect(aurl("a=list&m=$k"));
    $errs = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $d = collect($m['fields'], $row, $errs);
        if ($k === 'pages') { if ($row && in_array($row['slug'], $m['protect'], true)) $d['slug'] = $row['slug']; elseif (val('SELECT COUNT(*) FROM pages WHERE slug=? AND id<>?', [$d['slug'], $id])) $errs[] = 'That URL slug is already used.'; }
        if ($k === 'testimonials') { $d['active'] = ($d['status'] ?? '') === 'approved' ? 1 : 0; if (!$row) $d['created'] = date('Y-m-d H:i:s'); }
        if ($k === 'users' && !$row && empty($d['pass'])) $errs[] = 'A password is required for new users.';
        if ($k === 'users' && val('SELECT COUNT(*) FROM users WHERE email=? AND id<>?', [$d['email'], $id])) $errs[] = 'That e-mail is already used.';
        if (!$errs) {
            if ($row) upd($m['table'], $id, $d); else { if ($k === 'users') $d['created'] = date('c'); $id = ins($m['table'], $d); }
            flash(ucfirst($m['single']) . ' saved.'); redirect(aurl("a=list&m=$k"));
        }
        foreach ($errs as $x) flash($x, 'err'); $row = array_merge($row ?? [], $_POST);
    }
    layout_start(($id ? 'Edit ' : 'Add ') . $m['single'], $k);
    ?><form method="post" enctype="multipart/form-data" class="panel formx"><?= csrf_field() ?>
    <?php foreach ($m['fields'] as $f) echo field_input($f, $row[$f[0]] ?? ($f[2] === 'check' ? null : ''), $row); ?>
    <div class="fbar"><button class="btn p">Save</button><a class="btn" href="<?= e(aurl("a=list&m=$k")) ?>">Cancel</a></div></form>
    <?php layout_end(); break;

case 'del':
    $k = $_GET['m'] ?? ''; $m = $MODULES[$k] ?? null; $id = (int)($_GET['id'] ?? 0);
    if ($m && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $row = one("SELECT * FROM {$m['table']} WHERE id=?", [$id]);
        $ok = $row && !in_array($row['slug'] ?? '', $m['protect'] ?? [], true) && !($k === 'users' && ((int)$id === user()['id'] || (int)val('SELECT COUNT(*) FROM users') < 2));
        if ($ok) { foreach (['photo','logo','image'] as $c) if (!empty($row[$c])) delete_upload($row[$c]); q("DELETE FROM {$m['table']} WHERE id=?", [$id]); flash('Deleted.'); } else flash('This item cannot be deleted.', 'err');
    }
    redirect(aurl("a=list&m=$k"));

case 'approve':
    $id = (int)($_GET['id'] ?? 0);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) { q("UPDATE testimonials SET status='approved', active=1 WHERE id=?", [$id]); flash('Testimonial approved and published on the website.'); }
    redirect(aurl('a=list&m=testimonials'));

case 'import':
    require ROOT . '/inc/import.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { @set_time_limit(150); try { $r = import_original_images(); foreach ($r['log'] as $l) flash($l, strpos($l, 'Nothing') === 0 ? 'err' : 'ok'); } catch (Throwable $ex) { flash('Import failed: ' . $ex->getMessage(), 'err'); } redirect(aurl('a=import')); }
    layout_start('Import original photos', 'import');
    ?><div class="panel formx"><h2>Copy photos from excelpaces.com</h2><p>Imports the faculty portraits, hero banners, course images and gallery photos from the original website into this site. Photos already imported are skipped, and anything you uploaded yourself is never replaced.</p>
    <form method="post"><?= csrf_field() ?><button class="btn p">Import photos now</button></form><p class="muted" style="margin-top:14px">This can take up to a minute.</p></div>
    <?php layout_end(); break;

case 'settings':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errs = [];
        foreach ($SETTINGS as $fields) foreach ($fields as $f) {
            [$n, , $type] = $f;
            if ($type === 'image') {
                $e = null; $new = upload_image($n, $e); if ($e) $errs[] = $e;
                if ($new) { delete_upload(setting($n)); set_setting($n, $new); } elseif (!empty($_POST[$n . '__del'])) { delete_upload(setting($n)); set_setting($n, ''); }
            } elseif ($type === 'color') { $c = post($n); if (preg_match('/^#[0-9a-fA-F]{6}$/', $c)) set_setting($n, $c); }
            elseif ($n === 'head_code') set_setting($n, trim((string)($_POST[$n] ?? '')));
            elseif ($n === 'map_embed') { $v = trim((string)($_POST[$n] ?? '')); set_setting($n, preg_match('#^<iframe[^>]+src="https://www\.google\.com/maps/embed[^"]*"[^>]*></iframe>$#is', $v) ? $v : ''); if ($v !== '' && !preg_match('#google\.com/maps/embed#', $v)) $errs[] = 'Map code must be a Google Maps embed <iframe>.'; }
            else set_setting($n, post($n));
        }
        foreach ($errs as $x) flash($x, 'err'); if (!$errs) flash('Settings saved.'); redirect(aurl('a=settings'));
    }
    layout_start('Site Settings', 'settings');
    ?><form method="post" enctype="multipart/form-data" class="settings"><?= csrf_field() ?>
    <?php foreach ($SETTINGS as $g => $fields): ?><div class="panel formx"><h2><?= e($g) ?></h2><?php foreach ($fields as $f) echo field_input($f, settings()[$f[0]] ?? '', ['x' => 1]); ?></div><?php endforeach; ?>
    <div class="fbar sticky"><button class="btn p">Save all settings</button></div></form>
    <?php layout_end(); break;

case 'gallery':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['del'])) { $r = one('SELECT * FROM gallery WHERE id=?', [(int)$_POST['del']]); if ($r) { delete_upload($r['file']); q('DELETE FROM gallery WHERE id=?', [$r['id']]); flash('Photo deleted.'); } }
        elseif (isset($_POST['caps'])) { foreach ((array)$_POST['caps'] as $gid => $cap) q('UPDATE gallery SET title=? WHERE id=?', [trim((string)$cap), (int)$gid]); foreach ((array)($_POST['sorts'] ?? []) as $gid => $s) q('UPDATE gallery SET sort=? WHERE id=?', [(int)$s, (int)$gid]); flash('Gallery updated.'); }
        elseif (!empty($_FILES['photos']['name'][0])) {
            $n = 0; $f = $_FILES['photos'];
            foreach ($f['name'] as $i => $nm) {
                $_FILES['one'] = ['name'=>$nm,'type'=>$f['type'][$i],'tmp_name'=>$f['tmp_name'][$i],'error'=>$f['error'][$i],'size'=>$f['size'][$i]];
                $e = null; $p = upload_image('one', $e);
                if ($p) { ins('gallery', ['title'=>pathinfo($nm, PATHINFO_FILENAME),'file'=>$p,'sort'=>(int)val('SELECT COALESCE(MAX(sort),0)+1 FROM gallery'),'active'=>1]); $n++; } elseif ($e) flash("$nm: $e", 'err');
            }
            if ($n) flash("$n photo(s) uploaded.");
        }
        redirect(aurl('a=gallery'));
    }
    layout_start('Gallery', 'gallery'); $ph = all('SELECT * FROM gallery ORDER BY sort,id');
    ?><form method="post" enctype="multipart/form-data" class="panel dz"><?= csrf_field() ?><h2>Upload photos</h2><p class="muted">Select several images at once (JPG, PNG, WEBP · max 6 MB each).</p><input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" required><button class="btn p">Upload</button></form>
    <form method="post" class="panel"><?= csrf_field() ?><h2>Photos (<?= count($ph) ?>)</h2><div class="ggrid"><?php foreach ($ph as $p): ?>
      <div class="gcard"><img src="<?= e(media($p['file'])) ?>" alt=""><input name="caps[<?= $p['id'] ?>]" value="<?= e($p['title']) ?>" placeholder="Caption"><div class="grow"><input type="number" name="sorts[<?= $p['id'] ?>]" value="<?= (int)$p['sort'] ?>" title="Order"><button class="btn s d" name="del" value="<?= $p['id'] ?>" onclick="return confirm('Delete this photo?')">Delete</button></div></div>
    <?php endforeach; ?></div><?php if ($ph): ?><div class="fbar"><button class="btn p">Save captions &amp; order</button></div><?php endif; ?></form>
    <?php layout_end(); break;

case 'apps': case 'msgs':
    $isApp = $a === 'apps'; $tb = $isApp ? 'applications' : 'messages';
    if (($_GET['export'] ?? '') === '1') {
        header('Content-Type: text/csv; charset=utf-8'); header("Content-Disposition: attachment; filename=\"$tb-" . date('Y-m-d') . ".csv\"");
        $rows = all("SELECT * FROM $tb ORDER BY id DESC"); $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
        if ($rows) { fputcsv($o, array_keys($rows[0])); foreach ($rows as $r) { if (isset($r['employment'])) { $em = json_decode((string)$r['employment'], true) ?: []; $r['employment'] = implode('; ', array_map(fn($x) => trim(($x['hospital'] ?? '') . ' / ' . ($x['title'] ?? '') . ' / ' . ($x['years'] ?? '')), $em)); } fputcsv($o, array_map(fn($v) => preg_match('/^[=+\-@]/', (string)$v) ? "'" . $v : $v, $r)); } }
        exit;
    }
    layout_start($isApp ? 'Applications' : 'Messages', $a); $rows = all("SELECT * FROM $tb ORDER BY id DESC LIMIT 500");
    ?><div class="bar"><p class="muted">Submitted through the website forms. E-mail notifications are also sent to the address in Site Settings.</p><a class="btn" href="<?= e(aurl("a=$a&export=1")) ?>">⬇ Export CSV</a></div>
    <div class="panel np"><div class="tw"><table class="tbl"><tr><th>Name</th><th>E-mail</th><th>Phone</th><th><?= $isApp ? 'Course' : 'Subject' ?></th><th>Received</th><th><?= $isApp ? 'Status' : '' ?></th></tr>
    <?php foreach ($rows as $r): $new = $isApp ? $r['status'] === 'new' : !$r['is_read']; $cn = $isApp ? val('SELECT title FROM courses WHERE id=?', [$r['course_id']]) : $r['subject']; ?>
      <tr class="<?= $new ? 'unread' : '' ?>"><td><a href="<?= e(aurl('a=' . ($isApp ? 'app' : 'msg') . '&id=' . $r['id'])) ?>"><b><?= e($r['name']) ?></b></a></td><td><?= e($r['email']) ?></td><td><?= e($r['phone']) ?></td><td><?= e((string)$cn) ?></td><td><?= e(fmt_date($r['created'], 'j M Y, H:i')) ?></td><td><?= $isApp ? '<span class="pill ' . ($new ? 'o' : 'g') . '">' . e($r['status']) . '</span>' : '' ?></td></tr>
    <?php endforeach; if (!$rows) echo '<tr><td colspan="6" class="muted">Nothing yet.</td></tr>'; ?></table></div></div>
    <?php layout_end(); break;

case 'app': case 'msg':
    $isApp = $a === 'app'; $tb = $isApp ? 'applications' : 'messages'; $id = (int)($_GET['id'] ?? 0); $r = one("SELECT * FROM $tb WHERE id=?", [$id]); if (!$r) redirect(aurl('a=' . ($isApp ? 'apps' : 'msgs')));
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) { q("DELETE FROM $tb WHERE id=?", [$id]); flash('Deleted.'); redirect(aurl('a=' . ($isApp ? 'apps' : 'msgs'))); }
        if ($isApp) { $st = in_array($_POST['status'] ?? '', ['new','contacted','confirmed','waitlist','declined'], true) ? $_POST['status'] : 'new'; q('UPDATE applications SET status=? WHERE id=?', [$st, $id]); flash('Status updated.'); redirect(aurl("a=app&id=$id")); }
    }
    if (!$isApp && !$r['is_read']) q('UPDATE messages SET is_read=1 WHERE id=?', [$id]);
    if ($isApp && $r['status'] === 'new' && ($_GET['seen'] ?? '') === '') { }
    layout_start($isApp ? 'Application' : 'Message', $isApp ? 'apps' : 'msgs');
    $lbl = $isApp ? ['name'=>'Name','address'=>'Address','phone'=>'Phone','email'=>'Email ID','gender'=>'Gender','dob'=>'Date of birth','nationality'=>'Nationality','institution'=>'Educational institution','qualification'=>'Qualification','percentage'=>'Percentage score','year_grad'=>'Year graduated','created'=>'Received'] : ['name'=>'Name','email'=>'E-mail','phone'=>'Phone','subject'=>'Subject','body'=>'Message','created'=>'Received'];
    ?><div class="panel"><dl class="dl"><?php foreach ($lbl as $k => $l): ?><dt><?= $l ?></dt><dd><?= $k === 'email' ? '<a href="mailto:' . e($r[$k]) . '">' . e($r[$k]) . '</a>' : nl2br(e((string)$r[$k])) ?></dd><?php endforeach; ?>
    <?php if ($isApp): ?><dt>Course</dt><dd><?= e((string)(val('SELECT title FROM courses WHERE id=?', [$r['course_id']]) ?: '—')) ?></dd>
    <dt>Employment history</dt><dd><?php $emp = json_decode((string)($r['employment'] ?? ''), true) ?: []; if (!$emp) echo '—'; else { echo '<table class="tbl"><tr><th>Hospital</th><th>Job title</th><th>Years</th></tr>'; foreach ($emp as $x) echo '<tr><td>' . e($x['hospital'] ?? '') . '</td><td>' . e($x['title'] ?? '') . '</td><td>' . e($x['years'] ?? '') . '</td></tr>'; echo '</table>'; } ?></dd><?php endif; ?></dl>
    <form method="post" class="fbar"><?= csrf_field() ?><?php if ($isApp): ?><select name="status"><?php foreach (['new','contacted','confirmed','waitlist','declined'] as $s) echo '<option' . ($r['status'] === $s ? ' selected' : '') . '>' . $s . '</option>'; ?></select><button class="btn p">Update status</button><?php endif; ?>
      <button class="btn d" name="delete" value="1" onclick="return confirm('Delete permanently?')">Delete</button><a class="btn" href="<?= e(aurl('a=' . ($isApp ? 'apps' : 'msgs'))) ?>">Back</a></form></div>
    <?php layout_end(); break;

default: redirect(aurl());
}
