<?php
/**
 * Plugin Name:  Epogee Core
 * Plugin URI:   https://github.com/epogeedesign/epogee-core
 * Description:  Enhanced functions and settings for WordPress.
 * Version:      1.1.1
 * Author:       Epogee Design
 * Author URI:   https://epogeedesign.com/
 */

$ep_folder = dirname(__FILE__);

// Composer Autoload
require_once 'vendor/autoload.php';

// Core Classes
require_once 'inc/classes/post.class.php';
require_once 'inc/classes/term.class.php';
require_once 'inc/classes/options.class.php';
require_once 'inc/classes/admin-page.class.php';
require_once 'inc/classes/admin-tab.class.php';

// Functions
require_once 'inc/functions/options.inc.php';
require_once 'inc/functions/templates.inc.php';
require_once 'inc/functions/filters.inc.php';
require_once 'inc/functions/rest.inc.php';
require_once 'inc/functions/fields.inc.php';
require_once 'inc/functions/posts.inc.php';
require_once 'inc/functions/terms.inc.php';
require_once 'inc/functions/imgix.inc.php';
require_once 'inc/functions/yoast.inc.php';
require_once 'inc/functions/sitemap.inc.php';

// WordPress Filters
require_once 'inc/filters/updates.inc.php';
require_once 'inc/filters/editor.inc.php';
require_once 'inc/filters/comments.inc.php';
require_once 'inc/filters/imgix.inc.php';
require_once 'inc/filters/scripts.inc.php';
require_once 'inc/filters/phpmailer.inc.php';
require_once 'inc/filters/404-page.inc.php';
require_once 'inc/filters/last-login.inc.php';

// Epogee Options
require_once 'inc/options/options.inc.php';

// Custom Fields Types
require_once 'inc/fields/fields.inc.php';

// Signal Init
add_action('init', function () { 
	do_action('ep/init');
}, 10, 0);
