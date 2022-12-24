var body = document.getElementsByTagName('body')[0];
var topRhsSelector = document.getElementById('top-rhs-selector');
var adminSettingsDropdown = document.getElementById('admin-settings-dropdown');
var wrapper = document.getElementsByClassName('wrapper')[0];
var hamburger = document.getElementById('hamburger');

topRhsSelector.addEventListener('click', (ev) => {
	event.stopPropagation();
	adminSettingsDropdown.classList.toggle('active');
});

body.addEventListener('click', (ev) => {
	if (!adminSettingsDropdown.contains(ev.target)) {
		adminSettingsDropdown.classList.remove('active');
	}

    if (slideNav.style.zIndex>0) {
			hamburger.style.color = '#637b8e';
            hamburger.style.pointerEvents = 'none';
    } else {
		hamburger.style.color = '#eee';
        hamburger.style.pointerEvents = 'auto';
    }
});

window.addEventListener('load', (ev) => {
    adjustToTopGutter();
    wrapper.style.opacity = 1;
    makeTablesResponsive();
})

window.addEventListener('resize', (ev) => {
    adjustToTopGutter();
})

function makeTablesResponsive() {
	var allTables = document.querySelectorAll('div.center-stage > table');
	for (var i = 0; i < allTables.length; i++) {
		var newNode = document.createElement('div');
		newNode.setAttribute('style', 'overflow-x:auto');
		var existingNode = allTables[i];
		existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
		newNode.appendChild(allTables[i]);
	}
}

function adjustToTopGutter() {
	var topGutter = document.getElementsByClassName('top-gutter')[0];
	var topGutterRect = topGutter.getBoundingClientRect();
    topGutterHeight = topGutterRect.height;
	adminSettingsDropdown.style.top = topGutterHeight - 7 + 'px';
	wrapper.style.marginTop = topGutterHeight + 'px';
}

var dropdowns = document.getElementsByClassName('dropdown');
for (var i = 0; i < dropdowns.length; i++) {
	dropdowns[i].addEventListener('click', (ev) => {
		ev.stopPropagation;
		var dropdownEl = ev.target.closest('.dropdown');

		var caret = dropdownEl.querySelector('.fa-caret-right');
		caret.classList.toggle('rotate');

        var dropdownArea = dropdownEl.nextElementSibling;
        var dropdownAreaSize = dropdownArea.getBoundingClientRect();
        if (dropdownAreaSize.height>0) {
	        dropdownArea.style.maxHeight = 0;
        } else {
	        dropdownArea.style.maxHeight = dropdownArea.scrollHeight + "px";
        }
	});
}