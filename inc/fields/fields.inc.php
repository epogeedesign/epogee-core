<?php

add_action('acf/include_field_types', function () {
	include_once(plugin_dir_path(__FILE__) . 'icon/icon-field.class.php');

	new Ep_Icon_Field();
}, 10, 0);
