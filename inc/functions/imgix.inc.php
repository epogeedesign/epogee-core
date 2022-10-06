<?php

use Imgix\UrlBuilder;

function ep_imgix_parse_url($url) {
	$parts = wp_parse_url($url);

	if (empty($parts)) {
		return false;
	}

	$parts['params'] = [];

	if (!empty($parts['query'])) {
		parse_str($parts['query'], $parts['params']);
	}

	unset($parts['query']);

	return $parts;
}

function ep_imgix_build_url($parts) {
	// Filter imgix image parameters
	$parts['params'] = apply_filters('ep/imgix/params', $parts['params'], $parts);

	return sprintf(
		'%s://%s%s',
		$parts['scheme'],
		$parts['host'],
		$parts['path'] . (!empty($parts['params']) ? '?' . http_build_query($parts['params']) : '')
	);
}

function ep_imgix_rebuild_url($url, $params, $merge = true) {
	// Parse url parts
	$parts = ep_imgix_parse_url($url);

	// Merge or replace the image params
	if ($merge) {
		$parts['params'] = array_merge($parts['params'], $params);
	} else {
		$parts['params'] = $params;
	}

	// Rebuild image url with new properties
	$url = ep_imgix_build_url($parts);

	return $url;
}

function ep_imgix_srcset($parts, $options = []) {
	$builder = new UrlBuilder(
		$parts['host'],
		$parts['scheme'] == 'https',
		false,
		false
	);

	// Filter imgix srcset options and image parameters
	$options = apply_filters('ep/imgix/options', $options, $parts);
	$parts['params'] = apply_filters('ep/imgix/params', $parts['params'], $parts);

	return $builder->createSrcSet(
		$parts['path'],
		$parts['params'],
		$options
	);
}
