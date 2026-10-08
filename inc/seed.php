<?php
function seed_content(): void {
    $S = [
        'site_name' => 'Excel Paces', 'tagline' => 'MRCP (UK) PACES course at KIMSHEALTH, Trivandrum',
        'color_primary' => '#12348A', 'color_accent' => '#E2231A', 'color_ink' => '#0A1A44',
        'phone' => '+91 471 2941306', 'phone2' => '+91 471 2941399', 'email' => 'kimshealth@excelpaces.com', 'notify_email' => 'kimshealth@excelpaces.com',
        'address' => "P.B. No. 1, Anayara P.O.\nTrivandrum, Kerala, India",
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
        'partners_title' => 'A KIMSHEALTH initiative · part of the Aster DM Quality Care family',
    ];
    foreach ($S as $k => $v) set_setting($k, $v);

    $menu = [['Home','/'],['About Us','about'],['The Course','course'],['Faculty','faculty'],['Course Merit','merit'],['Testimonials','testimonials'],['Gallery','gallery'],['Contact Us','contact']];
    foreach ($menu as $i => $m) ins('menu', ['label'=>$m[0],'url'=>$m[1],'sort'=>$i+1,'active'=>1,'cta'=>0]);
    ins('menu', ['label'=>'Apply Now','url'=>'apply','sort'=>20,'active'=>1,'cta'=>1]);

    $pages = [
      ['about','About Us','The course, the people, the promise',
       "<p>The KIMSHEALTH Excel Paces course has gained approval from the MRCP (UK) Management Board and is supported and endorsed by the Royal Colleges of Physicians of London, Edinburgh and Glasgow.</p><p>The course is led by experienced examiners hailing from the UK, the Middle East, Malaysia, Sri Lanka and India, and is designed so that every candidate undergoes one-to-one presentations and receives individual feedback.</p><h3>Our vision</h3><p>To be a leading healthcare organisation of excellence that transforms lives through exceptional care.</p><h3>Our mission</h3><p>To provide high quality, cost effective care with courtesy, compassion and competence.</p><h3>Our values</h3><p>Compassion, Affordability, Ethics, Quality, Excellence, Transparency, Innovation, Trust.</p>"],
      ['course','The Course','Four days. Five stations. One focused goal.',
       "<p>Excel Paces is a four-day comprehensive MRCP (UK) PACES training programme organised by KIMSHEALTH in Trivandrum, Kerala, built as the final-stage preparation for the last hurdle to MRCP (UK).</p><ul><li>Four days of intensive revision covering all PACES stations</li><li>An international panel of experienced PACES tutors and examiners</li><li>Bedside teaching focused on examination technique, presentation and management</li><li>Small-group and individual attention across a wide range of cases</li><li>Guidance on meeting the current pass standards for each assessed skill</li><li>A mock examination, with results and feedback the same day</li><li>Content updated to match the latest examination guidelines</li></ul>"],
      ['merit','Course Merit','Why candidates choose Excel Paces',
       "<ul><li>A highly advanced, cost-effective PACES preparatory course</li><li>Dedicated, experienced senior PACES tutors and examiners</li><li>Demonstration of examination technique and presentation skills</li><li>Carefully chosen clinical cases and surrogates, with particular emphasis on Station 5</li><li>Small groups of four candidates taught in a high-intensity PACES-style format</li><li>Practice and feedback opportunities, plus a mock examination with same-day results</li><li>Ongoing updates to match the current MRCP PACES format</li></ul>"],
      ['venue','Venue & Resources','Getting here and what to bring',
       "<p>The course is held at KIMSHEALTH, Anayara, Trivandrum, Kerala. Edit this page in the CMS to add travel, accommodation and resource information.</p>"],
      ['faculty','Our Faculty','Examiners and tutors from the UK, Middle East, Malaysia, Sri Lanka and India','<p>Learn from clinicians who examine, teach and practise at the highest level.</p>'],
      ['testimonials','Candidate Testimonials','In the words of doctors who attended','<p>Feedback from candidates who have attended the course.</p>'],
      ['gallery','Media Gallery','Moments from the course','<p>A look inside the Excel Paces course.</p>'],
      ['contact','Contact Us','We usually reply within one working day','<p>Questions about dates, fees, eligibility or accommodation? Send us a message or call.</p>'],
      ['apply','Apply Now','Reserve your place at the next course','<p>Complete the form and our team will confirm your seat and share payment details.</p>'],
    ];
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

    $fac = [
      ['Dr. M. I. Sahadulla','Chairman and Managing Director','KIMSHEALTH',1],
      ['Dr. A. Saifudeen','Senior Consultant Physician','KIMSHEALTH · Internal Medicine and Benign Haematology',1],
      ['Dr. Prasad Nair','Senior Consultant Nephrologist','',1],
      ['Dr. Venkat Mahadevan','Consultant Physician in General Medicine','James Paget University Hospital, UK',1],
      ['Dr. Ajit Thomas','Consultant in Internal and Acute Medicine','KIMSHEALTH',0],
      ['Dr. Donald L. Farquhar','Consultant General Physician','',0],
      ['Dr. Jacqueline Taylor','Consultant Physician in Medicine','',0],
      ['Dr. Eric Livingston','Vice President (Medical), RCPSG','',0],
      ['Dr. Elizabeth A. Murphy','Consultant Rheumatologist','',0],
      ['Dr. Manoj Pazhampallil Mathews','Senior Consultant Physician','',0],
      ['Dr. Shaji Mohamed Haneef','Senior Consultant in Internal Medicine','KIMSHEALTH, Trivandrum',0],
      ['Prof. Mathew Thomas','Professor of Medicine','KIMSHEALTH Trivandrum and CSI Medical College',0],
      ['Prof. Panduka Karunanayake','Professor in Clinical Medicine','University of Colombo, Sri Lanka',0],
      ['Prof. Sunil Bhandari','Consultant in Nephrology','',0],
    ];
    foreach ($fac as $i => $f) ins('faculty', ['name'=>$f[0],'role'=>$f[1],'org'=>$f[2],'bio'=>'','photo'=>'','featured'=>$f[3],'sort'=>$i+1,'active'=>1]);

    $tes = [
      ['Dr Venmanassery Sreejan Gopinath','The course is well designed for one exam going candidate.'],
      ['Dr Arunima Kushari','This course really helped me a lot to get better understanding of the exam.'],
      ['Dr Krutika Kale','Really valued the insight & feedback given by the examiners.'],
      ['Dr Abdul Rasheed Kothur','Highly recommended to those who attend PACES.'],
      ['Dr Moses John Wesley','This is one of the best PACES courses I came across. Highly organized.'],
      ['Dr Nithin Prakash','An excellent course. Anyone planning to do PACES should attend.'],
      ['Dr Mosaab Khalil','Very well-organized course. Well selected cases.'],
      ['Dr Prasanth Kumar','Genuine intention to help MRCP aspirants.'],
      ['Dr Sankar Nath Jha','Great course done by national & International faculties.'],
      ['Dr. Shahna Subair','Truly custom-made course to give a clear understanding of PACES Exam'],
    ];
    foreach ($tes as $i => $t) ins('testimonials', ['name'=>$t[0],'role'=>'Course attendee','quote'=>$t[1],'photo'=>'','sort'=>$i+1,'active'=>1,'email'=>'','status'=>'approved','created'=>date('Y-m-d H:i:s')]);

    ins('courses', ['title'=>'MRCP (UK) PACES Course','start_date'=>'2026-10-01','end_date'=>'2026-10-04','venue'=>'KIMSHEALTH, Anayara, Trivandrum','fee'=>'','seats'=>'Limited','status'=>'closed','note'=>'Previous course. Add the next course in the CMS (Courses & Dates) and it will appear on the home page with a live countdown.','active'=>1]);

    foreach ([['KIMSHEALTH',''],['Aster DM Quality Care',''],['Aster Hospitals',''],['CARE Hospitals',''],['evercare','']] as $i => $p)
        ins('partners', ['name'=>$p[0],'logo'=>'','url'=>'','sort'=>$i+1,'active'=>1]);
}
