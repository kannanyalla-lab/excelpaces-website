<?php
declare(strict_types=1);
// One-time installer. Delete this file after installation.
define('ROOT', __DIR__);
if (is_file(ROOT . '/config.php')) { http_response_code(403); exit('<h2 style="font-family:sans-serif">Already installed.</h2><p style="font-family:sans-serif">Delete <code>install.php</code> from the server for safety.</p>'); }
require ROOT . '/inc/db.php';
require ROOT . '/inc/schema.php';
require ROOT . '/inc/seed.php';
define('BASE', '');
function set_setting($k,$val){ if (val('SELECT COUNT(*) FROM settings WHERE k=?',[$k])) q('UPDATE settings SET v=? WHERE k=?',[$val,$k]); else q('INSERT INTO settings (k,v) VALUES (?,?)',[$k,$val]); }
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
$err = ''; $v = ['host'=>'localhost','name'=>'','user'=>'','aname'=>'','aemail'=>'','driver'=>'mysql'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) if (isset($_POST[$k])) $v[$k] = trim((string)$_POST[$k]);
    $pass = (string)($_POST['pass'] ?? ''); $apass = (string)($_POST['apass'] ?? '');
    if ($v['driver'] !== 'sqlite' && $v['driver'] !== 'mysql') $v['driver'] = 'mysql';
    if (!filter_var($v['aemail'], FILTER_VALIDATE_EMAIL)) $err = 'Enter a valid admin e-mail.';
    elseif (strlen($apass) < 8) $err = 'Admin password must be at least 8 characters.';
    elseif ($v['name'] === '' && $v['driver'] === 'mysql') $err = 'Enter the database name.';
    if (!$err) {
        try {
            $CFG = $v['driver'] === 'sqlite'
                ? ['driver'=>'sqlite','sqlite'=>'data.sqlite','timezone'=>'Asia/Kolkata']
                : ['driver'=>'mysql','host'=>$v['host'],'name'=>$v['name'],'user'=>$v['user'],'pass'=>$pass,'timezone'=>'Asia/Kolkata'];
            $GLOBALS['CFG'] = $CFG;
            $pdo = db();
            create_schema($pdo, $CFG['driver']);
            if ((int)val('SELECT COUNT(*) FROM users') === 0) {
                ins('users', ['name'=>$v['aname'] ?: 'Administrator','email'=>$v['aemail'],'pass'=>password_hash($apass, PASSWORD_DEFAULT),'role'=>'admin','created'=>date('c')]);
            }
            if ((int)val('SELECT COUNT(*) FROM settings') === 0) {
                seed_content();
            }
            set_setting('schema_v', '2');
            file_put_contents(ROOT . '/config.php', "<?php\nreturn " . var_export($CFG, true) . ";\n");
            @chmod(ROOT . '/config.php', 0640);
            // best-effort: copy photos from the original excelpaces.com site
            try { @set_time_limit(150); require ROOT . '/inc/import.php'; $imp = import_original_images(); } catch (Throwable $ex) { $imp = ['log'=>['Photo import skipped: ' . $ex->getMessage()]]; }
            $done = true;
        } catch (Throwable $ex) { $err = 'Database error: ' . $ex->getMessage(); }
    }
}
$b = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])); $b = rtrim($b,'/');
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install · Excel Paces</title>
<style>
*{box-sizing:border-box}body{margin:0;font:16px/1.5 system-ui,Segoe UI,sans-serif;background:#0A1A44;min-height:100vh;display:grid;place-items:center;padding:24px;color:#0A1A44}
.card{background:#fff;border-radius:18px;max-width:520px;width:100%;padding:34px;box-shadow:0 30px 80px rgba(0,0,0,.35)}
h1{margin:0 0 4px;font-size:26px}p.s{margin:0 0 22px;color:#5a6785}label{display:block;font-weight:600;font-size:14px;margin:14px 0 5px}
input{width:100%;padding:12px 14px;border:1.5px solid #d5dbea;border-radius:10px;font:inherit}input:focus{outline:none;border-color:#12348A}
button{margin-top:24px;width:100%;padding:14px;border:0;border-radius:12px;background:#12348A;color:#fff;font:600 16px inherit;cursor:pointer}
.err{background:#fde8e6;color:#9b1c14;padding:12px 14px;border-radius:10px;margin-bottom:10px}.ok{background:#e4f6ec;color:#14633a;padding:16px;border-radius:12px}
h3{margin:26px 0 0;font-size:13px;letter-spacing:.1em;text-transform:uppercase;color:#E2231A}a.btn{display:block;text-align:center;margin-top:14px;padding:14px;border-radius:12px;background:#12348A;color:#fff;text-decoration:none;font-weight:600}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}details{margin-top:20px;font-size:14px;color:#5a6785}
</style></head><body><div class="card">
<h1>Excel Paces · Setup</h1><p class="s">Two minutes: connect the database and create your admin login.</p>
<?php if (!empty($done)): ?>
  <div class="ok"><strong>Installed.</strong> Your site and CMS are ready.<br>For safety, <strong>delete install.php</strong> from the server now.<?php if (!empty($imp['log'])): ?><br><br><small><?= e(implode(" · ", $imp['log'])) ?></small><?php endif; ?></div>
  <a class="btn" href="<?= e($b) ?>/admin/">Open the CMS</a><a class="btn" style="background:#E2231A" href="<?= e($b) ?>/">View the website</a>
<?php else: ?>
  <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <input type="hidden" name="driver" value="<?= e($v['driver']) ?>">
    <?php if ($v['driver'] === 'mysql'): ?>
    <h3>1 · MySQL database (hPanel → Databases)</h3>
    <label>Database name</label><input name="name" value="<?= e($v['name']) ?>" placeholder="u123456789_excelpaces">
    <div class="row"><div><label>Username</label><input name="user" value="<?= e($v['user']) ?>" placeholder="u123456789_admin"></div>
    <div><label>Password</label><input type="password" name="pass"></div></div>
    <label>Host</label><input name="host" value="<?= e($v['host']) ?>">
    <?php endif; ?>
    <h3><?= $v['driver']==='mysql' ? '2' : '1' ?> · Admin account</h3>
    <label>Your name</label><input name="aname" value="<?= e($v['aname']) ?>">
    <label>E-mail (login)</label><input type="email" name="aemail" value="<?= e($v['aemail']) ?>" required>
    <label>Password (min. 8 characters)</label><input type="password" name="apass" required>
    <button>Install website</button>
  </form>
<?php endif; ?></div></body></html>
