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
                <input type="text" id="website-url" id="website-url" value="" placeholder="Enter full website login URL here..." autocomplete="off">
            </div>
            <div class="two-col">
                <div>
                    <label>Website Name:</label>
                    <input type="text" id="website-name" value="" placeholder="Enter website name here...">
                </div>
                <div>
                    <label>Folder:</label>
                    <input type="text" id="folder" value="" placeholder="Select a folder...">
                </div>
            </div>
            <div class="two-col">
                <div>
                    <label>Username:</label>
                    <input type="text" id="username" value="" placeholder="Enter username here...">
                </div>
                <div>
                    <label>Site password:</label>
                    <input type="text" id="password" value="" placeholder="Enter site password here...">
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

<script>
const centerStage = document.getElementsByClassName('center-stage')[0];
let items;

function fetchWebsiteRecords() {
  targetUrl = baseUrl + 'api/get/website_records';
  console.log(targetUrl);
  const http = new XMLHttpRequest();
  http.open('get', targetUrl);
  http.setRequestHeader('Content-type', 'application/json');
  http.send();
  http.onload = function() {
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

function addItemToContainer(itemObj, itemsContainer) {
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
  launchBtn.setAttribute('onclick', 'launchUrl(\'' + itemObj['website_url'] + '\')');

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
  websiteName.innerText = itemObj['website_name'];

  // Create the website username element
  const websiteUsername = document.createElement('div');
  websiteUsername.classList.add('website_username');
  websiteUsername.innerText = itemObj['username'];

  cardBody.appendChild(child);

  if (itemObj['pic_path'] !== '') {
    // Create the image element
    var img = document.createElement('img');
    img.src = itemObj['pic_path'];
    img.alt = itemObj['website_name'];


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
    dummyImg.style.backgroundColor = itemObj['cell_background'];
    upperDiv.appendChild(dummyImg);

    lowerDiv.appendChild(websiteName);
    lowerDiv.appendChild(websiteUsername);

    cardBody.appendChild(upperDiv);
    cardBody.appendChild(lowerDiv);
  }

  // Append the card body to the card element
  card.appendChild(cardBody);

  // Append the card to the items grid element
  itemsContainer.appendChild(card);
}

function drawItemsGrid(responseText) {
  items = JSON.parse(responseText);
  if (items.length > 0) {
    removeSpinner();

    // Create the items grid element
    const itemsGrid = document.createElement('div');
    itemsGrid.classList.add('items_grid');

    for (var i = 0; i < items.length; i++) {
      addItemToContainer(items[i], itemsGrid);
    }

    // Append the items grid to the document body
    centerStage.appendChild(itemsGrid);
  }
}

function openAddItem(itemType, index = null) {
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

function devAutoOpenForm() {
  openModal('add_item');
  setTimeout(() => {
    openAddItem('password');
  }, 500);

  setTimeout(() => {

    document.getElementById('website-url').value = 'http://speedcodingacademy.com';
    document.getElementById('website-name').value = 'Speed Coding Academy';
    document.getElementById('username').value = 'Davcon';
    document.getElementById('password').value = 'whatever123,';
    document.getElementById('notes').value = 'Here are some notes';


  }, 1000);
}

function devAutoOpenFlashdata() {
  setTimeout(() => {
    const flashdataMsg = 'The record was successfully created.';
    const flashdataTheme = 'success';
    openFlashdata(flashdataMsg, flashdataTheme);
  }, 1000);
}

function openFlashdata(flashdataMsg, flashdataTheme) {
  console.log('running open flashdata')

  // Generate a random id for the flashdata div
  const flashdataId = Math.random().toString(36).substr(2, 9);

  const flashdataDiv = document.createElement('div');
  flashdataDiv.classList.add('flashdata', flashdataTheme);
  flashdataDiv.id = flashdataId;

  const message = document.createTextNode(flashdataMsg);

  const tick = document.createElement('i');
  tick.className = 'fa fa-check';
  tick.setAttribute('aria-hidden', 'true');

  const close = document.createElement('i');
  close.className = 'fa fa-times';
  close.setAttribute('aria-hidden', 'true');
  close.setAttribute('onclick', `removeFlashdata('${flashdataId}')`);

  flashdataDiv.appendChild(tick);
  flashdataDiv.appendChild(message);
  flashdataDiv.appendChild(close);

  document.body.appendChild(flashdataDiv);
  setTimeout(() => {
    flashdataDiv.classList.add('flashdata', 'flashdata-success', 'show');
  }, 1);

  setTimeout(() => {
    closeFlashdata(flashdataDiv);
  }, 3000);
}

function removeFlashdata(elId) {
  const flashdataDiv = document.getElementById(elId);
  closeFlashdata(flashdataDiv);
}

function closeFlashdata(flashdataDiv) {
  if (flashdataDiv) {
    flashdataDiv.classList.remove('show');
    setTimeout(() => {
      flashdataDiv.remove();
    }, 600);
  }
}

function submitForm(submitBtn, formData) {
  console.log('submitting form now');

  // Show the spinner
  submitBtn.innerHTML = '<div class="custom-spinner"></div>';
  submitBtn.disabled = true;

  // hide cancel buttons
  var cancelBtns = document.getElementsByClassName('cancel-btn');
  for (var i = 0; i < cancelBtns.length; i++) {
      cancelBtns[i].style.display = 'none';
  }

  // hide close window buttons
  var closeWindowBtns = document.querySelectorAll('.modal-heading.two-way-split > div:nth-child(3) > i');
  for (var i = 0; i < closeWindowBtns.length; i++) {
      closeWindowBtns[i].style.display = 'none';
  }

  const targetUrl = baseUrl + 'api/create/website_records';
  const http = new XMLHttpRequest();
  http.open('post', targetUrl);
  http.setRequestHeader('Content-type', 'application/json');
  http.send(JSON.stringify(formData));
  http.onload = function() {

    if(http.status == 200) {
      console.log(http.responseText);

      // Revert the button to its original state
      submitBtn.innerHTML = 'Save';
      submitBtn.disabled = false;

      for (var i = 0; i < cancelBtns.length; i++) {
          cancelBtns[i].style.display = 'inline-block';
      }

      for (var i = 0; i < closeWindowBtns.length; i++) {
          closeWindowBtns[i].style.display = 'inline-block';
      }

      const parentEl = submitBtn.closest('.modal-body');
      initSuccessClose(parentEl, 'This is a message');

      const itemsContainer = document.querySelector('body > div.wrapper > div.center-stage > div.items_grid');
      const newRecordObj = JSON.parse(http.responseText);
      addItemToContainer(newRecordObj, itemsContainer)
    }

  }
}

function submitFormORIGandWORKS(submitBtn) {
    console.log('click');
  // Show the spinner
  submitBtn.innerHTML = '<div class="custom-spinner text-primary" role="status"><span class="sr-only">Loading...</span></div>';
  submitBtn.disabled = true;

  // hide cancel buttons
  var cancelBtns = document.getElementsByClassName('cancel-btn');
  for (var i = 0; i < cancelBtns.length; i++) {
      cancelBtns[i].style.display = 'none';
  }

  // hide close window buttons
  var closeWindowBtns = document.querySelectorAll('.modal-heading.two-way-split > div:nth-child(3) > i');
  for (var i = 0; i < closeWindowBtns.length; i++) {
      closeWindowBtns[i].style.display = 'none';
  }

  // Perform the save action
  // ...
  setTimeout(() => {
      // Revert the button to its original state
      submitBtn.innerHTML = 'Save';
      submitBtn.disabled = false;

      for (var i = 0; i < cancelBtns.length; i++) {
          cancelBtns[i].style.display = 'inline-block';
      }

      for (var i = 0; i < closeWindowBtns.length; i++) {
          closeWindowBtns[i].style.display = 'inline-block';
      }

      const parentEl = submitBtn.closest('.modal-body');
      initSuccessClose(parentEl, 'This is a message');
  }, 2000);

}

function initSuccessClose(originalContainer, msg) {
  const overlay = createOverlay(originalContainer);
  overlay.setAttribute('id', 'tick-overlay');
  document.body.appendChild(overlay);
  drawBigTick(overlay);
}

function createOverlay(element) {
  const overlay = document.createElement('div');
  const rect = element.getBoundingClientRect();
  overlay.style.position = 'absolute';
  overlay.style.top = `${rect.top}px`;
  overlay.style.left = `${rect.left}px`;
  overlay.style.width = `${rect.width}px`;
  overlay.style.height = `${rect.height}px`;
  overlay.style.zIndex = 7;
  overlay.style.backgroundColor = '#fdf9f9';
  return overlay;
}

function drawBigTick(targetParentEl) {
  console.log('drawing big tick')
    targetParentEl.classList.add('text-center');
    var bigTick = document.createElement('div');
    bigTick.setAttribute('id', 'big-tick');
    bigTick.setAttribute('style', 'display: none');
    var trigger = document.createElement('div');
    trigger.setAttribute('class', 'trigger');
    bigTick.appendChild(trigger);
    var tickSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    tickSvg.setAttribute('version', '1.1');
    tickSvg.setAttribute('id', 'tick');
    tickSvg.setAttribute('style', 'margin:  0 auto; width:  53.7%; transform: scale(0.5)');
    tickSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    tickSvg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
    tickSvg.setAttribute('x', '0px');
    tickSvg.setAttribute('y', '0px');
    tickSvg.setAttribute('viewBox', '0 0 37 37');
    tickSvg.setAttribute('xml:space', 'preserve');
    bigTick.appendChild(tickSvg);

    var tickPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    tickPath.setAttribute('class', 'circ path');
    tickPath.setAttribute('style', 'fill:none;stroke:#007700;stroke-width:3;stroke-linejoin:round;stroke-miterlimit:10');
    tickPath.setAttribute('d', 'M30.5,6.5L30.5,6.5c6.6,6.6,6.6,17.4,0,24l0,0c-6.6,6.6-17.4,6.6-24,0l0,0c-6.6-6.6-6.6-17.4,0-24l0,0C13.1-0.2,23.9-0.2,30.5,6.5z');
    tickSvg.appendChild(tickPath);

    var polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
    polyline.setAttribute('class', 'tick path');
    polyline.setAttribute('style', 'fill:none;stroke:#007700;stroke-width:3;stroke-linejoin:round;stroke-miterlimit:10;');
    polyline.setAttribute('points', '11.6,20 15.9,24.2 26.4,13.8');
    tickSvg.appendChild(polyline);

   targetParentEl.appendChild(bigTick);
    
    var bigTick = document.getElementById('big-tick');
    bigTick.style.display = 'block';
    
    setTimeout(() => {
        var things = document.getElementsByClassName('trigger')[0];
        things.classList.add('drawn');
    }, 100);
    
    setTimeout(() => {
       hideBigTick();
    }, 1300);
}

function hideBigTick() {
    var things = document.getElementsByClassName('trigger')[0];
    things.classList.remove('drawn');
    var bigTick = document.getElementById('big-tick');
    bigTick.style.display = 'none';
    const tickOverlay = document.getElementById('tick-overlay');
    tickOverlay.remove();
    closeModal();
}




























function saveItem() {
  //this will EITHER produce validation errors OR post to an endpoint
  const values = readFormValues();
  if (Array.isArray(values)) {
    // Validation errors
    values.forEach((error) => {
      console.log(error);
    });
  } else {
    // No validation errors
    const submitBtn = document.getElementById('submit-btn');
    submitForm(submitBtn, values);
  }
}

function removeValidationErrors() {
  const errorAlerts = document.querySelectorAll('.validation-error-alert');
  errorAlerts.forEach(errorAlert => errorAlert.remove());

  const validationErrors = document.querySelectorAll('.validation-error-report');
  validationErrors.forEach(errorAlert => errorAlert.remove());

  const validationErrorFields = document.querySelectorAll('.form-field-validation-error');
  validationErrorFields.forEach(field => field.classList.remove('form-field-validation-error'));
}

function readFormValues() {

  removeValidationErrors();

  const form = document.querySelector('.password-form');
  const inputs = form.querySelectorAll('input');
  const textarea = form.querySelector('textarea');

  const errors = [];
  const params = {};
  for (let i = 0; i < inputs.length; i++) {
    const input = inputs[i];
    if (input.id === 'website-url' && !input.value.startsWith('http')) {
      input.classList.add('form-field-validation-error');
      const errorMsg = `The website URL must start with http`;
      errors.push(errorMsg);
      addValidationError('website-url', errorMsg);
    } else if (input.id === 'website-name' && input.value.length === 0) {
      input.classList.add('form-field-validation-error');
      const errorMsg = `The website name cannot be empty`;
      errors.push(`${input.id} cannot be empty`);
      addValidationError('website-name', errorMsg);
    } else if (input.id === 'password' && input.value.length === 0) {
      input.classList.add('form-field-validation-error');
      const errorMsg = `The password is required`;
      errors.push(`${input.id} is required`);
      addValidationError('password', errorMsg);
    } else if (input.id === 'password' && input.value.length > 64) {
      input.classList.add('form-field-validation-error');
      const errorMsg = `The password cannot be more than 64 characters in length`;
      errors.push(`${input.id} is required`);
      addValidationError('password', errorMsg);
    } else {
      input.classList.remove('form-field-validation-error');
      params[input.id] = input.value;
    }
  }

  const submittedPassword = document.getElementById('password').value;
  if (submittedPassword.length > 0) {
    checkPassword(submittedPassword);
  }

  return errors.length > 0 ? errors : params;

}

function addValidationError(formFieldId, errorMsg) {

  // Get the form field with the corresponding ID
  const field = document.getElementById(formFieldId);
  // Get the label for the form field
  const label = field.previousElementSibling;

  // Create a new div for the validation error alert
  const errorAlert = document.createElement('div');
  errorAlert.classList.add('validation-error-report');
  errorAlert.innerHTML = `<div>● ${errorMsg}</div>`;

  // Insert the validation error alert after the label
  label.parentNode.insertBefore(errorAlert, label.nextSibling);

}


function displayValidationErrors(errors) {

  // Iterate over the errors array
  errors.forEach(error => {
    // Get the form field with the corresponding ID
    const field = document.getElementById(error.key);
    // Get the label for the form field
    const label = field.previousElementSibling;

    // Create a new div for the validation error alert
    const errorAlert = document.createElement('div');
    errorAlert.classList.add('validation-error-alert');
    errorAlert.innerHTML = error.value;

    // Insert the validation error alert after the label
    label.parentNode.insertBefore(errorAlert, label.nextSibling);
  });
}


function checkPassword(password) {
  // Check if the password is too short
  if (password.length < 8) {
    alert("Just to let you know, the password that you have submitted is too short and would be incredibly easy for a hacker to guess!");
    return;
  }

  // Check if the password contains at least one special character
  const regex = /^(?=.*[^\w\d\s])/;
  if (!regex.test(password)) {
    alert("Just to let you know, the password that you have submitted does not contain enough special characters and would be incredibly easy for a hacker to guess!");
  }
}

function openCustomModal() {
    console.log('here we go');

    setTimeout(() => {
        removeValidationErrors();
    }, 10);
    
    openModal('add_item');
}

window.addEventListener('load', (ev) => {
  fetchWebsiteRecords();

  //devAutoOpenForm();
  devAutoOpenFlashdata();
});
</script>