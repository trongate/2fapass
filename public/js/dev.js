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