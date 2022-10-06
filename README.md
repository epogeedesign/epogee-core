# Epogee Core
Enhanced functions and settings for WordPress

## Installation
The easiest method is by using the [Download ZIP](https://github.com/epogeedesign/epogee-core/archive/refs/heads/main.zip) link. Rename the file to `epogee-core.zip` and install the plugin normally within WordPress.

Alternatively to use the latest development version simply clone this repository into a folder named `epogee-core` within your `wp-content/plugins` directory.

This plugin supports updates from the `main` branch using the [WordPress GitHub Plugin Updater](https://github.com/radishconcepts/wordpress-github-plugin-updater).

## Posts
Instead of extending the default WordPress Post object, Epogee Core provides a robust and uniform super-class which standardizes the data format of posts, pages, and other custom post types.

### Get Posts
Use the function `ep_get_post($post)` to get a fully-formed post object. It is recommended that the global `$post` object is not overwritten as this can cause problems with other plugins and WordPress features. See examples below, the function can be used within your theme `functions.php`, `single.php`, `page.php`, or any other template file. Typically it is placed at the top of a template before the call to `get_header()`.

Getting `$pagedata` from the global `$post` object including all ACF custom fields.
```php
$pagedata = ep_get_post($post, false, false, true);
```

Getting `$article` from the global `$post` object including all meta, terms, and fields.
```php
$article = ep_get_post($post, true, true, true);
```
