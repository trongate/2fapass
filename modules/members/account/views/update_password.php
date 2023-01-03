<section class="container">
	<h1 class="text-center"><?= $headline ?></h1>
    <p style="text-align: center;">Your password must be at least five characters long with at least one one number</p>
    <?php
    echo validation_errors();
    $form_attr['class'] = 'narrow-form';
    echo form_open($form_location, $form_attr);
    echo form_label('New Password');
    echo form_password('password', '', array("placeholder" => "Enter Your New Password Here"));
    echo form_label('Repeat New Password');
    echo form_password('password_repeat', '', array("placeholder" => "Repeat Your New Password Here"));
    echo form_submit('submit', 'Set Password');

    if (isset($cancel_url)) {
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
    }
    
    echo form_close();
    ?>
</section>