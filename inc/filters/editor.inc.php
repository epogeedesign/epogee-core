<?php

// Disable Gutenberg editor
add_action('admin_init', function () {
	if (!ep_get_option('ep_editor_gutenberg_disabled', false)) {
		return;
	}

	add_filter('use_block_editor_for_post_type', '__return_false', 10);
}, 10, 0);

add_action('admin_menu', function () {
	if (!ep_get_option('ep_editor_move_excerpt_metabox', false)) {
		return;
	}

	// Remove Excerpt metabox
	$post_types = get_post_types([], 'names');
	foreach ($post_types as $post_type) {
		if (post_type_supports($post_type, 'excerpt')) {
			remove_meta_box('postexcerpt', $post_type, 'normal');
		}
	}

	// Add and move Excerpt metabox
	add_action('add_meta_boxes', function ($post_type) {
		if (post_type_supports($post_type, 'excerpt')) {
			add_meta_box('excerpt_meta', __('Excerpt'), 'post_excerpt_meta_box', $post_type, 'temp', 'high');
		}
	}, 10, 1);

	// Render moved Excerpt metabox
	add_action('edit_form_after_title', function () {
		global $post;
		do_meta_boxes(get_current_screen(), 'temp', $post);
	}, 10, 0);
}, 10, 0);
