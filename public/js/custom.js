const centerStage = document.getElementsByClassName('center-stage')[0];

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

function launchUrl(targetUrl) {
  window.open(targetUrl, '_blank');
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

function moveLastChildToBeginning(parentElement) {
  if (parentElement.childElementCount > 1) {
    const lastChild = parentElement.lastElementChild;
    parentElement.insertBefore(lastChild, parentElement.firstChild);
  }
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

function openCustomModal(itemType, recordCode = '') {
  console.log('here we go with ' + itemType + ' and ' + recordCode);

  setTimeout(() => {
    removeValidationErrors();
  }, 10);

  openModal(itemType, recordCode);
}

function populateFolderDropdown(selectedFolderId='') {
   const folderDropdown = document.getElementById('folder-dropdown');

   for (var i = 0; i < allFolders.length; i++) {
     const newSelectOption = document.createElement('option');
     newSelectOption.setAttribute('value', allFolders[i]['id']);
     newSelectOption.innerHTML = allFolders[i]['folder_name'];
     folderDropdown.appendChild(newSelectOption);
   }
}