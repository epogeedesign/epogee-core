<?php

/**
 * Return a fully formed post object.
 *
 * @param WP_Post|int $post The base post object or id.
 * @param mixed $terms Optionally include terms in post object.
 * @param mixed $meta Optionally include meta in post object.
 * @param mixed $fields Optionally include ACF fields in post object.
 * 
 * @return Ep_Abstract_Post Fully formed post object.
 */
function ep_get_post($post, $terms = false, $meta = false, $fields = false) {
	// Get WP_Post object if supplied with id
	if (is_numeric($post)) {
		$post = get_post($post);
	}

	// Check if post was found
	if (!$post)
		return false;

	// Filter post terms
	$terms = apply_filters('ep/posts/terms', $terms, $post);
	$terms = apply_filters('ep/posts/terms/' . $post->post_type, $terms, $post);

	// Filter post meta
	$meta = apply_filters('ep/posts/meta', $meta, $post);
	$meta = apply_filters('ep/posts/meta/' . $post->post_type, $meta, $post);

	// Filter post fields
	$fields = apply_filters('ep/posts/fields', $fields, $post);
	$fields = apply_filters('ep/posts/fields/' . $post->post_type, $fields, $post);

	// Get fully formed post object
	$post = new Ep_Abstract_Post($post, $terms, $meta, $fields);

	// Check if post object is valid
	if (!$post->id)
		return false;

	// Filter the fully formed post object
	$post = apply_filters('ep/posts/get', $post);
	$post = apply_filters('ep/posts/get/' . $post->type, $post);
	
	// Return fully formed post object
	return $post;
}

/**
 * Retrieves an array of fully formed posts matching the given criteria.
 * 
 * @param array $args Arguments to retrieve posts. See WP_Query::parse_query() for all available arguments.
 * 
 * @return Ep_Abstract_Post[] Array of post fully formed objects.
 */
function ep_get_posts($args) {
	// Get WP_Post collection using query args
	$posts = get_posts($args);

	// Check if posts were found
	if ((!$posts) || (is_wp_error($posts)))
		return false;

	// Apply additional query args
	$terms = isset($args['include_terms']) ? $args['include_terms'] : false;
	$meta = isset($args['include_meta']) ? $args['include_meta'] : false;
	$fields = isset($args['include_fields']) ? $args['include_fields'] : false;

	// Map and return fully formed post objects
	return array_map(function ($post) use ($terms, $meta, $fields) {
		return ep_get_post($post, $terms, $meta, $fields);
	}, $posts);
}

/**
 * Retrieves an array of fully formed posts from posts array.
 * 
 * @param WP_Post[] $posts Array of post objects.
 * @param mixed $terms Optionally include terms in post object.
 * @param mixed $meta Optionally include meta in post object.
 * @param mixed $fields Optionally include ACF fields in post object.
 * 
 * @return Ep_Abstract_Post[] Array of post fully formed objects.
 */
function ep_format_posts($posts, $terms = false, $meta = false, $fields = false) {
	// Check if posts were supplied
	if (empty($posts) || empty($posts[0]))
		return false;

	// Map and return fully formed post objects
	return array_map(function ($post) use ($terms, $meta, $fields) {
		return ep_get_post($post, $terms, $meta, $fields);
	}, $posts);
}

function ep_get_register_post_labels($type, $slbl, $plbl, $args = []) {
	$slbl = _x($slbl, $type);
	$plbl = _x($plbl, $type);

	return array_merge([
		'name' => $plbl,
		'singular_name' => $slbl,
		'menu_name' => $plbl,
		'add_new' => _x(sprintf('Add New %s', $slbl), $type),
		'add_new_item' => _x(sprintf('Add %s', $slbl), $type),
		'edit_item' => _x(sprintf('Edit %s', $slbl), $type),
		'new_item' => _x(sprintf('New %s', $slbl), $type),
		'view_item' => _x(sprintf('View %s', $slbl), $type),
		'search_items' => _x(sprintf('Search %s', $plbl), $type),
		'not_found' => _x(sprintf('No %s found', $plbl), $type),
		'not_found_in_trash' => _x(sprintf('No %s found in trash', $plbl), $type),
		'parent_item_colon' => _x(sprintf('Parent %s', $plbl), $type)
	], $args);
}

function ep_get_post_type_archive_link($post_type) {
	global $wp_rewrite;

	$post_type_obj = get_post_type_object($post_type);
	if (!$post_type_obj) {
		return false;
	}
	
	if ('post' === $post_type) {
		$show_on_front = get_option('show_on_front');
		$page_for_posts = get_option('page_for_posts');

		if ('page' === $show_on_front && $page_for_posts) {
			$link = get_permalink($page_for_posts);
		} else {
			$link = get_home_url();
		}

		return apply_filters('post_type_archive_link', $link, $post_type);
	}
	
	if (get_option('permalink_structure') && is_array($post_type_obj->rewrite)) {
		if ($post_type_obj->rewrite['with_front']) {
			$struct = $wp_rewrite->front . $post_type_obj->rewrite['slug'];
		} else {
			$struct = $wp_rewrite->root . $post_type_obj->rewrite['slug'];
		}
		$link = home_url(user_trailingslashit($struct, 'post_type_archive'));
	} else {
		$link = home_url('?post_type=' . $post_type);
	}

	return apply_filters('post_type_archive_link', $link, $post_type);
}

function ep_numeric_posts_nav() {
	if( is_singular() )
		return;

	global $wp_query;

	$class = 'ep_pagination';
	$classInner = $class . '__inner';
	$classI = $class . '__item';
	$classA = $classI . '-active';
	$classId = $classI . '-disabled';
	$classB = $class . '__button';
	$classBd = $classB . '-disabled';

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/** Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/** Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="mod_row__pagination ' . $class . '">' . "\n";

	/** Previous Post Link */
	if ( get_previous_posts_link() ):
		printf( '<div class="' . $classB . '">%s</div>' . "\n", get_previous_posts_link('Back') );
	else:
		echo( '<div class="' . $classB . ' ' . $classBd . '"><span>Back</span></div>' );
	endif;

	echo '<ul class="' . $classInner . '">' . "\n";

	/** Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		//$class = 1 == $paged ? ' class="' . $classI . ' ' . $classI . '-active"' : '';
		$class = 1 == $paged ? ' class="' . $classI . ' ' . $classA . '"' : ' class="' . $classI . '" ';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			echo '<li>…</li>';
	}

	/** Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="' . $classI . ' ' . $classA . '"' : ' class="' . $classI . '" ';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/** Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			echo '<li>…</li>' . "\n";

		$class = $paged == $max ? ' class="' . $classI . ' ' . $classA . '"' : ' class="' . $classI . '" ';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	echo '</ul>' . "\n";

	/** Next Post Link */
	if ( get_next_posts_link() ):
		printf( '<div class="' . $classB . '">%s</div>' . "\n", get_next_posts_link('Next') );
	else:
		echo( '<div class="' . $classB . ' ' . $classBd . '"><span>Next</span></div>' );
	endif;
	echo '</div>' . "\n";
}

function ep_post_breadcrumbs(Ep_Abstract_Post $post, $include_current = true) {
	$breadcrumbs = $post->parent ? ep_post_breadcrumbs(ep_get_post($post->parent)) : [];

	if ($include_current) {
		$breadcrumbs[] = [
			'text' => $post->title,
			'href' => $post->url
		];
	}

	return $breadcrumbs;
}
