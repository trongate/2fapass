<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Folder Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Folder Name');
        echo form_input('folder_name', $folder_name, array("placeholder" => "Enter Folder Name"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>