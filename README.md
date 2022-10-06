# Epogee Core
Enhanced functions and settings for WordPress

## Installation
The easiest method is by using the [Download ZIP](https://github.com/epogeedesign/epogee-core/archive/refs/heads/main.zip) link. Rename the file to `epogee-core.zip` and install the plugin normally within WordPress.

Alternatively to use the latest development version simply clone this repository into a folder named `epogee-core` within your `wp-content/plugins` directory.

This plugin supports updates from the `main` branch using the [WordPress GitHub Plugin Updater](https://github.com/radishconcepts/wordpress-github-plugin-updater).

## Posts
Instead of extending the default WordPress Post object, Epogee Core provides a robust and uniform super-class which standardizes the data format of posts, pages, and other custom post types.

### Get Post
Use the function `ep_get_post($post)` to get a fully-formed post object. It is recommended that the global `$post` object is not overwritten as this can cause problems with other plugins and WordPress features. See examples below, the function can be used within your theme `functions.php`, `single.php`, `page.php`, or any other template file. Typically it is placed at the top of a template before the call to `get_header()`.

Getting `$pagedata` from the global `$post` object including all ACF custom fields.
```php
$pagedata = ep_get_post($post, false, false, true);
```

Getting `$article` from the global `$post` object including all meta, terms, and fields.
```php
$article = ep_get_post($post, true, true, true);
```

### Get Posts
Fully-formed post objects can be retrieved directly using the function `ep_get_posts($args)` which supports the same options as the native `get_posts()`.

Get a collection of fully-formed `$articles` with query arguments.
```php
$articles = ep_get_posts([
  'post_type' => 'post',
  'post_status' => 'publish',
  'posts_per_page' => -1
]);
```

### Format Posts
Sometimes there is already an array of WP Posts, this can be formatted to fully-formed post objects by using the `ep_format_posts($posts)` function.

Get a collection of fully-formed `$posts` from `WP_Query`.
```php
$query = new WP_Query($args);
$posts = ep_format_posts($query->posts);
```

### Filter Posts
All Epogee Core post functions support various filters to manage the `$terms`, `$meta`, and `$fields` that will be returned in addition to the final fully-formed post object itself.

* `ep/posts/terms` - Applies to `$terms` for all posts.
* `ep/posts/terms/{$post_type}` - Applies to `$terms` for a custom post type.

## Changelog

### 1.1.0 (current)
* Add GitHub plugin updates

### 1.0.1
* Add debug functions: `ep_return_json()`, `ep_check_debug()`
* Add template functions: `ep_locate_template()`, `ep_parse_args()`, `ep_build_html_attributes()`
* Add enqueue functions: `ep_enqueue_style()`, `ep_enqueue_script()`
* Add post function `ep_post_breadcrumbs()`
* Add Yoast function `ep_yoast_set_meta()`
* Add filters to `ep_get_post()`

### 1.0.0
* Add post functions: `ep_get_post()`, `ep_get_posts()`, `ep_format_posts()`
