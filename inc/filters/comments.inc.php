<?php

add_action('init', function () {
	if (!ep_get_option('ep_comments_disabled', false)) {
		return;
	}

	// Disable support for comments and trackbacks in post types
	add_action('admin_init', function() {
		$post_types = get_post_types();
		foreach ($post_types as $post_type) {
			if (post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
			remove_meta_box('commentsdiv', $post_type, 'normal');
		}
	}, 10, 0);

	// Close comments on the front-end
	add_filter('comments_open', '__return_false', 20, 0);
	add_filter('pings_open', '__return_false', 20, 0);

	// Hide existing comments
	add_filter('comments_array', function() {
		return [];
	}, 10, 0);

	// Remove comments page in menu
	add_action('admin_menu', function() {
		remove_menu_page('edit-comments.php');
	}, 10, 0);

	// Redirect any user trying to access comments page
	add_action('admin_init', function() {
		global $pagenow;
		if ($pagenow === 'edit-comments.php') {
			wp_redirect(admin_url());
			exit;
		}
	}, 10, 0);

	// Remove comments metabox from dashboard
	add_action('admin_init', function() {
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	}, 10, 0);

	// Remove comments links from admin bar
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}, 10, 0);
