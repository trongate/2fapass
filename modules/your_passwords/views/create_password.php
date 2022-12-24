<div class="container-md">
<h1><?= $headline ?></h1>
<div class="card">
    <div class="card-heading">
        Site Password Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location, array('class' => 'highlight-errors'));
        echo form_label('Website Name');
        echo validation_errors('website_name');
        echo form_input('website_name', $website_name, array("autocomplete" => "off", "placeholder" => "Enter Website Name"));
        echo form_label('Website URL');
        echo validation_errors('website_url');
        echo form_input('website_url', $website_url, array("autocomplete" => "off", "placeholder" => "Enter Website URL"));
        echo form_label('Username');
        echo validation_errors('username');
        echo form_input('username', $username, array("autocomplete" => "off", "placeholder" => "Enter Username"));
        echo form_label('Password');
        echo validation_errors('password');
        echo form_input('password', $password, array("autocomplete" => "off", "placeholder" => "Enter Password"));

        echo '<div class="text-right">';
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_submit('submit', 'Submit');
        echo '</div>';
        echo form_close();
        ?>
    </div>
</div>
</div>