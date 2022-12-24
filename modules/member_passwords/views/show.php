<h1><?= $headline ?> <span class="smaller hide-sm">(Record ID: <?= $update_id ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('member_passwords/manage', 'View All Member Passwords', array("class" => "button alt"));
        echo anchor('member_passwords/create/'.$update_id, 'Update Details', array("class" => "button"));
        $attr_delete = array( 
            "class" => "danger go-right",
            "id" => "btn-delete-modal",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>
<div class="three-col">
    <div class="card">
        <div class="card-heading">
            Member Password Details
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Website Name</div>
                    <div><?= $website_name ?></div>
                </div>
                <div class="row">
                    <div>Website URL</div>
                    <div><?= $website_url ?></div>
                </div>
                <div class="row">
                    <div>Username</div>
                    <div><?= $username ?></div>
                </div>
                <div class="row">
                    <div>Password</div>
                    <div><?= $password ?></div>
                </div>
            </div>
        </div>
    </div>
        <div class="card">
        <div class="card-heading">
            Picture
        </div>
        <div class="card-body picture-preview">
            <?php
            if ($draw_picture_uploader == true) {
                echo form_open_upload(segment(1).'/submit_upload_picture/'.$update_id);
                echo validation_errors();
                echo '<p>Please choose a picture from your computer and then press \'Upload\'.</p>';
                echo form_file_select('picture');
                echo form_submit('submit', 'Upload');
                echo form_close();
            } else {
            ?>
                <p class="text-center">
                    <button class="danger" onclick="openModal('delete-picture-modal')"><i class="fa fa-trash"></i> Delete Picture</button>
                </p>
                <p class="text-center">
                    <img src="<?= $picture_path ?>" alt="picture preview">
                </p>

                <div class="modal" id="delete-picture-modal" style="display: none;">
                    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Picture</div>
                    <div class="modal-body">
                        <?= form_open(segment(1).'/ditch_picture/'.$update_id) ?>
                            <p>Are you sure?</p>
                            <p>You are about to delete the picture.  This cannot be undone. Do you really want to do this?</p>
                            <p>
                                <button type="button" name="close" value="Cancel" class="alt" onclick="closeModal()">Cancel</button>
                                <button type="submit" name="submit" value="Yes - Delete Now" class="danger">Yes - Delete Now</button>
                            </p>
                        <?= form_close() ?>
                    </div>
                </div>

            <?php 
            }
            ?>
        </div>
    </div><div class="card">
        <div class="card-heading">
            Comments
        </div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt" onclick="openModal('comment-modal')">Add New Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-commenting-o"></i> Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <p><?php
            $attr_close = array( 
                "class" => "alt",
                "onclick" => "closeModal()"
            );
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
            ?>
        </p>
    </div>
</div>
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Record</div>
    <div class="modal-body">
        <?= form_open('member_passwords/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a Member Password record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>
<script>
var token = '<?= $token ?>';
var baseUrl = '<?= BASE_URL ?>';
var segment1 = '<?= segment(1) ?>';
var updateId = '<?= $update_id ?>';
var drawComments = true;
</script>