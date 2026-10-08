<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$route = trim((string)($_GET['route'] ?? ''), '/');
$route = $route === 'index.php' ? '' : $route;
$flash = [];

function render(string $view, array $d = []): void {
    extract($d);
    $pageTitle = $d['pageTitle'] ?? setting('site_name');
    $metaDesc = $d['metaDesc'] ?? setting('meta_desc');
    $bodyClass = $d['bodyClass'] ?? 'inner';
    include ROOT . '/inc/views/header.php';
    include ROOT . "/inc/views/$view.php";
    include ROOT . '/inc/views/footer.php';
}
function open_course(): ?array {
    return one("SELECT * FROM courses WHERE active=1 AND status IN ('open','full') AND start_date >= ? ORDER BY start_date ASC LIMIT 1", [date('Y-m-d')]);
}

if ($route === '') {
    render('home', [
        'bodyClass' => 'home', 'pageTitle' => setting('site_name') . ' · ' . setting('tagline'),
        'slides' => all('SELECT * FROM slides WHERE active=1 ORDER BY sort,id'),
        'highlights' => all("SELECT * FROM items WHERE kind='highlight' AND active=1 ORDER BY sort,id"),
        'stations' => all("SELECT * FROM items WHERE kind='station' AND active=1 ORDER BY sort,id"),
        'why' => all("SELECT * FROM items WHERE kind='why' AND active=1 ORDER BY sort,id"),
        'faculty' => all('SELECT * FROM faculty WHERE active=1 AND featured=1 ORDER BY sort,id'),
        'tests' => array_slice(array_values(array_filter(all("SELECT * FROM testimonials WHERE active=1 AND status='approved' ORDER BY sort,id"), fn($t) => mb_strlen($t['quote']) <= 330)), 0, 14),
        'course' => open_course(),
        'photos' => all('SELECT * FROM gallery WHERE active=1 ORDER BY sort,id LIMIT 6'),
        'about' => one("SELECT * FROM pages WHERE slug='course'"),
    ]);
    exit;
}

$page = one('SELECT * FROM pages WHERE slug=? AND active=1', [$route]);
if (!$page) { http_response_code(404); render('page', ['page' => ['title' => 'Page not found', 'subtitle' => '', 'body' => '<p>The page you are looking for does not exist. <a href="' . e(url()) . '">Return home</a>.</p>', 'image' => ''], 'pageTitle' => 'Not found']); exit; }

$d = ['page' => $page, 'pageTitle' => $page['title'] . ' · ' . setting('site_name'), 'metaDesc' => $page['meta_desc'] ?: setting('meta_desc')];
switch ($route) {
    case 'merit':
        $d['merits'] = all("SELECT * FROM items WHERE kind='merit' AND active=1 ORDER BY sort,id"); render('merit', $d); break;
    case 'faculty':
        $d['faculty'] = all('SELECT * FROM faculty WHERE active=1 ORDER BY sort,id'); render('faculty', $d); break;
    case 'testimonials':
        $errs = []; 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            if (post('website') !== '') { redirect(url('testimonials?sent=1')); }
            $f = ['name'=>mb_substr(post('name'),0,160),'role'=>mb_substr(post('role'),0,200),'email'=>post('email'),'quote'=>mb_substr(post('quote'),0,1200)];
            if ($f['name']==='') $errs[]='Please enter your name.';
            if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errs[]='Please enter a valid e-mail address (it is not shown publicly).';
            if (mb_strlen($f['quote'])<15) $errs[]='Please write a few sentences about your experience.';
            if ((time() - (int)($_SESSION['last_form'] ?? 0)) < 8) $errs[]='Please wait a few seconds before sending again.';
            if (!$errs) {
                ins('testimonials', $f + ['photo'=>'','sort'=>999,'active'=>0,'status'=>'pending','created'=>date('Y-m-d H:i:s')]);
                $_SESSION['last_form'] = time();
                mail_notify('New testimonial awaiting approval', "From: {$f['name']} ({$f['email']})\n\n{$f['quote']}\n\nApprove or reject it in the CMS under Testimonials.", $f['email']);
                redirect(url('testimonials?sent=1#share'));
            }
            $d['old'] = $f;
        }
        $d['sent'] = isset($_GET['sent']); $d['errs'] = $errs;
        $d['tests'] = all("SELECT * FROM testimonials WHERE active=1 AND status='approved' ORDER BY sort,id"); render('testimonials', $d); break;
    case 'gallery':
        $d['photos'] = all('SELECT * FROM gallery WHERE active=1 ORDER BY sort,id'); render('gallery', $d); break;
    case 'contact':
        $sent = false; $errs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            if (post('website') !== '') { redirect(url('contact?sent=1')); } // honeypot
            $f = ['name'=>post('name'),'email'=>post('email'),'phone'=>post('phone'),'subject'=>post('subject'),'body'=>post('body')];
            if ($f['name']==='') $errs[]='Please enter your name.';
            if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errs[]='Please enter a valid e-mail address.';
            if (strlen($f['body'])<5) $errs[]='Please write a short message.';
            if ((time() - (int)($_SESSION['last_form'] ?? 0)) < 8) $errs[]='Please wait a few seconds before sending again.';
            if (!$errs) {
                ins('messages', $f + ['created'=>date('Y-m-d H:i:s'),'is_read'=>0]);
                $_SESSION['last_form'] = time();
                mail_notify('New enquiry: ' . ($f['subject'] ?: 'Excel Paces'), "Name: {$f['name']}\nEmail: {$f['email']}\nPhone: {$f['phone']}\n\n{$f['body']}", $f['email']);
                redirect(url('contact?sent=1'));
            }
            $d['old'] = $f;
        }
        $d['sent'] = isset($_GET['sent']); $d['errs'] = $errs; render('contact', $d); break;
    case 'apply':
        $errs = [];
        $d['courses'] = all("SELECT * FROM courses WHERE active=1 AND status IN ('open','full') AND start_date >= ? ORDER BY start_date", [date('Y-m-d')]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            if (post('website') !== '') { redirect(url('apply?sent=1')); }
            $hs = (array)($_POST['emp_hospital'] ?? []); $ts = (array)($_POST['emp_title'] ?? []); $ys = (array)($_POST['emp_years'] ?? []);
            $emp = [];
            foreach ($hs as $i => $h) { $h = trim((string)$h); $t = trim((string)($ts[$i] ?? '')); $y = trim((string)($ys[$i] ?? '')); if ($h !== '' || $t !== '' || $y !== '') $emp[] = ['hospital'=>mb_substr($h,0,160),'title'=>mb_substr($t,0,120),'years'=>mb_substr($y,0,30)]; if (count($emp) >= 10) break; }
            $gender = in_array(post('gender'), ['Male','Female'], true) ? post('gender') : '';
            $dob = preg_match('/^\d{4}-\d{2}-\d{2}$/', post('dob')) ? post('dob') : '';
            $f = ['course_id'=>(int)post('course_id'),'name'=>post('name'),'address'=>post('address'),'phone'=>post('phone'),'email'=>post('email'),'gender'=>$gender,'dob'=>$dob,'nationality'=>post('nationality'),'institution'=>post('institution'),'qualification'=>post('qualification'),'percentage'=>post('percentage'),'year_grad'=>post('year_grad'),'employment'=>json_encode($emp, JSON_UNESCAPED_UNICODE)];
            if ($f['name']==='') $errs[]='Please enter your name.';
            if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errs[]='Please enter a valid e-mail address.';
            if (strlen($f['phone'])<6) $errs[]='Please enter a contact number.';
            if (!isset($_POST['consent'])) $errs[]='Please tick the confirmation box.';
            if ((time() - (int)($_SESSION['last_form'] ?? 0)) < 8) $errs[]='Please wait a few seconds before sending again.';
            if (!$errs) {
                ins('applications', $f + ['created'=>date('Y-m-d H:i:s'),'status'=>'new']);
                $_SESSION['last_form'] = time();
                $empTxt = implode("\n", array_map(fn($r) => " - {$r['hospital']} | {$r['title']} | {$r['years']}", $emp));
                mail_notify('New application: ' . $f['name'], "Name: {$f['name']}\nAddress: {$f['address']}\nPhone: {$f['phone']}\nEmail: {$f['email']}\nGender: {$gender}\nDOB: {$dob}\nNationality: {$f['nationality']}\n\nInstitution: {$f['institution']}\nQualification: {$f['qualification']}\nPercentage: {$f['percentage']}\nYear graduated: {$f['year_grad']}\n\nEmployment:\n{$empTxt}", $f['email']);
                redirect(url('apply?sent=1'));
            }
            $d['old'] = $f; $d['old_emp'] = $emp ?: null;
        }
        $d['sent'] = isset($_GET['sent']); $d['errs'] = $errs; render('apply', $d); break;
    default:
        render('page', $d);
}
