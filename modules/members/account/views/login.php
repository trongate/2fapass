<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>members_module/css/login.css">
	<title>Member Login</title>
</head>
<body>
	
<div>
	<?php
	echo form_open('members-account/submit_login');
	echo '<div class="form-body">';
    validation_errors();
    echo form_label('Username or Email Address');
    echo form_input('username', '', array('placeholder' => 'Enter username or email address'));
	
	echo form_label('Password');
	echo form_password('password', '', array('placeholder' => 'Enter password here'));

    echo '<div>Remember me: ';
    echo form_checkbox('remember', 1, $remember);
    echo '</div>';

    echo form_submit('submit', 'Login');
    echo anchor(BASE_URL, 'Cancel', array('class' => 'button alt'));    
    echo '</div>';
    ?>
    <div class="form-btm">
        <div><?= anchor('members/forgot_password', 'Forgotten your password?') ?></div>
        <div><?= anchor('members/join', 'Not a member?') ?></div>
    </div>
    <?php
    echo form_close();
    echo '<p>';
    echo anchor(BASE_URL, '&#8592; Return To Homepage');
    echo '</p>';
    ?>
</div>


</body>
</html>