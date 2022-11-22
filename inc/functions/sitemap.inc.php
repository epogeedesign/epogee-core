<?php

function ep_sitemap_for_posts($post_types = false) {
	// Get all public post types by default
	if ($post_types === false) {
		$post_types = get_post_types([
			'public' => 1
		], 'names');
	}

	// Get all sitemap posts for types
	$posts = ep_get_posts([
		'post_type' => $post_types,
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => [
			'relation' => 'OR',
			[
				'key' => '_yoast_wpseo_meta-robots-noindex',
				'compare' => 'NOT EXISTS'
			],
			[
				'key' => '_yoast_wpseo_meta-robots-noindex',
				'compare' => '=',
				'value' => ''
			],
			[
				'key' => '_yoast_wpseo_meta-robots-noindex',
				'compare' => '=',
				'value' => '2'
			]
		]
	]);

	// Generate entries
	if (!is_wp_error($posts) && !empty($posts)) {
		return array_map(function ($post) {
			return [
				'url' => $post->url,
				'title' => $post->title
			];
		}, $posts);
	}

	return [];
}

function ep_sitemap_for_taxonomies($taxonomies = false) {
	// Get all public taxonomies by default
	if ($taxonomies === false) {
		$taxonomies = get_taxonomies(array(
			'public' => 1
		), 'names');
	}

	// Get all sitemap terms for taxonomies
	$terms = ep_get_terms([
		'taxonomy' => $taxonomies
	]);

	// Generate entries
	if (!is_wp_error($terms) && !empty($terms)) {
		return array_map(function ($term) {
			return [
				'url' => $term->url,
				'title' => $term->name
			];
		}, $terms);
	}

	return [];
}

function ep_sitemap_to_tree($items, $path = '/') {
	// Collection of sitemap parents
	$parents = array();

	// Set the URL depth to search for parents
	$depth = substr_count($path, '/');

	// Iterate through the current set of sitemap entries
	foreach ($items as $entry) {
		$entry_depth = substr_count($entry['url'], '/');

		// Skip the current entry if the URL depth does not match
		if ((substr($entry['url'], 0, strlen($path)) != $path) || ($entry_depth != $depth)) {
			continue;
		}

		// Recursively generate children at the current URL
		$entry['children'] = ep_sitemap_to_tree($items, $entry['url'] . '/');

		// Add the current entry to the sitemap parents collection
		$parents[] = $entry;
	}

	// Check if the parents collection has entries
	if ($parents) {
		usort($parents, function ($a, $b) {
			return strcmp($a['url'], $b['url']);
		});
	}

	// Return the current collection of sitemap parents
	return $parents;
}

function ep_sitemap_to_html($items, $parent = 'ul', $child = 'li') {
	$html = '';

	if (!empty($items)) {
		$html .= sprintf('<%s>', $parent);

		foreach ($items as $entry) {
			$html .= sprintf('<%s>', $child);
			$html .= sprintf('<a href="%s">%s</a>', $entry['url'], $entry['title']);

			if (!empty($entry['children'])) {
				$html .= ep_sitemap_to_html($entry['children'], $parent, $child);
			}

			$html .= sprintf('</%s>', $child);
		}

		$html .= sprintf('</%s>', $parent);
	}

	return $html;
}
