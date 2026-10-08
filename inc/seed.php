<?php
function seed_content(): void {
    $S = [
        'site_name' => 'Excel Paces', 'tagline' => 'MRCP (UK) PACES course at KIMSHEALTH, Trivandrum',
        'color_primary' => '#12348A', 'color_accent' => '#E2231A', 'color_ink' => '#0A1A44',
        'phone' => '+91 471 2941306', 'phone2' => '+91 471 2941399', 'email' => 'kimshealth@excelpaces.com', 'notify_email' => 'kimshealth@excelpaces.com',
        'address' => "Course Administrator, EXCELPACES\nKIMSHEALTH, P.B. No 1, Anayara PO\nTrivandrum, Kerala – 695029, India",
        'map_embed' => '', 'meta_desc' => 'Four-day intensive MRCP (UK) PACES preparation course organised by KIMSHEALTH, Trivandrum. Small groups, international examiner faculty, mock exam with same-day feedback.',
        'hero_stat1_n' => '4', 'hero_stat1_l' => 'intensive days',
        'hero_stat2_n' => '4', 'hero_stat2_l' => 'candidates per group',
        'hero_stat3_n' => '14', 'hero_stat3_l' => 'faculty & examiners',
        'endorse_quote' => 'Quality in everything we do. KIMSHEALTH has been instrumental in revolutionizing the art of healthcare delivery, and Excel Paces carries that commitment to every candidate who walks through our doors.',
        'endorse_name' => 'Dr. M. I. Sahadulla', 'endorse_role' => 'Chairman & Managing Director, KIMSHEALTH',
        'footer_text' => 'Excel Paces is a four-day MRCP (UK) PACES preparation course organised by KIMSHEALTH, Trivandrum.',
        'copyright' => '© ' . date('Y') . ' KIMSHEALTH. All rights reserved.',
        'apply_terms' => "Places are allocated on a first come, first served basis, and seats are limited.\nRegistration continues until all seats are filled.\nFull payment is due at registration. The fee is returned if no place can be offered.\nWritten cancellations received before 1 September receive a refund minus a 20% service charge.\nCancellations after 1 September receive no refund.\nPaid fees cannot be transferred to a later course.\nIf the course is cancelled at short notice, candidates receive a full refund.",
        'apply_note' => 'Email us to confirm your place at kimshealth@excelpaces.com. Send the application form with your money transfer reference to Ms. Jessy Ajith, KIMSHEALTH, Thiruvananthapuram.',
        'cta_title' => 'Your PACES attempt deserves the best preparation.', 'cta_text' => 'Seats are limited to keep groups small. Apply early to secure your place at the next course.',
        'federation_url' => 'https://www.thefederation.uk/about-us',
        'federation_title' => 'The only course recognised by the Federation of the Royal Colleges of Physicians of the UK',
        'federation_text' => 'The Federation of Royal Colleges of Physicians of the United Kingdom (the Royal College of Physicians of Edinburgh, the Royal College of Physicians and Surgeons of Glasgow and the Royal College of Physicians of London) has endorsed the quality of the ExcelPACES training course and provided a statement of endorsement that ExcelPACES is an ‘MRCP(UK) approved course’.',
        'partners_title' => 'A KIMSHEALTH initiative · part of the Aster DM Quality Care family',
    ];
    foreach ($S as $k => $v) set_setting($k, $v);

    $menu = [['Home','/'],['About Us','about'],['The Course','course'],['Faculty','faculty'],['Course Merit','merit'],['Testimonials','testimonials'],['Gallery','gallery'],['Contact Us','contact']];
    foreach ($menu as $i => $m) ins('menu', ['label'=>$m[0],'url'=>$m[1],'sort'=>$i+1,'active'=>1,'cta'=>0]);
    ins('menu', ['label'=>'Apply Now','url'=>'apply','sort'=>20,'active'=>1,'cta'=>1]);

    $pages = seed_pages();
    foreach ($pages as $p) ins('pages', ['slug'=>$p[0],'title'=>$p[1],'subtitle'=>$p[2],'body'=>$p[3],'image'=>'','meta_desc'=>'','active'=>1]);

    $slides = [
      ['KIMSHEALTH presents','To excel at PACES, the last hurdle to MRCP (UK)','Four intensive days of bedside teaching, small groups and examiner feedback in Trivandrum.','The Course','course'],
      ['Small groups of four','Individual attention. Same-day feedback.','Every candidate presents one-to-one and receives personal feedback from international examiners.','Meet the faculty','faculty'],
      ['Approved & endorsed','Trusted by candidates and examiners alike','Approved by the MRCP (UK) Management Board and endorsed by the Royal Colleges of London, Edinburgh and Glasgow.','Apply now','apply'],
    ];
    foreach ($slides as $i => $s) ins('slides', ['eyebrow'=>$s[0],'title'=>$s[1],'text'=>$s[2],'btn_label'=>$s[3],'btn_url'=>$s[4],'sort'=>$i+1,'active'=>1]);

    $items = [
      ['highlight','Cutting-edge & affordable','A highly advanced, cost-effective PACES preparatory course offering a comprehensive learning experience.','chip'],
      ['highlight','Best chance to clear PACES','The most promising opportunity for candidates to successfully pass the exam.','target'],
      ['highlight','Four days of intensive revision','All PACES stations covered comprehensively over four focused days.','calendar'],
      ['highlight','Highly recommended','Former attendees and examiners recommend the programme for its effectiveness and value.','award'],
      ['station','Station 1 · Respiratory & Abdominal','Two systems examined back to back. Fluent technique, clear signs, confident presentation.','lung'],
      ['station','Station 2 · History taking','Focused, structured history with the patient and a crisp summary under time pressure.','chat'],
      ['station','Station 3 · Cardiovascular & Neurological','Cardiac and neurological examinations with the reasoning examiners look for.','heart'],
      ['station','Station 4 · Communication & Ethics','Difficult conversations, consent and ethics handled with clarity and empathy.','users'],
      ['station','Station 5 · Brief clinical consultations','Two short consultations covering skin, locomotor, eye, endocrine and more. Our special emphasis.','steth'],
      ['why','Approved by MRCP (UK)','The course has gained approval from the MRCP (UK) Management Board.','shield'],
      ['why','Endorsed by the Royal Colleges','Supported by the Royal Colleges of Physicians of London, Edinburgh and Glasgow.','award'],
      ['why','International examiners','Faculty from the UK, Middle East, Malaysia, Sri Lanka and India.','globe'],
      ['why','Mock exam, same-day results','A full mock examination with feedback delivered the same day.','clock'],
    ];
    foreach ($items as $i => $t) ins('items', ['kind'=>$t[0],'title'=>$t[1],'text'=>$t[2],'icon'=>$t[3],'sort'=>$i+1,'active'=>1]);

    $facData = json_decode(file_get_contents(ROOT . '/inc/data/faculty.json'), true) ?: [];
    foreach ($facData as $i => $f) {
        $bio = '<p><strong>Qualifications:</strong> ' . htmlspecialchars($f['qual'], ENT_QUOTES) . '</p>';
        foreach ($f['bio'] as $para) $bio .= '<p>' . htmlspecialchars($para, ENT_QUOTES) . '</p>';
        ins('faculty', ['name'=>$f['name'],'role'=>$f['role'],'org'=>$f['org'],'bio'=>$bio,'photo'=>'','featured'=>in_array($f['match'], ['Sahadulla','Saifudeen','Prasad Nair','Mahadevan'], true) ? 1 : 0,'sort'=>$i+1,'active'=>1]);
    }

    $tes = json_decode(file_get_contents(ROOT . '/inc/data/testimonials.json'), true) ?: [];
    foreach ($tes as $i => $t) ins('testimonials', ['name'=>$t['n'],'role'=>'Course attendee','quote'=>$t['t'],'photo'=>'','sort'=>$i+1,'active'=>1,'email'=>'','status'=>'approved','created'=>date('Y-m-d H:i:s')]);

    ins('courses', ['title'=>'MRCP (UK) PACES Course','start_date'=>'2026-10-01','end_date'=>'2026-10-04','venue'=>'KIMSHEALTH, Anayara, Trivandrum','fee'=>'','seats'=>'Limited','status'=>'closed','note'=>'Previous course. Add the next course in the CMS (Courses & Dates) and it will appear on the home page with a live countdown.','active'=>1]);

    $partners = [
        ['Federation of the Royal Colleges of Physicians of the UK', 'assets/img/federation.svg', 'https://www.thefederation.uk/about-us'],
        ['KIMSHEALTH', '', 'https://www.kimshealth.org'],
        ['Aster DM Healthcare', '', 'https://www.asterdmhealthcare.com'],
        ['Aster Hospitals', '', 'https://www.asterhospitals.in'],
        ['CARE Hospitals', '', 'https://www.carehospitals.com'],
        ['Evercare', '', 'https://www.evercaregroup.com'],
    ];
    foreach ($partners as $i => $p) ins('partners', ['name'=>$p[0],'logo'=>$p[1],'url'=>$p[2],'sort'=>$i+1,'active'=>1]);

    $merits = [
        ['Cost-effective and advanced','A highly advanced, affordable PACES preparation programme led by dedicated, experienced examiners.','target'],
        ['A large clinical case bank','Typical PACES cases, carefully chosen to mirror the scenarios you are likely to meet in the real examination.','steth'],
        ['Examiners demonstrate the technique','See the correct approach to examination and presentation shown by working PACES examiners, then practise it yourself.','users'],
        ['Four candidates per teacher','A small-group format gives personal attention, room to ask questions and individual feedback.','chat'],
        ['The new PACES 2023 format','Every station of the updated format is covered, aligned with the latest requirements and expectations.','check'],
        ['Mock exam with same-day results','A realistic final-day mock examination, with results and feedback the same day so you can refine your skills before the real one.','clock'],
    ];
    foreach ($merits as $i => $m) ins('items', ['kind'=>'merit','title'=>$m[0],'text'=>$m[1],'icon'=>$m[2],'sort'=>$i+1,'active'=>1]);
}

function seed_pages(): array {
    return [
      ['about','About Us','The course, the people, the promise',
       "<h2>Endorsement and approval by the Royal Colleges of Physicians (UK)</h2><p>The KIMSHEALTH course has gained approval from the MRCP (UK) Management Board, along with endorsement and support from the esteemed Royal Colleges of London, Edinburgh, and Glasgow. This serves as strong evidence of the course’s quality and recognition.</p><p>The course is led by experienced Examiners hailing from the UK, Middle East, Malaysia, Sri Lanka, and India. It offers a personalized approach, enabling every candidate to undergo one-to-one presentations and receive individual feedback.</p><p>With exceptional overall pass rates, the course is backed by endorsement and support from all three Royal Colleges of London, Edinburgh, and Glasgow, which further reinforces its credibility.</p><h2>What sets this course apart</h2><p>Its faculty of experienced Examiners come from diverse backgrounds, including the UK, Middle East, Malaysia, Sri Lanka, and India. Their wealth of knowledge and expertise ensures that candidates receive guidance from professionals who understand the intricacies of the MRCP examination.</p><p>One of the key advantages of the course is the personalized approach it offers. Each candidate has the opportunity to engage in one-to-one presentations with the faculty. This individualized attention allows for focused discussions, tailored feedback, and targeted support to address specific areas of improvement.</p><h2>We are KIMSHEALTH</h2><h3>Our Vision</h3><p>“To be a leading healthcare organization of excellence that transforms lives through exceptional care.”</p><h3>Our Values</h3><p>Compassion, Affordability, Ethics, Quality, Excellence, Transparency, Innovation, Trust.</p><h3>Our Mission</h3><p>“To provide high quality cost effective care with courtesy, compassion and competence.”</p>"],
      ['course','The Course','Four days. Five stations. One focused goal.',
       "<p>Excel Paces is a four-day comprehensive MRCP (UK) PACES training programme organised by KIMSHEALTH in Trivandrum, Kerala, built as the final-stage preparation for the last hurdle to MRCP (UK).</p><p>During the initial three days of this course, students receive intensive instruction on PACES stations 1, 2, 3, 4 and 5. The course culminates with a Mock Exam on the final day, allowing participants to put their knowledge to the test. Additionally, in the mornings, there are dedicated “special teaching sessions” that focus on selected topics.</p><ul><li>Four days of intensive revision course covering all PACES stations</li><li>International panel of highly experienced PACES tutors and examiners as faculty</li><li>Structured bedside teaching with emphasis on examination techniques, presentation and management</li><li>Focused attention as small groups, individuals and good variety of cases</li><li>Guidance to achieve the new pass standards in each of the required skills assessed in PACES exam</li><li>Mock examination with results and feedback on the same day</li><li>Continuous updating of the course with latest examination guidelines</li></ul><p><strong>Next course:</strong> October 1, 2, 3 &amp; 4, 2026 (Thursday, Friday, Saturday &amp; Sunday, 4 days).</p><p>A detailed course timetable will be provided on registration.</p>"],
      ['merit','Course Merit','Six reasons candidates choose Excel Paces',
       "<p>This course is designed as a highly advanced and cost-effective preparation programme for the PACES examination. Led by dedicated and experienced examiners, it offers comprehensive training to aspiring candidates.</p><p>In summary, this advanced and cost-effective PACES preparatory course provides a comprehensive learning experience. Led by experienced examiners, it offers a diverse clinical case bank, expert demonstrations of examination techniques and presentation skills, personalized small-group training, and complete coverage of the PACES 2023 format. The course concludes with a realistic mock examination and immediate feedback to enhance candidates’ readiness for the actual PACES examination.</p>"],
      ['venue','Venue & Resources','Getting here and what to bring',
       "<h2>Where the course is held</h2><p><strong>KIMSHEALTH</strong><br>P.B. No 1, Anayara PO,<br>Trivandrum, Kerala – 695029, India<br>Telephone: +91 471 2941306 / 2941399<br>Email: kimshealth@excelpaces.com</p><h2>Travel and accommodation</h2><ul><li>Trivandrum International Airport is 10 km and the main bus station and railway station are 8 km away from KIMSHEALTH.</li><li>There are excellent hotels (3 to 5 star) in Trivandrum city, all within a 30 minute drive of the course venue.</li><li>Taxis, buses, three-wheelers and Uber are always available for transportation in the city and to commute from the hotel to the course venue.</li><li>KIMSHEALTH can arrange accommodation for candidates attending Excelpaces on prior request in a nearby hotel (at the expense of the candidate).</li><li>For those who want to do something exciting and explore Kerala, God’s Own Country, excellent sight-seeing tours and boating facilities in the nearby backwaters are available.</li></ul><h2>Good to know</h2><ul><li><strong>Weather:</strong> Pleasant at this time of the year.</li><li><strong>Communication:</strong> Kerala is a highly literate state and most people can communicate in English.</li><li><strong>Currency:</strong> Indian Rupees. Most hotels and shops accept international credit cards. It would be handy to carry some local currency for small transactions.</li></ul>"],
      ['downloads','Downloads','Application form and course documents',
       "<p><strong>MRCP PACES course application form</strong></p><p>Please complete the online registration on the <a href=\"/apply\">Apply Now</a> page. Upon receipt of the completed application by email, we will send you the bank transfer details for the money transfer.</p><p><a class=\"btn btn-accent\" href=\"https://excelpaces.com/wp-content/uploads/2023/05/EXCELPACES-Application-Form-Year-2023.doc\" target=\"_blank\" rel=\"noopener\">Download the application form (.doc)</a></p>"],
      ['faculty','Our Faculty','Examiners and tutors from the UK, Middle East, Malaysia, Sri Lanka and India','<p>Learn from clinicians who examine, teach and practise at the highest level.</p>'],
      ['testimonials','Candidate Testimonials','In the words of doctors who attended','<p>Feedback from candidates who have attended the course.</p>'],
      ['gallery','Media Gallery','Moments from the course','<p>A look inside the Excel Paces course.</p>'],
      ['contact','Contact Us','We usually reply within one working day','<p>Please contact us through email or phone for matters regarding the Excel Paces course.</p><p><strong>All written correspondence to:</strong><br>Course Administrator, EXCELPACES<br>KIMSHEALTH<br>P.B. No 1, Anayara PO<br>Trivandrum, Kerala – 695029, India</p>'],
      ['apply','Apply Now','Reserve your place at the next course','<p>Complete the form and our team will confirm your seat and share payment details.</p>'],
    ];
}
