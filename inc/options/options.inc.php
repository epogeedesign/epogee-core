<?php

add_action('admin_enqueue_scripts', function () {
	global $ep_folder;

	$adminCssFile = $ep_folder . '/assets/css/admin.css';
	
	if (file_exists($adminCssFile)) {
		$adminCssFile = '/' . ltrim(str_replace('\\', '/', substr($adminCssFile, strlen(ABSPATH))), '/\\');
		wp_enqueue_style('ep-admin-styles', $adminCssFile);
	}
}, 10, 0);

add_action('init', function () {
	global $ep_folder;

	require_once 'general/general-tab.class.php';
	require_once 'general/imgix-tab.class.php';
	require_once 'general/mail-tab.class.php';
	require_once 'general/scripts-tab.class.php';

	$logoFile = $ep_folder . '/assets/img/epogee-logo-round.png';
	$logoFile = '/' . ltrim(str_replace('\\', '/', substr($logoFile, strlen(ABSPATH))), '/\\');

	$MainSettingsPage = new Ep_Admin_Menu_Page(
		'ep-core',
		__('General Options', 'ep'),
		'EP Core',
		'EP Core',
		'manage_options',
		'', // $logoFile,
		98.6
	);

	$MainSettingsPage->AddTab('general', __('General', 'ep'), 'Ep_Options_General_Tab');
	$MainSettingsPage->AddTab('imgix', __('Imgix', 'ep'), 'Ep_Options_Imgix_Tab');
	$MainSettingsPage->AddTab('scripts', __('Scripts', 'ep'), 'Ep_Options_Scripts_Tab');
	$MainSettingsPage->AddTab('mail', __('Mail', 'ep'), 'Ep_Options_Mail_Tab');

	// Apply Filters
	do_action('ep/options/init');
}, 10, 0);
