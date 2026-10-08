<?php
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $p = ''): string { return BASE . '/' . ltrim($p, '/'); }
function media(?string $p): string { return $p ? url($p) : ''; }
function settings(): array {
    static $s = null;
    if ($s === null) { $s = []; foreach (all('SELECT k,v FROM settings') as $r) $s[$r['k']] = $r['v']; }
    return $s;
}
function setting(string $k, string $d = ''): string { $s = settings(); return isset($s[$k]) && $s[$k] !== '' ? $s[$k] : $d; }
function set_setting(string $k, string $v): void {
    if (val('SELECT COUNT(*) FROM settings WHERE k=?', [$k])) q('UPDATE settings SET v=? WHERE k=?', [$v, $k]);
    else q('INSERT INTO settings (k,v) VALUES (?,?)', [$k, $v]);
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf()) . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) { http_response_code(419); exit('Session expired. Please go back, refresh the page and try again.'); }
}
function flash(?string $msg = null, string $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'][] = [$type, $msg]; return; }
    $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f;
}
function redirect(string $to): never { header('Location: ' . $to); exit; }
function post(string $k, string $d = ''): string { return trim((string)($_POST[$k] ?? $d)); }
function slugify(string $s): string { $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-')); return $s ?: 'page'; }

function fmt_date(?string $d, string $f = 'j M Y'): string { if (!$d) return ''; $t = strtotime($d); return $t ? date($f, $t) : $d; }
function date_range(?string $a, ?string $b): string {
    if (!$a) return '';
    $ta = strtotime($a); $tb = $b ? strtotime($b) : 0;
    if (!$tb || $tb === $ta) return date('j F Y', $ta);
    if (date('Ym', $ta) === date('Ym', $tb)) return date('j', $ta) . '–' . date('j F Y', $tb);
    return date('j M', $ta) . ' – ' . date('j M Y', $tb);
}

/* Allow-list HTML sanitiser for CMS rich text */
function clean_html(string $html): string {
    $html = preg_replace('#<(script|style|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $html);
    $html = strip_tags($html, '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><a><blockquote><img><table><thead><tbody><tr><th><td><hr><span><div>');
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*(javascript|data|vbscript):[^"\']*\2/i', '$1=$2#$2', $html);
    $html = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html);
    return $html;
}

/* Image upload; returns relative path or null */
function upload_image(string $field, ?string &$err = null): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) { $err = 'Upload failed (code ' . $f['error'] . ').'; return null; }
    if ($f['size'] > 6 * 1024 * 1024) { $err = 'Image is larger than 6 MB.'; return null; }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
    if (!$ext || !@getimagesize($f['tmp_name'])) { $err = 'Only JPG, PNG, WEBP or GIF images are allowed.'; return null; }
    $dir = ROOT . '/uploads/' . date('Y-m');
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) { $err = 'Could not save the file (check folder permissions).'; return null; }
    return 'uploads/' . date('Y-m') . '/' . $name;
}
function delete_upload(?string $p): void {
    if ($p && str_starts_with($p, 'uploads/') && !str_contains($p, '..')) @unlink(ROOT . '/' . $p);
}

/* Auth */
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user()) redirect(url('admin/index.php?a=login')); }
function mail_notify(string $subject, string $body, string $replyTo = ''): void {
    $to = setting('notify_email', setting('email'));
    if (!$to) return;
    $host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
    $h = "From: " . setting('site_name', 'Excel Paces') . " <noreply@$host>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $h .= "Reply-To: $replyTo\r\n";
    @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $h);
}
function icon(string $name): string {
    static $i = [
        'chip' => '<path d="M9 3v2M15 3v2M9 19v2M15 19v2M3 9h2M3 15h2M19 9h2M19 15h2"/><rect x="6" y="6" width="12" height="12" rx="2"/><rect x="10" y="10" width="4" height="4"/>',
        'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'award' => '<circle cx="12" cy="9" r="6"/><path d="M8.5 14.5 7 22l5-3 5 3-1.5-7.5"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M16 4.5a3.5 3.5 0 0 1 0 7M18.5 14.5c1.9.9 3 2.7 3 5.5"/>',
        'lung' => '<path d="M12 4v8M12 12c-1 2-3 3-5 3-2 0-3.5 2-3.5 4 0 1 .8 1.5 2 1.5 3 0 5-1.5 6.5-4.5M12 12c1 2 3 3 5 3 2 0 3.5 2 3.5 4 0 1-.8 1.5-2 1.5-3 0-5-1.5-6.5-4.5"/>',
        'chat' => '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 10h8M8 13h5"/>',
        'heart' => '<path d="M12 20s-8-5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 9c0 6-8 11-8 11z"/>',
        'brain' => '<path d="M9 4a3 3 0 0 0-3 3 3 3 0 0 0-2 5 3 3 0 0 0 2 5 3 3 0 0 0 6 1V4a3 3 0 0 0-3 0zM15 4a3 3 0 0 1 3 3 3 3 0 0 1 2 5 3 3 0 0 1-2 5 3 3 0 0 1-6 1"/>',
        'steth' => '<path d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2a5 5 0 0 0 10 0v-2"/><circle cx="20" cy="11" r="2"/>',
        'check' => '<path d="M5 12.5 10 17.5 19 7"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/>',
        'star' => '<path d="m12 3 2.7 5.6 6.1.8-4.5 4.2 1.1 6-5.4-3-5.4 3 1.1-6L3.2 9.4l6.1-.8z"/>',
        'shield' => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'pin' => '<path d="M12 21s7-6 7-11a7 7 0 0 0-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    ];
    $p = $i[$name] ?? $i['star'];
    return '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}
const ICONS = ['chip','target','calendar','award','users','lung','chat','heart','brain','steth','check','clock','globe','star','shield'];
