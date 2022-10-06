<?php

function ep_get_option($option, $default = false) {
	global $Ep_Options;
	return $Ep_Options->GetOption($option, $default);
}

function ep_get_option_states($option) {
	global $Ep_Options;
	return $Ep_Options->GetStates($option);
}

function ep_register_option_replacements($replacements) {
	if (empty($replacements)) return;

	$filter = function ($value) use ($replacements) {
		if (empty($value)) return $value;

		foreach ($replacements as $rkey => $rvalue) {
			$value = str_replace(sprintf('{{%s}}', $rkey), $rvalue, $value);
		}

		return $value;
	};

	add_filter('acf/format_value/type=text', $filter, 10, 1);
	add_filter('acf/format_value/type=textarea', $filter, 10, 1);
	add_filter('acf/format_value/type=wysiwyg', $filter, 10, 1);
}

function ep_register_option_replacements_from_post(WP_Post $post) {
	if (empty($post)) return;

	$replacements = [
		'post_title' => $post->post_title,
		'post_name' => $post->post_name
	];

	$parent = !empty($post->post_parent) ? get_post($post->post_parent) : false;

	if (!empty($parent)) {
		$replacements = array_merge($replacements, [
			'parent_post_title' => $parent->post_title,
			'parent_post_name' => $parent->post_name
		]);
	}

	ep_register_option_replacements($replacements);
}
