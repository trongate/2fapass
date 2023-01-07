<h1>Passwords</h1>
<p><button onclick="openCustomModal('add_item')">Add New Item <i class="fa fa-plus"></i></button></p>
<p class="spinner top_margin"></p>

<div class="modal" id="create_password" style="display:none">


    <div class="modal-heading two-way-split">
        <div><i class="fa fa-lock"></i>  Add Password</div>
        <div class="logo-font"><?= OUR_NAME ?></div>
        <div><i class="fa fa-times" onclick="closeModal()"></i></div>
    </div>
    <div class="modal-body">
        <form class="password-form">
            <div>
                <label>URL:</label>
                <input type="text" id="website-url" value="" placeholder="Enter full website login URL here..." autocomplete="off">
            </div>
            <div class="two-col">
                <div>
                    <label>Website Name:</label>
                    <input type="text" id="website-name" value="" placeholder="Enter website name here...">
                </div>
                <div>
                    <label>Folder:</label>
                    <select id="folder-dropdown" name="folder">
                    <option value="" selected="">Select folder...</option>
                    </select>
                </div>
            </div>
            <div class="two-col">
                <div>
                    <label>Username:</label>
                    <input type="text" id="username" value="" placeholder="Enter username here...">
                </div>
                <div>
                    <label>Site password:</label>
                    <input type="text" id="password" value="" placeholder="Enter site password here..." autocomplete="off">
                </div>
            </div>
            <div>
                <label>Notes:</label>
                <textarea id="notes" placeholder="Use this space to enter any notes relating to this record..."></textarea>
            </div>
            <p class="text-right top_divider">
                <button type="button" name="cancel" value="Cancel" class="cancel-btn button alt" onclick="closeModal()">Cancel</button>
                <button type="button" name="submit" id="submit-btn" value="Save" onclick="saveItem('password')">Save</button>
            </p>
        </form>
    </div>
</div>

<div class="modal" id="add_item" style="display:none">
    <div class="modal-heading two-way-split">
        <div>Add Item</div>
        <div class="logo-font"><?= OUR_NAME ?></div>
        <div><i class="fa fa-times" onclick="closeModal()"></i></div>
    </div>
    <div class="modal-body">
        <div class="modal-body-grid">
            <div onclick="openAddItem('password')">
                <div><i class="fa fa-lock"></i></div>
                <div>password</div>
            </div>
            <div onclick="openAddItem('secure note')">
                <div><i class="fa fa-file-text-o"></i></div>
                <div>secure note</div>
            </div>
            <div onclick="openAddItem('address')">
                <div><i class="fa fa-address-book"></i></div>
                <div>address</div>
            </div>
            <div onclick="openAddItem('payment card')">
                <div><i class="fa fa-credit-card"></i></div>
                <div>payment card</div>
            </div>
            <div onclick="openAddItem('bank account')">
                <div><i class="fa fa-bank"></i></div>
                <div>bank account</div>
            </div>
        </div>
    </div>
</div>

<div id="add_btn">
    <button onclick="openModal('add_item')"><i class="fa fa-plus"></i></button>
</div>