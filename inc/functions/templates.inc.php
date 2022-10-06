<?php

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH and wp-includes/theme-compat
 * so that themes which inherit from a parent theme can just overload one file.
 *
 * @param string|array $templates Template(s) file to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param array        $args           Optional. Additional arguments passed to the template.
 *                                     Default empty array.
 * @return string The template filename if one is located.
 */

function ep_locate_template($templates, $load = false, $args = []) {
	global $ep_folder;
	$located = '';

	// return located if no template(s) provided
	if (empty($templates)) {
		return $located;
	}

	// Coerce template into templates array
	$templates = (array)$templates;

	// Append .php to filename if missing
	$templates = array_map(function ($template) {
		return $template . (substr($template, -4) != '.php' ? '.php' : '');
	}, $templates);

	// Attempt to find the template(s) within the active theme
	$located = locate_template($templates, false);

	// If not found already check template(s) within the plugins template folder
	if (empty($located)) {
		foreach ($templates as $template) {
			if (empty($template)) {
				continue;
			}

			// Generate path to plugin template
			$fallback_template = $ep_folder . '/' . $template;

			// Break and set located if the template exists
			if (file_exists($fallback_template)) {
				$located = $fallback_template;
				break;
			}
		}
	}

	// Check if the template has been found and should be loaded
	if (!empty($located) && ($load)) {
		// Determine the located template slug
		$located_template = false;
		foreach ($templates as $template) {
			if (substr($located, strlen($template) * -1) == $template) {
				$located_template = substr($located, strlen($template) * -1, strlen($template) - 4);
				break;
			}
		}

		// Apply filters on the template args
		if ($located_template) {
			$args = apply_filters('ep/template/' . $located_template, $args);
		}

		load_template($located, false, $args);
	}

	// Return the located template if any
	return $located;
}

function ep_render_template($templates, $args = []) {
	ob_start();

	$located = ep_locate_template($templates, true, $args);

	if (empty($located)) {
		return $located;
	}

	return ob_get_clean();
}

function ep_render_eval($code) {
	ob_start();

	eval($code);

	return ob_get_clean();
}

function ep_render_view($views) {
	// Coerce view into views array
	$views = (array)$views;

	// Iterate through views
	foreach ($views as $view) {
		// Determine times to repeat the given view
		$repeat = isset($view['repeat']) ? (int)$view['repeat'] : 1;

		// Render and repeat the view
		for ($i = 0; $i < $repeat; $i++) {
			// Check for valid template
			if (!empty($view['template'])) {
				ep_locate_template($view['template'], true, !empty($view['args']) ? $view['args'] : []);
			}

			// Check for manually set html or text content
			else if (!empty($view['content'])) {
				echo $view['content'];
			}
		}
	}
}

/**
 * Deep merges user defined arguments into defaults array.
 *
 * @param string|array|object $args     Value to merge with $defaults.
 * @param array               $defaults Optional. Array that serves as the defaults.
 *                                      Default empty array.
 * @return array Merged user defined values with defaults.
 */
function ep_parse_args($args, $defaults = []) {
	// Coerce the inputs into arrays
	$args = (array)$args;
	$defaults = (array)$defaults;

	// Setup return array
	$result = $args;

	// Iterate through all keys in args
	foreach ($defaults as $key => $value) {
		// Check if value is array and result key exists
		if (is_array($value)) {
			// Rescursively update the current key in result
			$result[$key] = ep_parse_args(isset($result[$key]) ? $result[$key] : [], $value);
		}
		
		// Check if default is important
		else if (substr($key, 0, 1) == '!') {
			$key2 = substr($key, 1);

			// Check if arg has been set
			if (isset($result[$key2])) {
				$result[$key2] = array_merge((array)$value, (array)$result[$key2]);
			}
			
			// Set result key to current value
			else {
				$result[$key2] = $value;
			}
		}
		
		// Check if arg has been set
		else if (!isset($result[$key])) {
			// Set result key to current value
			$result[$key] = $value;
		}
	}

	return $result;
}

function ep_build_html_attributes($attributes) {
	$atts = implode(' ', array_map(function ($key) use ($attributes) {
		// Check if attribute is an array
		if (is_array($attributes[$key])) {
			// Apply delimiter for array items in attribute
			$attributes[$key] = implode($key == 'style' ? '; ' : ' ', array_filter($attributes[$key]));
		}

		// Check if attribute is a key with no value
		if ($attributes[$key] === NULL) {
			return sprintf('%s', $key);
		}

		// Return
		return sprintf('%s="%s"', $key, htmlspecialchars($attributes[$key]));
	}, array_keys($attributes)));

	return $atts;
}

/**
 * Enqueue a CSS stylesheet.
 *
 * Registers the style if source provided (does NOT overwrite) and enqueues.
 * 
 * @param string           $handle Name of the stylesheet. Should be unique.
 * @param string           $src    Path to stylesheet relative to the theme directory.
 * @param string[]         $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
 * @param string           $media  Optional. The media for which this stylesheet has been defined.
 */
function ep_enqueue_style($handle, $src = '', $deps = [], $media = 'all') {
	if (file_exists(get_template_directory() . '/' . $src)) {
		wp_enqueue_style(
			$handle,
			get_template_directory_uri() . '/' . $src,
			$deps,
			filemtime(get_template_directory() . '/' . $src),
			$media
		);
	}
}

/**
 * Enqueue a script.
 *
 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
 *
 * @param string           $handle    Name of the script. Should be unique.
 * @param string           $src       Path to script relative to the theme directory.
 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
 */
function ep_enqueue_script($handle, $src = '', $deps = [], $in_footer = false) {
	if (file_exists(get_template_directory() . '/' . $src)) {
		wp_enqueue_script(
			$handle,
			get_template_directory_uri() . '/' . $src,
			$deps,
			filemtime(get_template_directory() . '/' . $src),
			$in_footer
		);
	}
}

/**
 * Localize a script.
 *
 * Works only if the script has already been registered.
 *
 * @param string $handle      Script handle the data will be attached to.
 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
 *                            Example: '/[a-zA-Z0-9_]+/'.
 * @param array  $data        The data itself. The data can be either a single or multi-dimensional array.
 * @return bool True if the script was successfully localized, false otherwise.
 */
function ep_localize_script($handle, $object_name, $data) {
	$script = sprintf('var %s = %s;', $object_name, json_encode($data));
	wp_add_inline_script($handle, $script, 'before');
}

function ep_asset_uri($src, $version = true) {
	if (is_bool($version) && $version) {
		$version = file_exists(get_template_directory() . '/' . $src) ? filemtime(get_template_directory() . '/' . $src) : false;
	}

	return get_template_directory_uri() . '/' . $src . (!empty($version) ? '?ver=' . $version : '');
}

/**
 * Enqueue google maps script.
 *
 * @param string           $handle    Name of the script. Should be unique.
 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
 */
function ep_enqueue_maps($handle, $deps = [], $in_footer = false) {
	add_action('wp_enqueue_scripts', function () use ($handle, $deps, $in_footer) {
		$browser_key = ep_get_option('ep_maps_browser_key', '');

		if (!empty($browser_key)) {
			wp_enqueue_script(
				$handle,
				sprintf('//maps.googleapis.com/maps/api/js?key=%s&callback=ep_maps_init#asyncload', $browser_key),
				$deps,
				false,
				$in_footer
			);
		}
	}, 10, 0);
}

function ep_return_json($data) {
	header('Content-Type: application/json');
	echo json_encode($data);
	exit();
}

function ep_check_debug($data) {
	// Get current user
	$user = wp_get_current_user();
	$user_is_admin = $user->exists() && $user->has_cap('administrator');

	// Bail if user is not an admin
	if (!$user_is_admin) return;

	// Show debug if set
	if (isset($_GET['debug'])) ep_return_json($data);;

	// Add debug link in header
	add_action('admin_bar_menu', function ($admin_bar) {
		$args = [
			'id' => 'ep-debug',
			'title' => 'Debug', 
			'href' => '?debug',
			'meta' => [
				'target' => '_blank'
			]
		];

		$admin_bar->add_node($args);
	}, 999);
}

/**
 * @param string|array $templates      Template(s) file to search for, in order.
 * @param array        $args           Optional. Additional arguments passed to the template.
 */
function ep_show_custom_template($templates, $args) {
	global $wp_query;

	// Set default query options
	$wp_query->is_home = false;

	// Run some WP actions that are otherwise skipped
	do_action('template_redirect');

	// Render template
	ep_locate_template($templates, true, $args);
	die;
}
