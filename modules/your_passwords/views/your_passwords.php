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
                <input type="text" id="website-url" id="website-url" value="" autocomplete="off">
            </div>
            <div class="two-col">
                <div>
                    <label>Website Name:</label>
                    <input type="text" id="website-name" value="">
                </div>
                <div>
                    <label>Folder:</label>
                    <input type="text" id="folder" value="">
                </div>
            </div>
            <div class="two-col">
                <div>
                    <label>Username:</label>
                    <input type="text" id="username" value="">
                </div>
                <div>
                    <label>Site password:</label>
                    <input type="text" id="password" value="">
                </div>
            </div>
            <div>
                <label>Notes:</label>
                <textarea id="notes"></textarea>
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

function fetchSitePasswords() {
  targetUrl = baseUrl + 'api/get/member_passwords';
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

function drawItemsGrid(responseText) {
  items = JSON.parse(responseText);
  if (items.length > 0) {
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

function submitForm(submitBtn) {
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
  }, 2000);

}


function saveItem() {
  const values = readFormValues();
  if (Array.isArray(values)) {
    // Validation errors
    values.forEach((error) => {
      console.log(error);
    });
  } else {
    // No validation errors
    const submitBtn = document.getElementById('submit-btn');
    submitForm(submitBtn);
    return;
    submitBtn.innerHTML = '<span class="spinner"></span>';

    console.log('no validation errors');
    return;

    const formData = new FormData();
    for (const key in values) {
      formData.append(key, values[key]);
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost/demo/submit', true);
    xhr.send(formData);
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

  if (textarea.value.length === 0) {
    textarea.classList.add('form-field-validation-error');
    errors.push('notes cannot be empty');
  } else {
    textarea.classList.remove('form-field-validation-error');
    params.notes = textarea.value;
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
  fetchSitePasswords();

  devAutoOpenForm();
});
</script>