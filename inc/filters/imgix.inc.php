<?php

add_action('init', function () {
	if (!ep_get_option('ep_imgix_enabled', false)) {
		return;
	}

	// Disable all default wp thumbnail sizes
	add_filter('intermediate_image_sizes', '__return_empty_array', 999);
	add_filter('big_image_size_threshold', '__return_false', 999);

	// Filter wp max size of directly linked images
	add_filter('attachment_link', function ($url, $postId) {
		return wp_get_attachment_image_url($postId, [ 1920, 1080 ]);
	}, 10, 2);

	// Filter all media and replace with imgix url
	if ($mediaUrl = ep_get_option('ep_imgix_custom_url', '')) {
		add_filter('wp_get_attachment_url', function($url) use ($mediaUrl) {
			$upload_dir = wp_get_upload_dir();
			return str_replace($upload_dir['baseurl'], $mediaUrl, $url);
		}, 10, 1);
	}

	// Translate wp image sizes to imgix parameters
	add_filter('image_downsize', function ($return, $attachment_id, $size) {
		$params = false;

		$img_url = wp_get_attachment_url($attachment_id);

		if ((is_array($size)) && (count($size) == 2)) {
			$params = [
				'w' => $size[0],
				'h' => $size[1],
				'fit' => 'crop'
			];
		} else {
			$sizes = [
				'thumbnail' => [ 'w' => 150, 'h' => 150, 'fit' => 'crop', 'auto' => 'compress' ],
				'medium' => [ 'w' => 300, 'h' => 300, 'fit' => 'max', 'auto' => 'compress' ],
				'large' => [ 'w' => 1024, 'h' => 1024, 'fit' => 'max', 'auto' => 'compress' ]
			];

			if (array_key_exists($size, $sizes)) {
				$params = $sizes[$size];
			}
			
			// else {
			// 	$meta = wp_get_attachment_metadata($attachment_id);
			// 	$params = [
			// 		'w' => (isset($meta['width']) ? $meta['width'] : 0),
			// 		'h' => (isset($meta['height']) ? $meta['height'] : 0)
			// 	];
			// }
		}

		if (!empty($params)) {
			$img_url = add_query_arg($params, $img_url);

			$return = [ $img_url, $params['w'], $params['h'], true ];
		}

		return $return;
	}, 10, 3);

	// Filter yoast images and bypass clean_url filter which strips Imgix properties
	(function () {
		$filter = function ($current) {
			if (empty($current)) {
				return $current;
			}

			// Parse and add default Imgix properties
			$current = ep_imgix_rebuild_url($current, [ 'w' => 1200, 'h' => 628, 'fit' => 'crop' ]);

			// Attach filter in the current context
			add_filter('clean_url', function ($new, $original, $context) use ($current) {
				// If the attempted clean url is the original image return it
				return ($current == $original) ? $original : $new;
			}, 20, 3);
		
			return $current;
		};

		// Add filter for yoast OG and Twitter image
		add_filter('wpseo_opengraph_image', $filter, 20, 1);
		add_filter('wpseo_twitter_image', $filter, 20, 1);
	})();

	// Add MCE editor plugin
	add_filter('mce_external_plugins', function($plugin_array) {
		global $ep_folder;

		$mceImgixFile = $ep_folder . '/assets/js/mce-imgix.js';
		
		if (file_exists($mceImgixFile)) {
			$mceImgixFile = '/' . ltrim(str_replace('\\', '/', substr($mceImgixFile, strlen(ABSPATH))), '/\\');
			$plugin_array['ep_imgix'] = $mceImgixFile;
		}
	
		return $plugin_array;
	}, 10, 1);

	// Add image size css vars when inserting media
	add_filter('image_send_to_editor', function($html, $id, $caption, $title, $align, $url, $size, $alt) {    
		if (!$id)
			return;

		$dom = new DOMDocument();
		$dom->loadHTML($html);
		$img = $dom->getElementsByTagName('img')->item(0);

		$data = sprintf(' style="--img-w: %s; --img-h: %s" ', $img->getAttribute('width'), $img->getAttribute('height'));
		$html = str_replace('<img src', sprintf('<img%ssrc', $data), $html);

		return $html;
	}, 10, 8);

	// Add thumbnail to columns for the edit posts and pages list
	(function () {
		$filter = function ($column, $post_id) {
			switch ($column) {
				case 'ep_thumbnail':
					$thumb_id = get_post_thumbnail_id($post_id);
					if ($thumb_id) {
						$thumb = wp_get_attachment_image($thumb_id, [ 60, 60 ]);
						if ($thumb) {
							echo edit_post_link($thumb, '', '', $post_id);
						}
					}
					
					break;
			}
		};

		// Add filter for yoast OG and Twitter image
		add_action('manage_posts_custom_column', $filter, 10, 2);
		add_action('manage_pages_custom_column', $filter, 10, 2);
	})();

	// Adjust column width styling to the thumbnail in the edit posts list
	add_action('admin_head', function() {
		?>
		<style type="text/css">
			.column-ep_thumbnail { width: 60px; }
			.column-ep_thumbnail img { width: 100%; display: block; }
		</style>
		<?php
	}, 10, 0);

	// Add thumbnail column to selected post types
	add_action('admin_init', function () {
		$post_types = get_post_types([
			'public' => true
		]);
	
		foreach ($post_types as $post_type) {
			if (ep_get_option('ep_imgix_' . $post_type. '_thumbnails', false)) {
				add_action('manage_' . $post_type . '_posts_columns', function ($columns) {
					if (!isset($columns['ep_thumbnail'])) {
						$insertPos = array_search('title', array_keys($columns));
						$columns = array_slice($columns, 0, $insertPos, true)
							+ ['ep_thumbnail' => '']
							+ array_slice($columns, $insertPos, count($columns) - 1, true);
					}
					return $columns;
				}, 10, 1);
	
				add_action('manage_' . $post_type . '_custom_column', function ($column, $post_id) {
					if ($column == 'ep_thumbnail') {
						$thumb_id = get_post_thumbnail_id($post->ID);
						if ($thumb_id) {
							$thumb = wp_get_attachment_image_src($thumb_id, [ 60, 60 ]);
							if ($thumb) {
								echo edit_post_link('<img src="' . $thumb[0] . '" />', '', '', $thumb_id);
							}
						}
					}			
				}, 10, 2);
			}
		}
	}, 10, 0);

	// Remove Offload S3 filtes breaking imgix parameters
	ep_remove_filter('wp_prepare_attachment_for_js', 'Amazon_S3_And_CloudFront', 'maybe_encode_wp_prepare_attachment_for_js', 99);
	ep_remove_filter('wp_prepare_attachment_for_js', 'Amazon_S3_And_CloudFront_Pro', 'maybe_encode_wp_prepare_attachment_for_js', 99);
	ep_remove_filter('wp_prepare_attachment_for_js', 'DeliciousBrains\WP_Offload_Media\Integrations\Media_Library', 'maybe_encode_wp_prepare_attachment_for_js', 99);
	ep_remove_filter('wp_prepare_attachment_for_js', 'DeliciousBrains\WP_Offload_Media\Pro\Integrations\Media_Library_Pro', 'maybe_encode_wp_prepare_attachment_for_js', 99);

	ep_remove_filter('wp_get_attachment_image_attributes', 'Amazon_S3_And_CloudFront', 'wp_get_attachment_image_attributes', 99);
	ep_remove_filter('wp_get_attachment_image_attributes', 'Amazon_S3_And_CloudFront_Pro', 'wp_get_attachment_image_attributes', 99);
	ep_remove_filter('wp_get_attachment_image_attributes', 'DeliciousBrains\WP_Offload_Media\Integrations\Media_Library', 'wp_get_attachment_image_attributes', 99);
	ep_remove_filter('wp_get_attachment_image_attributes', 'DeliciousBrains\WP_Offload_Media\Pro\Integrations\Media_Library_Pro', 'wp_get_attachment_image_attributes', 99);
}, 100, 0);
