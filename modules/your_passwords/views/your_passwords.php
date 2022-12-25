<h1>Passwords</h1>
<?= flashdata() ?>
<p>
    <button onclick="openModal('add_item')">Add New Item <i class="fa fa-plus"></i></button>
</p>
<div class="items_grid">
    <?php
    foreach($items as $item) {
       ?>
        <div class="card">
            <div class="card-body parent">

  <div class="child">
    <div class="launch-div">
        <div>
            <button class="launch-btn">Launch</button>    
        </div>
        <div>
            <div><button class="alt"><i class="fa fa-wrench"></i></button></div>
            <div><button class="alt"><i class="fa fa-users"></i></button></div>
            <div><button class="alt"><i class="fa fa-trash"></i></button></div>
        </div>
        
    </div>
  </div>


                <div><?php
                if ($item->pic_path !== '') {
                    echo '<img src="'.$item->pic_path.'" alt="'.$item->website_name.'">';
                } else {
                    $cell_bg = $item->cell_background;
                    echo '<div class="default_icon" style="background-color: '.$cell_bg.'"><i class="fa fa-lock"></i></div>';
                }
                ?>
                </div>
                <div class="website_name"><?= $item->website_name ?></div>
                <div class="website_username"><?= $item->username ?></div>            
            </div>
        </div>
       <?php
    }
    ?>
</div>

<style>
.parent {
  position: relative;
}

.child {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 99;
  visibility: hidden;  /* The child element is initially hidden */
}

.parent:hover .child {
  visibility: visible;  /* The child element is shown when the parent element is hovered over */
}

</style>

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


<div class="modal" id="create_password" style="display:none">


    <div class="modal-heading two-way-split">
        <div><i class="fa fa-lock"></i>  Add Password</div>
        <div class="logo-font"><?= OUR_NAME ?></div>
        <div><i class="fa fa-times" onclick="closeModal()"></i></div>
    </div>

    <div class="modal-body">
        <?php
        echo form_open('your_passwords/submit', array('class' => 'password-form'));

        echo '<div>';
        echo form_label('URL:');
        echo form_input('website_url', '');
        echo '</div>';

        echo '<div class="two-col">';
            echo '<div>';
            echo form_label('Name:');
            echo form_input('website_name', '');
            echo '</div>';
            echo '<div>';
            echo form_label('Folder:');
            echo form_input('folder', '');
            echo '</div>';
        echo '</div>';

        echo '<div class="two-col">';
            echo '<div>';
            echo form_label('Username:');
            echo form_input('website_name', '');
            echo '</div>';
            echo '<div>';
            echo form_label('Site password:');
            echo form_input('folder', '');
            echo '</div>';
        echo '</div>';

        echo '<div>';
        echo form_label('Notes:');
        echo form_textarea('notes', '');
        echo '</div>';

    
        echo '<p class="text-right top_divider">';
        $cancel_attr['class'] = 'button alt';
        $cancel_attr['onclick'] = 'closeModal()';
        echo form_button('cancel', 'Cancel', $cancel_attr);
        echo form_submit('submit', 'Save');
        echo '</p>';

        echo form_close();
        ?>
    </div>
</div>

<div class="modal" id="more_item_info" style="display:none">
    <div class="modal-heading two-way-split">
        <div><i class="fa fa-lock"></i> Password Record</div>
        <div><i class="fa fa-times" onclick="closeItemInfoModal()"></i></div>
    </div>
    <div class="modal-body">
        <p class="text-right" style="margin:0"><?php
        $edit_url = BASE_URL.'your_passwords/create';
        $link_text = '<i class="fa fa-edit"></i> <span class="sm">edit record</span>';
        echo anchor($edit_url, $link_text);
        ?></p>

        <div class="dummy-card">
            <div class="card-body">
                <div id="temp_site_logo"><img src="http://localhost/2fapass/member_passwords_module/member_passwords_pics/2/paypal_alt.png" alt="PayPal Business">                </div>           
            </div>            
        </div>

        <div id="record-details-tbl" style="display:none">
            <table>
            <tbody>
                <tr>
                    <td>URL: </td>
                    <td><a href="https://trongate.io" target="_blank">https://trongate.io</a></td>
                </tr>
                <tr>
                    <td>Site Name: </td>
                    <td>Trongate Framework</td>
                </tr>
                <tr>
                    <td>Username: </td>
                    <td>Some Username</td>
                </tr>
                <tr>
                    <td>Site Password: </td>
                    <td id="password_cell">***********</td>
                </tr>
            </tbody>
        </table>
        </div>


        <p class="standard-modal-btns">
            <button class="alt" onclick="viewRecord()"><i class="fa fa-eye"></i> View Record</button>
            <button><i class="fa fa-copy"></i> Copy Username</button>
            <button onclick="revealPassword()"><i class="fa fa-copy"></i> Copy Password</button>
        </p>
        <p class="action-btns" style="display: none">
            <button class="alt" onclick="goBackVibe()"><i class="fa fa-arrow-left"></i> Go Back</button>
            <button onclick="revealPassword()" id="reveal_password_btn" class="danger"><i class="fa fa-warning"></i> Reveal Password</button>
        </p>
    </div>
</div>

<div id="add_btn">
    <button onclick="openModal('add_item')"><i class="fa fa-plus"></i></button>
</div>

<style>
td {
    border: 1px var(--primary-dark) solid;
}
</style>

<script>
function genPass() {
    site_password.type = 'text';
    site_password.value = generatePassword(16);
}

function generatePassword(length) {
  let password = '';
  for (let i = 0; i < length; i++) {
    let randomNumber = Math.floor(Math.random() * 94) + 33;
    password += String.fromCharCode(randomNumber);
  }
  return password;
}

function adjustDefaultIcons() {
    const gridIcons = document.querySelectorAll('.items_grid > div > div > div:nth-child(1) > img');
    const defaultIcons = document.querySelectorAll('.default_icon');
    console.log(gridIcons.length);
    console.log(defaultIcons.length);

    if (defaultIcons.length>0) {
        if(gridIcons.length>0) {
            const firstGridIconShape = gridIcons[0].getBoundingClientRect();
            var targetHeight = firstGridIconShape.height;
        } else {
            var targetHeight = '76.28px';
        }

        for (var i = 0; i < defaultIcons.length; i++) {
            defaultIcons[i].style.minHeight = targetHeight + 'px';
        }
    }
}

function openMoreInfo(clickedEl) {
    openModal('more_item_info');
}

function listenForIconClicks() {
   const clickableIcons = document.querySelectorAll('.items_grid > div > div > div:nth-child(1) > img');
   for (var i = 0; i < clickableIcons.length; i++) {
       clickableIcons[i].addEventListener('click', (ev) => {
           openMoreInfo(ev.target);
       });
   }

   const clickableItemNames = document.querySelectorAll('.items_grid > div > div > div.website_name > a');
   for (var i = 0; i < clickableItemNames.length; i++) {
       clickableItemNames[i].addEventListener('click', (ev) => {
           openMoreInfo(ev.target);
       });
   }
}

function viewRecord() {
    temp_site_logo.style.display = 'none';
    const modalBody = document.querySelector('#more_item_info > div.modal-body');
    const modalBtns = document.querySelector('#more_item_info > div.modal-body > p.standard-modal-btns');
    const actionBtns = document.querySelector('#more_item_info > div.modal-body > p.action-btns');
    const recordDetailsTbl = document.getElementById('record-details-tbl');
    recordDetailsTbl.style.display = 'block';
    modalBtns.style.display = 'none';
    actionBtns.style.display = 'block';
}

function revealPassword() {
    password_cell.innerHTML = 'your password';
    password_cell.classList.add('reveal');

    setTimeout(() => {
        password_cell.classList.add('transition-to-clear');
    }, 500);

    setTimeout(() => {
        password_cell.classList.remove('reveal');
        password_cell.classList.remove('transition-to-clear');
        const origBtnInner = reveal_password_btn.innerHTML;
        const newBtnInner = origBtnInner.replace('Reveal', 'Hide');
        reveal_password_btn.innerHTML = newBtnInner;
        reveal_password_btn.removeAttribute('onclick');
        reveal_password_btn.setAttribute('onclick', 'hidePassword()');
    }, 800);
    
}

function hidePassword() {
    password_cell.innerHTML = '***********';
    password_cell.classList.add('reveal');

    setTimeout(() => {
        password_cell.classList.add('transition-to-clear');
    }, 500);

    setTimeout(() => {
        password_cell.classList.remove('reveal');
        password_cell.classList.remove('transition-to-clear');
        const origBtnInner = reveal_password_btn.innerHTML;
        const newBtnInner = origBtnInner.replace('Hide', 'Reveal');
        reveal_password_btn.innerHTML = newBtnInner;
        reveal_password_btn.removeAttribute('onclick');
        reveal_password_btn.setAttribute('onclick', 'revealPassword()');
    }, 800);
}

function goBackVibe() {
    hidePassword();
    const recordDetailsTbl = document.getElementById('record-details-tbl');
    const modalBtns = document.querySelector('#more_item_info > div.modal-body > p.standard-modal-btns');
    const actionBtns = document.querySelector('#more_item_info > div.modal-body > p.action-btns');

    recordDetailsTbl.style.display = 'none';
    modalBtns.style.display = 'block';
    actionBtns.style.display = 'none';
    const tempSiteLogo = document.getElementById('temp_site_logo');
    tempSiteLogo.style.display = 'block';

    const tempInfoEls = document.getElementsByClassName('temp-info');
    for (var i = tempInfoEls.length - 1; i >= 0; i--) {
        tempInfoEls[i].remove();
    }
}

function goToUrl(targetUrl=null) {
    window.location.href = 'https://trongate.io';
}

function closeItemInfoModal() {
    goBackVibe();
    closeModal();
}


function openAddItem(itemType, index=null) {
    switch (itemType) {
      case 'password':
        openPasswordModal();
        break;      
    }

}

function openPasswordModal() {
    closeModal();
    setTimeout(() => {
        openModal('create_password');
    }, 160);
}

function addItemBoxBtns(el) {


    
    // console.log(el.outerHTML);

    // const launchBtnDiv = document.createElement('div');
    // launchBtnDiv.setAttribute('id', 'launch_btn_div');

    // const launchBtn = document.createElement('button');
    // launchBtn.setAttribute('id', 'launch_btn');
    // launchBtn.innerHTML = 'Launch';
    // launchBtnDiv.appendChild(launchBtn);

    // el.appendChild(launchBtnDiv);

    // const itemImg = document.querySelector('body > div.wrapper > div.center-stage > div.items_grid > div:nth-child(1) > div > div:nth-child(1) > img');

    // const itemImgShape = itemImg.getBoundingClientRect();

}

function removeLaunchBtnEls() {
   // launch_btn_div.remove();
}

function listenForItemClicks() {
    const itemBoxes = document.querySelectorAll('body > div.wrapper > div.center-stage > div.items_grid > div > div');
    for (var i = 0; i < itemBoxes.length; i++) {
        itemBoxes[i].addEventListener('mouseover', function() {
          addItemBoxBtns(this);
        });
    }

    for (var i = 0; i < itemBoxes.length; i++) {
        itemBoxes[i].addEventListener('mouseout', function() {
          removeLaunchBtnEls();
        });
    }

}

window.addEventListener('load', (ev) => {
    adjustDefaultIcons();
    listenForIconClicks();
    listenForItemClicks();
     



// openModal('add_item');
//     setTimeout(() => {
//         openPasswordModal();
//     }, 400);
});
</script>