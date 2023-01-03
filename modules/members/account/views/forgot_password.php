<section class="container">
    <h1>Forgot Password</h1>
    <?= validation_errors() ?>
    <p>Enter your username or email address below and then hit 'Submit'</p>
    <?php
    echo form_open('members-account/submit_forgot_password');
    echo form_label('Username or Email Address');
    echo form_input('my_vibe', '', array('placeholder' => 'Enter your username or email address here'));
    echo form_submit('submit', 'Submit');
    echo anchor(BASE_URL, 'Cancel', array('class' => 'button alt'));
    echo form_close();
    ?>	
</section>