const centerStage = document.getElementsByClassName('center-stage')[0];
let currentRecordCode = '';
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
  card.setAttribute('id', 'card-' + itemObj['id']);

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

  // Append the launch button
  launchDiv.appendChild(launchBtn);

  // Create button group element
  var outerDiv = document.createElement("div");
  outerDiv.id = "8136e02444a0";

  // Create the inner div elements
  var innerDiv1 = document.createElement("div");
  var innerDiv2 = document.createElement("div");
  var innerDiv3 = document.createElement("div");

  // Create the button elements
  var button1 = document.createElement("button");
  button1.className = "alt";
  button1.setAttribute("onclick", "initUpdateItem(" + itemObj['id'] + ")");
  var button2 = document.createElement("button");
  button2.className = "alt";
  var button3 = document.createElement("button");
  button3.className = "alt";

  // Create the i elements
  var i1 = document.createElement("i");
  i1.setAttribute("class", "fa fa-wrench");
  var i2 = document.createElement("i");
  i2.className = "fa fa-users";
  var i3 = document.createElement("i");
  i3.className = "fa fa-trash";

  // Append the i elements to the button elements
  button1.appendChild(i1);
  button2.appendChild(i2);
  button3.appendChild(i3);

  // Append the button elements to the inner div elements
  innerDiv1.appendChild(button1);
  innerDiv2.appendChild(button2);
  innerDiv3.appendChild(button3);

  // Append the inner div elements to the outer div element
  outerDiv.appendChild(innerDiv1);
  outerDiv.appendChild(innerDiv2);
  outerDiv.appendChild(innerDiv3);

  // Append the outer div element to the body of the page
  launchDiv.appendChild(outerDiv);

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

  //Add hidden data to the card body
  const hiddenCardData1 = document.createElement("div");
  hiddenCardData1.id = "hidden-card-data-" + itemObj['id'];
  hiddenCardData1.style.display = "none";

  const usernameDiv = document.createElement("div");
  usernameDiv.className = "record-username";
  usernameDiv.textContent = itemObj['username'];

  const passwordDiv = document.createElement("div");
  passwordDiv.className = "record-password";
  passwordDiv.textContent = itemObj['password'];

  const notesDiv = document.createElement("div");
  notesDiv.className = "record-notes";
  notesDiv.textContent = itemObj['notes'];

  hiddenCardData1.appendChild(usernameDiv);
  hiddenCardData1.appendChild(passwordDiv);
  hiddenCardData1.appendChild(notesDiv);

  cardBody.appendChild(hiddenCardData1);

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
  console.log(JSON.stringify(formData));

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

      addItemToContainer(newRecordObj, itemsContainer);
      moveLastChildToBeginning(itemsContainer);

      setTimeout(() => {
        const flashdataMsg = 'The new website record was successfully added';
        const flashdataTheme = 'success';
        openFlashdata(flashdataMsg, flashdataTheme);
      }, 1333);

    }

  }
}

function moveLastChildToBeginning(parentElement) {
  if (parentElement.childElementCount > 1) {
    const lastChild = parentElement.lastElementChild;
    parentElement.insertBefore(lastChild, parentElement.firstChild);
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
  const textarea = form.querySelector('#notes');

  const errors = [];
  const params = {};

  for (let i = 0; i < inputs.length; i++) {
    const input = inputs[i];
    let errorMsg = '';
    let valid = true;

    switch (input.id) {
      case 'website-url':
        if (!input.value.startsWith('http')) {
          errorMsg = `The website URL must start with http`;
          valid = false;
        }
        break;
      case 'website-name':
        if (input.value.length === 0) {
          errorMsg = `The website name cannot be empty`;
          valid = false;
        }
        break;
      case 'password':
        if (input.value.length === 0) {
          errorMsg = `The password is required`;
          valid = false;
        } else if (input.value.length > 64) {
          errorMsg = `The password cannot be more than 64 characters in length`;
          valid = false;
        }
        break;
      default:
        // No validation needed
        params[input.id] = input.value;
        break;
    }

    if (!valid) {
      input.classList.add('form-field-validation-error');
      errors.push(errorMsg);
      addValidationError(input.id, errorMsg);
    } else {
      input.classList.remove('form-field-validation-error');
      params[input.id] = input.value;
    }
  }

  if (textarea.value.length > 800) {
    textarea.classList.add('form-field-validation-error');
    const errorMsg = `The notes cannot be more than 800 characters in length`;
    errors.push(errorMsg);
    addValidationError(textarea.id, errorMsg);
  } else {
    textarea.classList.remove('form-field-validation-error');
    params[textarea.id] = textarea.value;
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

  // Check if an error message div already exists for the form field
  const existingErrorAlert = document.querySelector(`#${formFieldId} + .validation-error-report`);

  if (!existingErrorAlert) {
    // Create a new div for the validation error alert
    const errorAlert = document.createElement('div');
    errorAlert.classList.add('validation-error-report');
    errorAlert.innerHTML = `<div>● ${errorMsg}</div>`;

    // Insert the validation error alert after the form field
 
    label.appendChild(errorAlert);
  } else {
    // Update the error message in the existing error message div
    existingErrorAlert.innerHTML = `<div>● ${errorMsg}</div>`;
  }
}



function addValidationErrorX(formFieldId, errorMsg) {
console.log('adding val error for ' + formFieldId);
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

function initUpdateItem(recordId) {

  const cardElement = document.getElementById("card-" + recordId);
  const websiteNameElement = cardElement.querySelector(".website_name");
  const websiteName = websiteNameElement.textContent;

  const buttonElement = cardElement.querySelector(".launch-btn");
  const onclick = buttonElement.getAttribute("onclick");
  const websiteUrl = onclick.match(/'(.*?)'/)[1];

  const hiddenCardData = document.getElementById("hidden-card-data-" + recordId);
  const usernameDiv = hiddenCardData.querySelector(".record-username");
  const username = usernameDiv.textContent;

  const passwordDiv = hiddenCardData.querySelector(".record-password");
  const password = passwordDiv.textContent;

  const notesDiv = hiddenCardData.querySelector(".record-notes");
  const notes = notesDiv.textContent;

  openModal('create_password');

  document.getElementById('website-url').value = websiteUrl;
  document.getElementById('website-name').value = websiteName;
  document.getElementById('username').value = username;
  document.getElementById('password').value = password;
  document.getElementById('notes').value = notes;

}

setInterval(() => {
    console.log(currentRecordCode);
}, 1000);

function openCustomModal(itemType, recordCode='') {
    console.log('here we go with ' + itemType + ' and ' + recordCode);

    setTimeout(() => {
        removeValidationErrors();
    }, 10);
    
    openModal(itemType, recordCode);
}

window.addEventListener('load', (ev) => {
  fetchWebsiteRecords();

  //devAutoOpenForm();
  //devAutoOpenFlashdata();
});