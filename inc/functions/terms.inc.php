<?php

/**
 * Return a fully formed term object.
 *
 * @param WP_Term|int $term The base term object or id.
 * @param mixed $meta Optionally include meta in term object.
 * @param mixed $fields Optionally include ACF fields in term object.
 * 
 * @return Ep_Abstract_Term Full formed term object.
 */
function ep_get_term($term, $meta = false, $fields = false) {
	// Get WP_Term object if supplied with id
	if (is_numeric($term)) {
		$term = get_term($term);
	}

	// Check if term was found
	if (!$term)
		return false;

	// Filter term meta
	$meta = apply_filters('ep/terms/meta', $meta, $term);
	$meta = apply_filters('ep/terms/meta/' . $term->taxonomy, $meta, $term);

	// Filter term fields
	$fields = apply_filters('ep/terms/fields', $fields, $term);
	$fields = apply_filters('ep/terms/fields/' . $term->taxonomy, $fields, $term);

	// Get fully formed term object
	$term = new Ep_Abstract_Term($term, $meta, $fields);

	// Check if term object is valid
	if (!$term->id)
		return false;

	// Filter the fully formed term object
	$term = apply_filters('ep/terms/get', $term, $meta, $fields);
	$term = apply_filters('ep/terms/get/' . $term->taxonomy, $term, $meta, $fields);
	
	// Return fully formed term object
	return $term;
}

/**
 * Retrieve the fully formed terms in a given taxonomy or list of taxonomies.
 * 
 * @param string|array $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
 * @param mixed $meta Optionally include meta in term object.
 * @param mixed $fields Optionally include ACF fields in term object.
 * 
 * @return Ep_Abstract_Term[] Array of fully formed term objects.
 */
function ep_get_terms($args, $meta = false, $fields = false) {
	// Get WP_Term collection using query args
	$terms = get_terms($args);

	// Check if terms were found
	if ((!$terms) || (is_wp_error($terms)))
		return false;

	// Map and return fully formed term objects
	return array_map(function ($term) use ($meta, $fields) {
		return ep_get_term($term, $meta, $fields);
	}, $terms);
}

/**
 * Retrieves an array of fully formed terms from terms array.
 * 
 * @param WP_Term[] $terms Array of term objects.
 * @param mixed $meta Optionally include meta in term object.
 * @param mixed $fields Optionally include ACF fields in term object.
 * 
 * @return Ep_Abstract_Term[] Array of fully formed term objects.
 */
function ep_format_terms($terms, $meta = false, $fields = false) {
	// Check if terms were supplied
	if (empty($terms) || empty($terms[0]))
		return false;

	// Map and return fully formed term objects
	return array_map(function ($term) use ($meta, $fields) {
		return ep_get_term($term, $meta, $fields);
	}, $terms);
}

function ep_get_register_taxonomy_labels($type, $slbl, $plbl, $args = []) {
	$slbl = _x($slbl, $type);
	$plbl = _x($plbl, $type);

	return array_merge([
		'name' => $plbl,
		'singular_name' => $slbl,
		'menu_name' => $plbl,
		'search_items' => _x(sprintf('Search %s', $plbl), $type),
		'popular_items' => _x(sprintf('Popular %s', $plbl), $type),
		'all_items' => _x(sprintf('All %s', $plbl), $type),
		'parent_item' => _x(sprintf('Parent %s', $slbl), $type),
		'parent_item_colon' => _x(sprintf('Parent %s:', $slbl), $type),
		'edit_item' => _x(sprintf('Edit %s', $slbl), $type),
		'update_item' => _x(sprintf('Update %s', $slbl), $type),
		'add_new_item' => _x(sprintf('Add New %s', $slbl), $type),
		'new_item_name' => _x(sprintf('New %s Name', $slbl), $type),
		'separate_items_with_commas' => _x(sprintf('Separate %s with commas', $plbl), $type),
		'add_or_remove_items' => _x(sprintf('Add or remove %s', $plbl), $type),
		'choose_from_most_used' => _x(sprintf('Choose from the most used %s', $plbl), $type),
		'not_found' => _x(sprintf('No %s found', $plbl), $type)
	], $args);
}
