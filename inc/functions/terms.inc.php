<?php

function ep_get_term($term, $meta = false, $fields = false) {
	if (is_numeric($term)) {
		$term = get_term($term);
	}

	$term = new Ep_Abstract_Term($term, $meta, $fields);

	if (!$term->id)
		return false;

	$term = apply_filters('ep/terms/get', $term, $meta, $fields);
	$term = apply_filters('ep/terms/get/' . $term->taxonomy, $term, $meta, $fields);
	
	return $term;
}

/**
 * Retrieve the fully formed terms in a given taxonomy or list of taxonomies.
 * 
 * @param string|array $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
 * 
 * @return Ep_Abstract_Term[]
 */
function ep_get_terms($args) {
    $terms = get_terms($args);

    $meta = (!empty($args['include_meta']));
    $fields = (!empty($args['include_fields']));

    foreach ($terms as &$term)
        $term = new Ep_Abstract_Term($term, $meta, $fields);

    return $terms;
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
