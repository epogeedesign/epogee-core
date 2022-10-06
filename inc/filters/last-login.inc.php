<?php

// Add Last Login feature
add_action('init', function () {
	if (!ep_get_option('ep_user_last_login_enabled', false)) {
		return;
	}

	// Update the user's last login time when logging in
	add_action('wp_login', function($user_login, WP_User $user) {
		update_user_meta($user->ID, 'last_login', current_time('mysql'));
	}, 10, 2);

	// Update registered columns for the manage Users list
	add_filter('manage_users_columns', function($columns) {
		$columns['ep_last_login'] = __('Last login', 'ep');
		return $columns;
	}, 10, 1);

	// Update sortable columns for the manage Users list
	add_filter('manage_users_sortable_columns', function ($columns) {
		$columns['ep_last_login'] = 'last_login';
		return $columns;
	}, 10, 1);

	// Add data to registered columns for the manage Users list
	add_action('manage_users_custom_column',  function($value, $column, $user_id) {
		switch ($column) {
			case 'ep_last_login':
				$last_login = get_user_meta($user_id, 'last_login', true);
				return date_i18n('Y/m/d \a\t h:i a', strtotime($last_login));
		}

		return $value;
	}, 10, 3);

	// Modify order by request for the Users list
	add_action('load-users.php', function () {
		add_filter('pre_get_users', function ($vars) {
			if (!empty($vars->query_vars['orderby'])) {
				switch ($vars->query_vars['orderby']) {
					case 'last_login':
						$vars->query_vars = array_merge($vars->query_vars, [
							'meta_key' => 'last_login',
							'orderby' => 'meta_value'
						]);
						break;
				}
			}

			return $vars;
		}, 10, 1);
	}, 10, 0);

	// Adjust column width styling for Users list
	add_action('admin_head', function() {
		?>
		<style type="text/css">
			.fixed .column-ep_last_login { width: 200px; }
		</style>
		<?php
	}, 10, 0);
}, 10, 0);
