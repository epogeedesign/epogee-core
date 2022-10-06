<?php

function ep_yoast_set_meta($args) {
	if (empty($args) || !is_array($args)) return;

	// Extract $office from $args
	extract($args);

	// Set yoast urls
	if (!empty($url)) {
		add_filter('wpseo_canonical', function () use ($url) {
			return $url;
		}, 10, 0);

		add_filter('wpseo_opengraph_url', function () use ($url) {
			return $url;
		}, 10, 0);
	}

	// Set yoast titles
	if (!empty($title)) {
		$title = wpseo_replace_vars($title, false);

		add_filter('wpseo_title', function () use ($title) {
			return $title;
		}, 10, 0);

		add_filter('wpseo_opengraph_title', function () use ($title) {
			return $title;
		}, 10, 0);

		add_filter('wpseo_twittertitle', function () use ($title) {
			return $title;
		}, 10, 0);
	}

	// Set yoast descriptions
	if (!empty($description)) {
		$description = wp_trim_words(wp_strip_all_tags($description, true), 30, '…');

		add_filter('wpseo_metadesc', function () use ($description) {
			return $description;
		}, 10, 0);

		add_filter('wpseo_opengraph_desc', function () use ($description) {
			return $description;
		}, 10, 0);
	}

	// Set yoast images
	if ($image) {
		add_filter('wpseo_opengraph_image', function () use ($image) {
			return $image;
		}, 10, 0);

		add_filter('wpseo_twitter_image', function () use ($image) {
			return $image;
		}, 10, 0);
	}
}
