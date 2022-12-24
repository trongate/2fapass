<p>Dear <?= $member_obj->first_name ?> <?= $member_obj->last_name ?>,</p>
<p>We have received a password reset request. To reset your password, please goto the following URL:</p>
<p><a href="<?= $reset_url ?>"><?= $reset_url ?></a></p>
<p>If you have received this email in error then please accept our apologies.</p>
<p>Regards,</p>
<p><?= OUR_NAME ?></p>