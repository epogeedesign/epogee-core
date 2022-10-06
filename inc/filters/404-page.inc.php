<?php

add_action('init', function () {
	if (!($page404Id = intval(ep_get_option('ep_404_page_id', 0)))) return;

	add_filter('display_post_states', function ($post_states, $post) use ($page404Id) {
		if ($page404Id === $post->ID) {
			$post_states['page_404'] = __('404 Page', 'ep');
		}
		return $post_states;
	}, 10, 2);

	add_filter('template_include', function ($template) use ($page404Id) {
		global $post, $wp_query;
	
		if (is_404()) {
			$page404template = locate_template([
				get_page_template_slug($page404Id),
				'page.php'
			]);

			if ($page404template) {
				$post = get_post($page404Id);

				$wp_query->queried_object = $post;
				$wp_query->is_single = true;
				$wp_query->is_404 = false;
				$wp_query->queried_object_id = $post->ID;
				$wp_query->post_count = 1;
				$wp_query->current_post = -1;
				$wp_query->post = $post;
				$wp_query->posts = [$post];
				
				return $page404template;
			}
		}
	
		return $template;
	}, 99, 1);
}, 10, 0);
