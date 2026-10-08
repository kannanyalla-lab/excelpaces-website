<?php
/* One-click import of the photos used on the original excelpaces.com site.
   Runs on the web server (same Hostinger account), so it can copy the files directly or download them.
   Safe to run repeatedly: it skips anything already imported. */
const ORIG_HOST = 'https://excelpaces.com';
if (!function_exists('setting')) { function setting(string $k, string $d = ''): string { $v = val('SELECT v FROM settings WHERE k=?', [$k]); return ($v !== null && $v !== '') ? (string)$v : $d; } }

function imp_find_local_root(): ?string {
    // On Hostinger: /home/uXXXX/domains/<domain>/public_html. The old site lives beside this one.
    $p = str_replace('\\', '/', realpath(ROOT) ?: ROOT);
    if (preg_match('#^(.*?/domains)/[^/]+/public_html#', $p, $m)) {
        $c = $m[1] . '/excelpaces.com/public_html';
        if (is_dir($c . '/wp-content/uploads')) return $c;
    }
    return null;
}
function imp_http(string $url): ?string {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30, CURLOPT_USERAGENT => 'ExcelPacesImporter/1.0', CURLOPT_MAXREDIRS => 4]);
        $d = curl_exec($ch); $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        return ($d !== false && $code >= 200 && $code < 300) ? $d : null;
    }
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'user_agent' => 'ExcelPacesImporter/1.0']]);
    $d = @file_get_contents($url, false, $ctx);
    return $d === false ? null : $d;
}
/* Returns relative uploads path or null */
function imp_fetch(string $url, string $prefix = ''): ?string {
    $rel = preg_replace('#^https?://(www\.)?excelpaces\.com/#i', '', $url);
    $data = null;
    if ($root = imp_find_local_root()) {
        $f = $root . '/' . $rel;
        if (is_file($f) && !str_contains($rel, '..')) $data = @file_get_contents($f);
    }
    if ($data === null) $data = imp_http(str_starts_with($url, 'http') ? $url : ORIG_HOST . '/' . ltrim($url, '/'));
    if ($data === null || strlen($data) < 500 || strlen($data) > 12 * 1024 * 1024) return null;
    $info = @getimagesizefromstring($data);
    $ext = $info ? ([IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'][$info[2]] ?? null) : null;
    if (!$ext) return null;
    $dir = ROOT . '/uploads/original';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $base = preg_replace('/[^A-Za-z0-9_-]+/', '-', pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME));
    $name = $prefix . substr($base, 0, 60) . '-' . substr(md5($url), 0, 6) . '.' . $ext;
    if (!@file_put_contents("$dir/$name", $data)) return null;
    return 'uploads/original/' . $name;
}

function import_original_images(): array {
    $log = []; $u = ORIG_HOST . '/wp-content/uploads/';
    $faculty = [
        'Sahadulla' => '2023/05/sa.jpg', 'Saifudeen' => '2023/05/3232.jpg', 'Prasad Nair' => '2023/05/drprasad.png',
        'Ajit Thomas' => '2023/05/Ajit-Thomas.jpg', 'Farquhar' => '2023/05/Donald.jpg', 'Jacqueline' => '2023/05/Jacqueline.jpg',
        'Livingston' => '2024/06/PHOTO-2024-04-24-18-51-54-1.jpg', 'Murphy' => '2024/05/murphy.jpg', 'Manoj' => '2025/02/Dr-Manoj-CASUAL-PHOTO.jpg',
        'Haneef' => '2024/07/Dr-Shaji-MH.jpg', 'Mathew Thomas' => '2023/05/mathew.jpg', 'Mahadevan' => '2023/05/mahadevan.jpg',
        'Panduka' => '2023/05/panduka.jpg', 'Bhandari' => '2023/05/Sunil.jpg',
    ];
    $n = 0;
    foreach ($faculty as $needle => $path) {
        $row = one('SELECT id, photo FROM faculty WHERE name LIKE ?', ['%' . $needle . '%']);
        if (!$row || $row['photo']) continue;
        if ($f = imp_fetch($u . $path, 'fac-')) { upd('faculty', (int)$row['id'], ['photo' => $f]); $n++; }
    }
    $log[] = "Faculty photos imported: $n";
    // the Chairman's photo for the quote block
    if (!setting('endorse_photo') && ($f = imp_fetch($u . '2023/05/sa.jpg', 'chair-'))) set_setting('endorse_photo', $f);
    if (!setting('favicon') && ($f = imp_fetch($u . '2023/05/cropped-favicon-270x270.png', 'icon-'))) set_setting('favicon', $f);

    // page images
    foreach (['course' => '2026/04/excelpaces-image0002.jpg', 'merit' => '2026/04/EXCELPACES-mERIT-COURSES-KIMSHEALTH.jpg'] as $slug => $path) {
        $row = one('SELECT id, image FROM pages WHERE slug=?', [$slug]);
        if ($row && !$row['image'] && ($f = imp_fetch($u . $path, 'page-'))) upd('pages', (int)$row['id'], ['image' => $f]);
    }

    // hero banners
    $slides = all('SELECT id, image FROM slides ORDER BY sort,id');
    $banners = [ORIG_HOST . '/wp-content/uploads/slider/cache/e4e4ab7947618b937e3db7feb4b2cd5c/sl1.jpg', ORIG_HOST . '/wp-content/uploads/slider/cache/518f8606854be99f1eefebf09ed27d1a/Slider-excel-paces-2026.jpg', ORIG_HOST . '/wp-content/uploads/slider/cache/5c89c6c2238553fb3b6aca98935142a4/sld3.jpg'];
    $b = 0;
    foreach ($slides as $i => $s) { if (!$s['image'] && isset($banners[$i]) && ($f = imp_fetch($banners[$i], 'hero-'))) { upd('slides', (int)$s['id'], ['image' => $f]); $b++; } }
    $log[] = "Banner images imported: $b";

    // gallery: known photos + anything found on the gallery pages
    $urls = array_map(fn($p) => $u . $p, ['2024/11/paces-course-exam-453-scaled.jpg','2024/02/IMG_5694-1.jpg','2024/11/paces-course-exam-213-scaled.jpg','2024/11/paces-course-exam-456-scaled.jpg','2024/11/paces-course-exam-123-scaled.jpg','2024/02/IMG-1.jpg','2024/02/IMG_5468-1.jpg','2024/02/IMG_5481-1.jpg','2024/02/IMG_5471-1.jpg','2024/02/IMG_5505-1.jpg','2024/02/IMG_5467-1.jpg','2024/02/IMG_5484-1.jpg']);
    foreach (['/media-gallery/', '/media-gallery/page/2/', '/media-gallery/page/3/', '/media-gallery/page/4/'] as $pg) {
        $html = imp_http(ORIG_HOST . $pg);
        if ($html && preg_match_all('#https?://(?:www\.)?excelpaces\.com/wp-content/uploads/(\d{4}/\d{2}/[^"\'\s)]+\.(?:jpe?g|png|webp))#i', $html, $m)) {
            foreach ($m[1] as $rel) { if (!preg_match('/-\d+x\d+\./', $rel) && !preg_match('/logo|favicon|icon/i', $rel)) $urls[] = $u . $rel; }
        }
    }
    $g = 0; $max = (int)val('SELECT COALESCE(MAX(sort),0) FROM gallery');
    foreach (array_unique($urls) as $url) {
        $title = pathinfo($url, PATHINFO_FILENAME);
        if (val('SELECT COUNT(*) FROM gallery WHERE title=?', [$title])) continue;
        if ($f = imp_fetch($url, 'gal-')) { ins('gallery', ['title' => $title, 'file' => $f, 'sort' => ++$max, 'active' => 1]); $g++; }
    }
    $log[] = "Gallery photos imported: $g";
    if (!$n && !$g && !$b) $log[] = 'Nothing could be fetched. Check that excelpaces.com is reachable from this server, or upload photos manually in the CMS.';
    return ['log' => $log, 'faculty' => $n, 'gallery' => $g, 'banners' => $b];
}
