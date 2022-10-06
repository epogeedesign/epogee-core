<?php

function ep_get_fields($name) {
	if ((!function_exists('acf_get_fields')) || (!function_exists('get_field')))
		return false;

	if (!$group = acf_get_fields($name))
		return false;

	$fields = [];

	foreach ($group as $field) {
		$fields[$field['name']] = get_field($field['key'], 'option');
	}

	return $fields;
}
