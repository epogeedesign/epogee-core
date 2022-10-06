<?php

// Add async property to scripts
add_filter('clean_url', function ($url) {
	if (strpos($url, '#async') === false) {
		return $url;
	}

	return str_replace('#async', '', $url) . "' async='async";
}, 11, 1);

// Inject GTM container script into header and footer
add_action('init', function () {
	if (!ep_get_option('ep_gtm_enabled', false)) {
		return;
	}

	$container_id = ep_get_option('ep_gtm_container_id', '');

	if (empty($container_id)) {
		return;
	}

	add_action('wp_head', function () use ($container_id) {
		?>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','<?=$container_id?>');</script>
			<!-- End: Google Tag Manager -->
	<?php
	}, 20, 0);

	add_action('wp_footer', function () use ($container_id) {
	?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?=$container_id?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End: Google Tag Manager (noscript) -->
	<?php
	}, 20, 0);
		
}, 10, 0);

// Apply Google Maps settings
add_action('init', function () {

	if (!ep_get_option('ep_maps_enabled', false)) {
		return;
	}

	$browser_key = ep_get_option('ep_maps_browser_key', '');

	if (empty($browser_key)) {
		return;
	}

	// ...?
}, 10, 0);
