<section class="phead"><div class="wrap"><p class="eyebrow"><a href="<?= e(url()) ?>">Home</a> / Apply</p><h1><?= e($page['title']) ?></h1><p class="lead"><?= e($page['subtitle']) ?></p></div></section>
<section class="sec"><div class="wrap two">
  <div>
    <div class="prose"><?= clean_html($page['body']) ?></div>
    <div class="steps"><div><i>1</i><span>Submit this form</span></div><div><i>2</i><span>We confirm your seat by e-mail</span></div><div><i>3</i><span>Pay and receive joining details</span></div></div>
  <?php if (setting('apply_terms')): ?><div class="terms"><h3>Terms &amp; conditions</h3><ul><?php foreach (preg_split('/\R+/', setting('apply_terms')) as $t): if (trim($t) !== ''): ?><li><?= e(trim($t)) ?></li><?php endif; endforeach; ?></ul><?php if (setting('apply_note')): ?><p><?= e(setting('apply_note')) ?></p><?php endif; ?></div><?php endif; ?>
  </div>
  <div class="formcard">
    <?php if ($sent): ?><div class="notice ok"><b>Application received.</b> Thank you. Our team will contact you shortly to confirm your seat.</div>
    <?php else: ?>
    <?php foreach ($errs as $x): ?><div class="notice err"><?= e($x) ?></div><?php endforeach; ?>
    <form method="post" class="form"><?= csrf_field() ?>
      <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
      <?php if ($courses): ?>
      <label>Course<select name="course_id"><?php foreach ($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)($old['course_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['title'] . ' · ' . date_range($c['start_date'], $c['end_date'])) ?><?= $c['status'] === 'full' ? ' (waiting list)' : '' ?></option><?php endforeach; ?></select></label>
      <?php else: ?><input type="hidden" name="course_id" value="0"><div class="notice info">No dates are open right now. Submit this form to register your interest.</div><?php endif; ?>
      <p class="fs-t">Personal details</p>
      <label>Name<input name="name" required value="<?= e($old['name'] ?? '') ?>"></label>
      <label>Address<textarea name="address" rows="3"><?= e($old['address'] ?? '') ?></textarea></label>
      <div class="g2"><label>Phone number<input name="phone" type="tel" required value="<?= e($old['phone'] ?? '') ?>"></label><label>Email ID<input type="email" name="email" required value="<?= e($old['email'] ?? '') ?>"></label></div>
      <div class="g2"><div class="fieldset"><span class="lbl">Gender</span><div class="radios"><label><input type="radio" name="gender" value="Male" <?= ($old['gender'] ?? '') === 'Male' ? 'checked' : '' ?>> Male</label><label><input type="radio" name="gender" value="Female" <?= ($old['gender'] ?? '') === 'Female' ? 'checked' : '' ?>> Female</label></div></div>
      <label>Date of birth<input type="date" name="dob" value="<?= e($old['dob'] ?? '') ?>"></label></div>
      <label>Nationality<input name="nationality" value="<?= e($old['nationality'] ?? '') ?>"></label>
      <p class="fs-t">Academic details</p>
      <label>Educational institution<input name="institution" value="<?= e($old['institution'] ?? '') ?>"></label>
      <div class="g2"><label>Qualification<input name="qualification" value="<?= e($old['qualification'] ?? '') ?>"></label><label>Percentage score<input name="percentage" value="<?= e($old['percentage'] ?? '') ?>"></label></div>
      <label>Year graduated<input name="year_grad" inputmode="numeric" maxlength="4" value="<?= e($old['year_grad'] ?? '') ?>"></label>
      <p class="fs-t">Employment history</p>
      <div class="emp" id="emp">
        <?php $rows = $old_emp ?? [['hospital'=>'','title'=>'','years'=>'']]; foreach ($rows as $r): ?>
        <div class="emp-row"><input name="emp_hospital[]" placeholder="Hospital name" value="<?= e($r['hospital']) ?>"><input name="emp_title[]" placeholder="Job title" value="<?= e($r['title']) ?>"><input name="emp_years[]" placeholder="Years" value="<?= e($r['years']) ?>"><button type="button" aria-label="Remove row">−</button></div>
        <?php endforeach; ?>
        <button type="button" class="emp-add">+ Add another post</button>
      </div>
      <label class="chk"><input type="checkbox" name="consent" value="1" <?= !empty($_POST['consent']) ? 'checked' : '' ?>> I hereby certify that the facts furnished above are true and correct to the best of my knowledge and belief.</label>
      <button class="btn btn-accent block">Send <?= icon('arrow') ?></button>
    </form>
    <?php endif; ?>
  </div>
</div></section>
