<?php

// Set Card defaults
add_filter('ep/template/views/parts/card/card', function ($args) {
	// Card defaults
	$defaults = [
		'card' => [
			'tag' => 'article',
			'bem' => 'card',
			'appearance' => [
				'layout' => 'ttb'
			]
		]
	];

	$args = ep_parse_args($defaults, $args);

	return $args;
}, 10, 1);

// Run render function if present in Card args
add_filter('ep/template/views/parts/card/card', function ($args) {
	// CHeck if the render function if present and callable
	if (!empty($args['render']) && is_callable($args['render'])) {
		// Call render function
		$render = $args['render'](!empty($args['data']) ? $args['data'] : []);

		// Filter the rendered data
		$render = apply_filters('ep/card/render', $render, $args);

		// Check rendered data and merge with args
		if (!empty($render)) {
			$args = ep_parse_args($render, $args);
		}
	}

	return $args;
}, 20, 1);

// Check and add basic properties to a Card
add_filter('ep/template/views/parts/card/card', function ($args) {
	// Check if card missing attributes
	if (empty($args['card']['attributes'])) {
		$args['card']['attributes'] = [];
	}

	// Check if card missing class
	if (empty($args['card']['attributes']['class'])) {
		$args['card']['attributes']['class'] = '';
	}

	// Check if card missing bem
	if (empty($args['card']['bem'])) {
		$args['card']['bem'] = 'card';
	}

	// Apply card bem class
	$args['card']['attributes']['class'] = trim(implode(' ', [
		$args['card']['bem'],
		$args['card']['attributes']['class']
	]));

	// Apply card appearance classes
	if (!empty($args['card']['appearance'])) {
		foreach ($args['card']['appearance'] as $property => $value) {
				$args['card']['attributes']['class'] = trim(implode(' ', [
				$args['card']['attributes']['class'],
				sprintf('%s--%s-%s', $args['card']['bem'], $property, $value)
			]));
		}
	}

	return $args;
}, 30, 1);

// Run additional filters on Card Media and Body
add_filter('ep/template/views/parts/card/card', function ($args) {
	// Apply defaults and format media options
	if (!empty($args['media'])) {
		$args = apply_filters('ep/template/views/parts/card/card-media', $args);
	}

	// Apply defaults and format body options
	if (!empty($args['body'])) {
		$args = apply_filters('ep/template/views/parts/card/card-body', $args);
	}

	return $args;
}, 40, 1);

// Filter Card Body arguments
add_filter('ep/template/views/parts/card/card-body', function ($args) {
	foreach ($args['body'] as $type => &$options) {
		// Set body item key as type if not set
		if (empty($options['type'])) {
			$options['type'] = $type;
		}

		// Check if item missing tag
		if (empty($options['tag'])) {
			$options['tag'] = 'p';
		}

		// Check if item missing attributes
		if (empty($options['attributes'])) {
			$options['attributes'] = [];
		}

		// Check if item missing class
		if (empty($options['attributes']['class'])) {
			$options['attributes']['class'] = '';
		}

		// Apply item bem class
		$options['attributes']['class'] = trim(implode(' ', [
			sprintf('%s__%s', $args['card']['bem'], $type),
			$options['attributes']['class']
		]));
	}

	return $args;
}, 10, 2);

// Filter Card Media arguments 
add_filter('ep/template/views/parts/card/card-media', function ($args) {
	// Check if media missing type
	if (empty($args['media']['type'])) {
		if (!empty($args['media']['image'])) {
			$args['media']['type'] = 'image';
		} else {
			$args['media']['type'] = 'default';
		}
	}

	// Check if media missing ratio
	if (empty($args['media']['ratio'])) {
		$args['media']['ratio'] = '1:1';
	}

	// Check if media missing attributes
	if (empty($args['media']['attributes'])) {
		$args['media']['attributes'] = [];
	}

	// Check if media missing class
	if (empty($args['media']['attributes']['class'])) {
		$args['media']['attributes']['class'] = '';
	}
	
	// Apply media bem class
	$args['media']['attributes']['class'] = trim(implode(' ', [
		sprintf('%s__media', $args['card']['bem']),
		sprintf('%s__media--%s', $args['card']['bem'], $args['media']['type']),
		$args['media']['attributes']['class']
	]));

	// Check if media missing style
	if (empty($args['media']['attributes']['style'])) {
		$args['media']['attributes']['style'] = '';
	}

	// Apply media ratio vars
	$ratio = explode(':', $args['media']['ratio']);
	if (count($ratio) == 2) {
		// Reset ratio attributes with split parameters
		$args['media']['ratio'] = [
			'str' => $args['media']['ratio'],
			'width' => $ratio[0],
			'height' => $ratio[1]
		];

		$args['media']['attributes']['style'] = trim(implode(' ', [
			$args['media']['attributes']['style'],
			sprintf('--%s-media-width: %d;', $args['card']['bem'], $args['media']['ratio']['width']),
			sprintf('--%s-media-height: %d;', $args['card']['bem'], $args['media']['ratio']['height'])
		]));
	}

	return $args;
}, 10, 2);

// Filter Card Media Image arguments
add_filter('ep/template/views/parts/card/card-media-image', function ($args) {
	// Set base $args and current $media vars
	extract($args);

	// Check if image is present and missing alt
	if (!empty($media['image']) && !isset($media['image']['alt'])) {
		$media['image']['alt'] = '';
	}

	// Check if image is present and missing lazy
	if (!empty($media['image']) && !isset($media['image']['lazy'])) {
		$media['image']['lazy'] = false;
	}

	// Reset and return $args
	return array_merge([ 'args' => $args ], [ 'media' => $media ]);
}, 10, 2);

// Filter Card Media Imgix arguments
add_filter('ep/template/views/parts/card/card-media-imgix', function ($args) {
	// Apply default Card Media Image filters
	$args = apply_filters('ep/template/views/parts/card/card-media-image', $args);

	// Set base $args and current $media vars
	extract($args);

	// Check if image missing src
	if (empty($media['image']['src'])) {
		return array_merge([ 'args' => $args ], [ 'media' => $media ]);
	}

	// Check if image missing sizes
	if (empty($media['image']['sizes'])) {
		if (!empty($media['image']['lazy'])) {
			$media['image']['sizes'] = [ 'auto' ];
		} else {
			$media['image']['sizes'] = [];
		}
	}

	// Check if image missing options
	if (empty($media['image']['options'])) {
		$media['image']['options'] = [];
	}

	// Check if image missing params
	if (empty($media['image']['params'])) {
		$media['image']['params'] = [];
	}

	// Split any Imgix parameters in the src url
	$parts = ep_imgix_parse_url($media['image']['src']);

	// Apply aspect ratio parameter if not already set based on media ratio
	if (empty($parts['params']['ar'])) {
		$parts['params']['ar'] = $media['ratio']['str'];
	}

	// Apply cropping if aspect ratio is present
	if (!empty($parts['params']['ar']) && empty($parts['params']['fit'])) {
		$parts['params']['fit'] = 'crop';
	}

	// Build default src
	$media['image']['src'] = ep_imgix_build_url($parts);

	// Strip width or height
	unset($parts['params']['w'], $parts['params']['h']);

	// Generate srcset
	$media['image']['srcset'] = ep_imgix_srcset($parts, $media['image']['options']);

	// Reset and return $args
	return array_merge([ 'args' => $args ], [ 'media' => $media ]);
}, 10, 2);
