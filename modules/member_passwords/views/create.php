<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Member Password Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Website Name');
        echo form_input('website_name', $website_name, array("autocomplete" => "off", "placeholder" => "Enter Website Name"));
        echo form_label('Website URL');
        echo form_input('website_url', $website_url, array("autocomplete" => "off", "placeholder" => "Enter Website URL"));
        echo form_label('Username');
        echo form_input('username', $username, array("autocomplete" => "off", "placeholder" => "Enter Username"));
        echo form_label('Password');
        echo form_input('password', $password, array("autocomplete" => "off", "placeholder" => "Enter Password"));
        echo form_label('Associated Member');
        echo form_dropdown('members_id', $members_options, $members_id);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>