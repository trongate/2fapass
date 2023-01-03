<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Item Picture Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Entity Name');
        echo form_input('entity_name', $entity_name, array("placeholder" => "Enter Entity Name"));
        echo form_label('URL Identifier String');
        echo form_input('url_identifier_string', $url_identifier_string, array("placeholder" => "Enter URL Identifier String"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>