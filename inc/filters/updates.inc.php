<?php

add_action('init', function () {
	define('WP_GITHUB_FORCE_UPDATE', true);

	// Bail if not in admin
	if (!is_admin()) return;

	// Set updater config
	$config = [
		'slug' => 'epogee-core/epogee-core.php',
		'proper_folder_name' => 'epogee-core',
		'api_url' => 'https://api.github.com/repos/epogeedesign/epogee-core',
		'raw_url' => 'https://raw.github.com/epogeedesign/epogee-core/main',
		'github_url' => 'https://github.com/epogeedesign/epogee-core',
		'zip_url' => 'https://github.com/epogeedesign/epogee-core/archive/main.zip',
		'requires' => '6.0',
		'tested' => '6.0',
		'readme' => 'README.md'
	];

	// Use updater
	new \WP_GitHub_Updater($config);
}, 10, 0);
