<section class="container">
	<h1>Update Your Details</h1>
    <?php
    validation_errors();
    $form_attr['class'] = 'narrow-form';
    echo form_open($form_location, $form_attr);

    echo form_label('Username');
    $attr['placeholder'] = 'Enter your username or email address here';
    echo form_input('username', $username, $attr);

    echo form_label('First Name');
    $attr['placeholder'] = 'Enter your first name here';
    echo form_input('first_name', $first_name, $attr);

    echo form_label('Last Name');
    $attr['placeholder'] = 'Enter your last name here';
    echo form_input('last_name', $last_name, $attr);

    echo form_label('Email Address');
    $attr['placeholder'] = 'Enter your email address here';
    echo form_email('email_address', $email_address, $attr);

    echo form_submit('submit', 'Submit');
    echo anchor('members-account/your_account', 'Cancel', array('class' => 'button alt'));
    echo form_close();
    ?>
</section>