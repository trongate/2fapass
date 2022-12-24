<p>Dear <?= $member_obj->first_name ?> <?= $member_obj->last_name ?>,</p>
<p>Thank you for creating an account with us at <?= OUR_NAME ?>.  To activate your account, please goto the following URL:</p>
<p><a href="<?= $activate_url ?>"><?= $activate_url ?></a></p>
<p>If you have received this email in error then please accept our apologies.</p>
<p>Regards,</p>
<p><?= OUR_NAME ?></p>