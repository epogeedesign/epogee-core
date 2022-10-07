# Epogee Core
Enhanced functions and settings for WordPress

- [Installation](#installation)
- [Posts](#posts)
  - [Post Object](#post-object)
  - [Get Post](#get-post)
  - [Get Posts](#get-posts)
  - [Format Posts](#format-posts)
  - [Filter Posts](#filter-posts)
- [Terms](#terms)
  - [Term Object](#term-object)
  - [Get Term](#get-term)
  - [Get Terms](#get-terms)
- [Templates](#templates)
  - [Locate Template](#locate-template)
  - [Parse Arguments](#parse-arguments)
- [Changelog](#changelog)

## Installation
The easiest method is by using the [Download ZIP](https://github.com/epogeedesign/epogee-core/archive/refs/heads/main.zip) link. Rename the file to `epogee-core.zip` and install the plugin normally within WordPress.

Alternatively to use the latest development version simply clone this repository into a folder named `epogee-core` within your `wp-content/plugins` directory.

This plugin supports updates from the `main` branch using the [WordPress GitHub Plugin Updater](https://github.com/radishconcepts/wordpress-github-plugin-updater).

## Posts
Instead of extending the default WordPress Post object, Epogee Core provides a robust and uniform super-class which standardizes the data format of posts, pages, and other custom post types.

### Post Object

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `id` | `int` | ID of the post mapped from `ID`. |
| `parent` | `int` | ID of the post's parent mapped from `post_parent`. |
| `author` | `int` | Author of the post mapped from `post_author`. |
| `type` | `string` | Name of the post type mapped from `post_type`. |
| `status` | `string` | Status of the post mapped from `post_status`. |
| `slug` | `string` | Url slug of the post mapped from `post_name`. |
| `title` | `string` | Title of the post mapped from `post_title`. |
| `order` | `int` | Order of the post mapped from `menu_order`. |
| `date` | `int` | Timestamp of the post mapped from `post_date`. |
| `modified` | `int` | Timestamp of the post mapped from `post_modified`. |
| `content` | `string` | Content of the post mapped from `post_content` with `the_content` filter applied. |
| `excerpt` | `string` | Excerpt of the post mapped from `post_excerpt` with `the_excerpt` filter applied. |
| `url` | `string` | Relative URL of the post using `get_permalink` and `wp_make_link_relative`. |
| `image` | `string` | Full URL of Featured Image of the post set as `_thumbnail_id` in the post meta. |
| `terms` | `array` | Optional collection of `$terms`. |
| `meta` | `array` | Optional collection of `$meta`. |
| `fields` | `array` | Optional collection of `$fields`. |

### Get Post
Use the function `ep_get_post($post)` to get a fully-formed post object. It is recommended that the global `$post` object is not overwritten as this can cause problems with other plugins and WordPress features. See examples below, the function can be used within your theme `functions.php`, `single.php`, `page.php`, or any other template file. Typically it is placed at the top of a template before the call to `get_header()`.

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$post` | `WP_Post` &#124; `int` | WordPress post object or post id. |
| `$terms` | `bool` &#124; `string[]` | Optional, default `false`. Taxonomy slugs to include. |
| `$meta` | `bool` &#124; `string[]` | Optional, default `false`. Post Meta keys to include. |
| `$fields` | `bool` &#124; `string[]` | Optional, default `false`. ACF custom field names to include. |

Examples:

* Getting `$pagedata` on `page.php` from the global `$post` object including all ACF custom fields.
```php
$pagedata = ep_get_post($post, false, false, true);
```

* Getting `$article` on `single.php` from the global `$post` object including all meta, terms, and fields.
```php
$article = ep_get_post($post, true, true, true);
```

### Get Posts
Fully-formed post objects can be retrieved directly using the function `ep_get_posts($args)` which supports the same options as the native `get_posts()`. In addition to the base options, `$args` supports the following extra properties

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$args` | `array` | [WP_Query](https://developer.wordpress.org/reference/classes/WP_Query/parse_query) args. |
| `$terms` | `bool` &#124; `string[]` | Optional, default `false`. Taxonomy slugs to include. |
| `$meta` | `bool` &#124; `string[]` | Optional, default `false`. Post Meta keys to include. |
| `$fields` | `bool` &#124; `string[]` | Optional, default `false`. ACF custom field names to include. |

* Get a collection of fully-formed `$articles` with query arguments.
```php
$articles = ep_get_posts([
  'post_type' => 'post',
  'post_status' => 'publish',
  'posts_per_page' => -1
]);
```

### Format Posts
Sometimes there is already an array of WP Posts, this can be formatted to fully-formed post objects by using the `ep_format_posts($posts)` function.

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$posts` | `int[]` &#124; `WP_Post[]` | Array of post ids or [WP_Post](https://developer.wordpress.org/reference/classes/wp_post) objects. |
| `$terms` | `bool` &#124; `string[]` | Optional, default `false`. Taxonomy slugs to include. |
| `$meta` | `bool` &#124; `string[]` | Optional, default `false`. Post Meta keys to include. |
| `$fields` | `bool` &#124; `string[]` | Optional, default `false`. ACF custom field names to include. |

* Get a collection of fully-formed `$posts` from `WP_Query`.
```php
$query = new WP_Query($args);
$posts = ep_format_posts($query->posts);
```

* Get a collection of fully-formed `$posts` from ACF relationship field.
```php
$posts = ep_format_posts(get_field('my_favorite_posts', 'option'));
```

### Filter Posts
All Epogee Core post functions support various filters to manage the `$terms`, `$meta`, and `$fields` that will be returned in addition to the final fully-formed post object itself.

* `ep/posts/terms` - Applies to `$terms` for all posts.
* `ep/posts/terms/{$type}` - Applies to `$terms` for a custom post type.
* `ep/posts/meta` - Applies to `$meta` for all posts.
* `ep/posts/meta/{$type}` - Applies to `$meta` for a custom post type.
* `ep/posts/fields` - Applies to `$fields` for all posts.
* `ep/posts/fields/{$type}` - Applies to `$fields` for a custom post type.

Fully-formed post filters:
* `ep/posts/get` - Applies to `$post` for all posts.
* `ep/posts/get/{$type}` - Applies to `$post` for a custom post type.

Examples:

* Filter `$terms` for all `post` posts to include default `category` taxonomy.
```php
add_filter('ep/posts/terms/post', function ($terms, $post) {
  return ['category'];
}, 10, 2);
```

* Filter fully-formed `$post` to customize the object for an `event` custom post type.
```php
add_filter('ep/posts/get/event', function ($post) {
  // Get event dates
  $start_date = strtotime(get_post_meta($post->id, 'start_date', true));
  $end_date = strtotime(get_post_meta($post->id, 'end_date', true));

  // Add data to $post
  $post->start_date_formatted = date('F j, Y', $start_date);
  $post->end_date_formatted = date('M j, Y', $end_date);

  return $post;
}, 10, 1);
```

## Terms
Provides a super-class extending the default WordPress Term object.

### Term Object

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `id` | `int` | ID of the term mapped from `term_id`. |
| `parent` | `int` | ID of the term's parent mapped from `parent`. |
| `slug` | `string` | URL slug of the term mapped from `slug`. |
| `name` | `string` | Name of the term mapped from `name`. |
| `description` | `string` | Description of the term mapped from `description`. |
| `group` | `string` | Group of the term mapped from `group`. |
| `taxonomy` | `string` | Taxonomy of the term mapped from `taxonomy`. |
| `taxonomyId` | `int` | ID of the Taxonomy of the term mapped from `term_taxonomy_id`. |
| `url` | `string` | Relative URL of the term using `get_term_link` and `wp_make_link_relative`. |
| `count` | `int` | Amount of posts using the current term mapped from `count`. |
| `meta` | `array` | Optional collection of `$meta`. |
| `fields` | `array` | Optional collection of `$fields`. |

### Get Term
Use the function `ep_get_term($term)` to get a fully-formed term object. See examples below, the function can be used within your theme `functions.php`, `taxonomy.php`, `index.php`, or any other template file. Typically it is placed at the top of a template before the call to `get_header()`.

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$term` | `WP_Term` &#124; `int` | WordPress term object or term id. |
| `$meta` | `bool` &#124; `string[]` | Optional, default `false`. Post Meta keys to include. |
| `$fields` | `bool` &#124; `string[]` | Optional, default `false`. ACF custom field names to include. |

* Getting `$category` on `taxonomy.php` or `index.php` using `get_queried_object_id`.
```php
$category = ep_get_term(get_queried_object_id());
```

### Get Terms
Fully-formed term objects can be retrieved directly using the function `ep_get_terms($args)` which supports the same options as the native `get_terms()`.

* Get a collection of fully-formed `$categories` with query arguments.
```php
$categories = ep_get_terms([
  'taxonomy' => 'category',
  'orderby' => 'name',
  'order' => 'ASC'
]);
```

## Templates
WordPress template functions are a bit all over the place. Epogee Core provides standardized template functions which can be extended and filtered.

### Locate Template

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$templates` | `string` &#124; `string[]` | Template(s) file to search for, relative to the theme root, in order. |
| `$load` | `bool` | Optional, default `false`. If true the template file will be loaded if it is found. |
| `$args` | `array` | Optional, default `[]`. Additional arguments passed to the template. |

* Loading `parts/template.php` located in the theme root with `$args`.
```php
ep_locate_template('parts/template', true, [
  'title' => 'My Awesome Website'
]);
```

### Parse Arguments
Extend defaults for templates and other functions using `ep_parse_args($args, $defaults)` similar to the built-in `parse_args()`. The main differences are this function supports deep and nested arrays with logic to intelligently merge and replace arguments in the return.

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$args` | `array` | Value to merge with $defaults. |
| `$defaults` | `array` | Optional, default `[]`. Array that serves as the defaults. |

## Changelog

### 1.1.0 (current)
* Add GitHub plugin updates
* Add term function `ep_format_terms()`
* Cleanup term and post functions

### 1.0.1
* Add debug functions: `ep_return_json()`, `ep_check_debug()`
* Add template functions: `ep_locate_template()`, `ep_parse_args()`, `ep_build_html_attributes()`
* Add enqueue functions: `ep_enqueue_style()`, `ep_enqueue_script()`
* Add post function `ep_post_breadcrumbs()`
* Add Yoast function `ep_yoast_set_meta()`
* Add filters to `ep_get_post()`

### 1.0.0
* Add post functions: `ep_get_post()`, `ep_get_posts()`, `ep_format_posts()`
* Add term functions `ep_get_term()`, `ep_get_terms()`
