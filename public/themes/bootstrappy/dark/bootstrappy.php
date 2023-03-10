<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/admin-slide-nav.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>css/bootstrappy.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/custom.css">
    <?= $additional_includes_top ?>
	<title><?= OUR_NAME ?></title>
</head>
<body>
<script>
const baseUrl = '<?= BASE_URL ?>';
</script>
<div class="top-gutter">
	<div class="logo logo-font"><?= anchor('#', OUR_NAME) ?></div>
	<div class="top-rhs">
		<div class="hide-sm"><?= anchor('#', '<i class=\'fa fa-envelope\'></i> Enquiries (88)', array('class' => 'highlight')) ?></div>
		<div class="hide-sm"><?= anchor('#', '<i class=\'fa fa-shopping-cart\'></i> Orders') ?></div>
		<div class="hide-sm language-selector"><?php
		$language_options['ar'] = 'Arabic';
		$language_options['en'] = 'English';
		$language_options['fr'] = 'French';
		echo form_dropdown('language', $language_options, 'en');
		?></div>
		<div id="top-rhs-selector">
		<i class="fa fa-user"></i><span id="admin-down-arrow">▼</span></div>
		<div id="admin-settings-dropdown">
			<ul>
				<li><?= anchor('members-account/your_account', '<i class=\'fa fa-user\'></i> Your Account ') ?></li>
				<li><?= anchor('members-account/update', '<i class=\'fa fa-pencil\'></i> Update Your Details ') ?></li>
				<li class="top-border"><?= anchor('members/logout', '<i class=\'fa fa-sign-out\'></i> Logout ') ?></li>
			</ul>
		</div>
		<div id="hamburger" class="hide-lg" onclick="openSlideNav()">&#9776;</div>
	</div>
</div>
<div class="wrapper" style="opacity:0">
	<div id="sidebar">
		<nav id="left-nav">
			<ul>
				<li><?= anchor('#', '<i class=\'fa fa-home\'></i> All Items') ?></li>
				<li><?= anchor('your_passwords', '<i class=\'fa fa-lock\'></i> Passwords') ?></li>
				<li><?= anchor('#', '<i class=\'fa fa-file-text\'></i> Notes') ?></li>
				<li><?= anchor('#', '<i class=\'fa fa-address-book\'></i> Addresses') ?></li>
				<li><?= anchor('#', '<i class=\'fa fa-credit-card\'></i> Payment Cards') ?></li>
				<li><?= anchor('#', '<i class=\'fa fa-bank\'></i> Bank Accounts') ?></li>
		    </ul>
	    </nav>
	</div>
	<div class="center-stage"><?= Template::display($data) ?></div>
</div>
<div class="footer">
	<?= anchor('https://trongate.io/', 'Powered by Trongate') ?>
</div>

<div id="slide-nav">
    <div id="close-btn" onclick="closeSlideNav()">&times;</div>
    <ul auto-populate="true"></ul>
</div>
<script>
const allFolders = [
  { id: "1", folder_name: "Business", url_string: "business" },
  { id: "2", folder_name: "Entertainment", url_string: "entertainment" },
  { id: "3", folder_name: "Finance", url_string: "finance" },
  { id: "4", folder_name: "Hosting", url_string: "hosting" },
  { id: "5", folder_name: "Social Media", url_string: "social-media" }
];
</script>
<script src="<?= BASE_URL ?>js/admin.js"></script>
<script src="<?= THEME_DIR ?>js/bootstrappy.js"></script>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
<script src="<?= BASE_URL ?>js/custom.js"></script>
<?= $additional_includes_btm ?>
<?php
if (ENV == 'dev') {
    echo '<script src="'.BASE_URL.'js/dev.js"></script>';
}
?>
</body>
</html>