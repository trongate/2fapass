<h1>Passwords</h1>
<p><button onclick="openModal('add_item')">Add New Item <i class="fa fa-plus"></i></button></p>
<p class="spinner top_margin"></p>

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

<script>
const centerStage = document.getElementsByClassName('center-stage')[0];
let items;

function fetchSitePasswords() {
  targetUrl = baseUrl + 'api/get/member_passwords';
  const http = new XMLHttpRequest();
  http.open('get', targetUrl);
  http.setRequestHeader('Content-type', 'application/json');
  http.send();
  http.onload = function() {
    console.log(http.status);
    console.log(http.responseText);
    if (http.status == 200) {
      drawItemsGrid(http.responseText);
    }
  }
}

function removeSpinner() {
  const spinners = document.getElementsByClassName('spinner');
  for (var i = spinners.length - 1; i >= 0; i--) {
    spinners[i].remove();
  }
}

function drawItemsGrid(responseText) {
  items = JSON.parse(responseText);
  if (items.length > 0) {
    console.log('we got ' + items.length);
    removeSpinner();

    // Create the items grid element
    const itemsGrid = document.createElement('div');
    itemsGrid.classList.add('items_grid');

    for (var i = 0; i < items.length; i++) {

      // Create the card element
      const card = document.createElement('div');
      card.classList.add('card');

      // Create the card body element
      const cardBody = document.createElement('div');
      cardBody.classList.add('card-body', 'parent');

      // Create the child element
      const child = document.createElement('div');
      child.classList.add('child');

      // Create the launch div element
      const launchDiv = document.createElement('div');
      launchDiv.classList.add('launch-div');

      // Create the launch button element
      const launchBtn = document.createElement('button');
      launchBtn.classList.add('launch-btn');
      launchBtn.innerText = 'Launch';
      launchBtn.setAttribute('onclick', 'launchUrl(\'' + items[i]['website_url'] + '\')');

      // Append the launch button to the launch div
      launchDiv.appendChild(launchBtn);

      // Create the button group element
      const buttonGroup = document.createElement('div');
      buttonGroup.innerHTML = `
        <div><button class="alt"><i class="fa fa-wrench"></i></button></div>
        <div><button class="alt"><i class="fa fa-users"></i></button></div>
        <div><button class="alt"><i class="fa fa-trash"></i></button></div>
      `;

      // Append the button group to the launch div
      launchDiv.appendChild(buttonGroup);

      // Append the launch div to the child element
      child.appendChild(launchDiv);

      // Create the website name element
      const websiteName = document.createElement('div');
      websiteName.classList.add('website_name');
      websiteName.innerText = items[i]['website_name'];

      // Create the website username element
      const websiteUsername = document.createElement('div');
      websiteUsername.classList.add('website_username');
      websiteUsername.innerText = items[i]['username'];

      cardBody.appendChild(child);

      if (items[i]['pic_path'] !== '') {
          // Create the image element
          var img = document.createElement('img');
          img.src = items[i]['pic_path'];
          img.alt = items[i]['website_name'];
          

          // Append the child, image, website name, and website username elements to the card body
          
          cardBody.appendChild(img);
          cardBody.appendChild(websiteName);
          cardBody.appendChild(websiteUsername);
      } else {
        cardBody.classList.add('card-body', 'use-default-pic');
        const upperDiv = document.createElement('div');
        upperDiv.classList.add('use-default-pic-upper');
        const lowerDiv = document.createElement('div');
        lowerDiv.classList.add('use-default-pic-lower');
        
        const dummyImg = document.createElement('div');
        dummyImg.classList.add('dummy-img');
        dummyImg.innerHTML = '<i class="fa fa-lock"></i>';
        dummyImg.style.backgroundColor = items[i]['cell_background'];
        upperDiv.appendChild(dummyImg);

        lowerDiv.appendChild(websiteName);
        lowerDiv.appendChild(websiteUsername);

        cardBody.appendChild(upperDiv);
        cardBody.appendChild(lowerDiv);
      }



      // Append the card body to the card element
      card.appendChild(cardBody);

      // Append the card to the items grid element
      itemsGrid.appendChild(card);

    }

    // Append the items grid to the document body
    centerStage.appendChild(itemsGrid);
  }
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

function launchUrl(targetUrl) {
  window.open(targetUrl, '_blank');
}

window.addEventListener('load', (ev) => {
  fetchSitePasswords();
});
</script>