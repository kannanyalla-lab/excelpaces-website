<?php
/* One-time content update (content_v 3): brings an already-installed site up to date with the full
   content of the original excelpaces.com. Safe to run repeatedly. It never touches images or CMS-only data. */
require_once __DIR__ . '/seed.php';

function content_update_v3(): void {
    // settings: only fill what is empty
    foreach ([
        'federation_url' => 'https://www.thefederation.uk/about-us',
        'federation_title' => 'The only course recognised by the Federation of the Royal Colleges of Physicians of the UK',
        'federation_text' => 'The Federation of Royal Colleges of Physicians of the United Kingdom (the Royal College of Physicians of Edinburgh, the Royal College of Physicians and Surgeons of Glasgow and the Royal College of Physicians of London) has endorsed the quality of the ExcelPACES training course and provided a statement of endorsement that ExcelPACES is an ‘MRCP(UK) approved course’.',
    ] as $k => $v) if (setting($k) === '') set_setting($k, $v);
    if (str_starts_with(setting('address'), 'P.B. No. 1')) set_setting('address', "Course Administrator, EXCELPACES\nKIMSHEALTH, P.B. No 1, Anayara PO\nTrivandrum, Kerala – 695029, India");

    // pages: replace the short placeholder text; keep image and any page already rewritten in the CMS (long body)
    foreach (seed_pages() as $p) {
        $row = one('SELECT id, body FROM pages WHERE slug=?', [$p[0]]);
        if (!$row) { ins('pages', ['slug'=>$p[0],'title'=>$p[1],'subtitle'=>$p[2],'body'=>$p[3],'image'=>'','meta_desc'=>'','active'=>1]); continue; }
        if (in_array($p[0], ['about','course','merit','venue','contact'], true) && mb_strlen(strip_tags($row['body'])) < 600)
            upd('pages', (int)$row['id'], ['subtitle'=>$p[2],'body'=>$p[3]]);
    }

    // faculty: full bios (photos untouched)
    $fac = json_decode(@file_get_contents(ROOT . '/inc/data/faculty.json'), true) ?: [];
    foreach ($fac as $f) {
        $row = one('SELECT id, bio FROM faculty WHERE name LIKE ?', ['%' . $f['match'] . '%']);
        if (!$row || trim((string)$row['bio']) !== '') continue;
        $bio = '<p><strong>Qualifications:</strong> ' . htmlspecialchars($f['qual'], ENT_QUOTES) . '</p>';
        foreach ($f['bio'] as $para) $bio .= '<p>' . htmlspecialchars($para, ENT_QUOTES) . '</p>';
        upd('faculty', (int)$row['id'], ['bio'=>$bio,'role'=>$f['role'],'org'=>$f['org']]);
    }

    // testimonials: restore full text of shortened ones, add the missing ones
    $tes = json_decode(@file_get_contents(ROOT . '/inc/data/testimonials.json'), true) ?: [];
    $sort = (int)val('SELECT COALESCE(MAX(sort),0) FROM testimonials WHERE sort<900') + 1;
    foreach ($tes as $t) {
        $norm = fn($x) => preg_replace('/\s+/', ' ', trim($x));
        $found = false;
        foreach (all('SELECT id, quote FROM testimonials WHERE name=?', [$t['n']]) as $r) {
            $q = $norm($r['quote']); $full = $norm($t['t']);
            if ($q === $full) { $found = true; if ($r['quote'] !== $t['t']) upd('testimonials', (int)$r['id'], ['quote'=>$t['t']]); break; }
            if ($q !== '' && str_starts_with($full, rtrim($q, '.…')) ) { upd('testimonials', (int)$r['id'], ['quote'=>$t['t']]); $found = true; break; }
        }
        if (!$found) ins('testimonials', ['name'=>$t['n'],'role'=>'Course attendee','quote'=>$t['t'],'photo'=>'','sort'=>$sort++,'active'=>1,'email'=>'','status'=>'approved','created'=>date('Y-m-d H:i:s')]);
    }

    // merit cards
    if ((int)val("SELECT COUNT(*) FROM items WHERE kind='merit'") === 0) {
        foreach ([
            ['Cost-effective and advanced','A highly advanced, affordable PACES preparation programme led by dedicated, experienced examiners.','target'],
            ['A large clinical case bank','Typical PACES cases, carefully chosen to mirror the scenarios you are likely to meet in the real examination.','steth'],
            ['Examiners demonstrate the technique','See the correct approach to examination and presentation shown by working PACES examiners, then practise it yourself.','users'],
            ['Four candidates per teacher','A small-group format gives personal attention, room to ask questions and individual feedback.','chat'],
            ['The new PACES 2023 format','Every station of the updated format is covered, aligned with the latest requirements and expectations.','check'],
            ['Mock exam with same-day results','A realistic final-day mock examination, with results and feedback the same day so you can refine your skills before the real one.','clock'],
        ] as $i => $m) ins('items', ['kind'=>'merit','title'=>$m[0],'text'=>$m[1],'icon'=>$m[2],'sort'=>$i+1,'active'=>1]);
    }

    // footer logos: Federation first, links on all
    $links = ['KIMSHEALTH'=>'https://www.kimshealth.org','Aster DM'=>'https://www.asterdmhealthcare.com','Aster Hospitals'=>'https://www.asterhospitals.in','CARE'=>'https://www.carehospitals.com','evercare'=>'https://www.evercaregroup.com'];
    foreach ($links as $needle => $url) q("UPDATE partners SET url=? WHERE name LIKE ? AND (url IS NULL OR url='')", [$url, '%' . $needle . '%']);
    if ((int)val("SELECT COUNT(*) FROM partners WHERE url LIKE '%thefederation.uk%'") === 0) {
        q('UPDATE partners SET sort=sort+1');
        ins('partners', ['name'=>'Federation of the Royal Colleges of Physicians of the UK','logo'=>'assets/img/federation.svg','url'=>'https://www.thefederation.uk/about-us','sort'=>1,'active'=>1]);
    }
}
