let currentRecordCode = '';
let items;

function fetchWebsiteRecords() {
  targetUrl = baseUrl + 'api/get/website_records';
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

function openPasswordModal() {
  closeModal();
  setTimeout(() => {
    openModal('create_password');
  }, 160);
}

function submitForm(submitBtn, formData) {
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

    if (http.status == 200) {
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

window.addEventListener('load', (ev) => {
  fetchWebsiteRecords();
  //devAutoOpenForm();
  //devAutoOpenFlashdata();
});