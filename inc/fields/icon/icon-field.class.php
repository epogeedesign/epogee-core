<?php

// exit if accessed directly
if (!defined('ABSPATH'))
	exit;

// check if class already exists
if (!class_exists('Ep_Icon_Field')) :

class Ep_Icon_Field extends ACF_Field
{
	/**
	 * Initializes the field type.
	 *
	 * @param	void
	 * @return	void
	 */
	function initialize() {
		// Properties
		$this->name = 'ep_icon';
		$this->label = __('Icon', 'epogee');
		$this->category = 'choice';
		$this->defaults = [
			'allow_null' => false,
			'placeholder' => __('Select Icon', 'epogee'),
			'sprite' => ''
		];
	}

	/**
	 * Renders the field settings HTML.
	 *
	 * @param	array $field The ACF field.
	 * @return	void
	 */
	function render_field_settings($field) {
		// default_value
		acf_render_field_setting($field, [
			'name'  => 'default_value',
			'type' => 'text',
			'label' => __('Default Value', 'acf')
		]);

		// Allow Null
		acf_render_field_setting($field, [
			'name' => 'allow_null',
			'type' => 'true_false',
			'label' => __('Allow Null?', 'acf'),
			'ui' => true
		]);

		// Placeholder
		acf_render_field_setting($field, [
			'name' => 'placeholder',
			'type' => 'text',
			'label' => __('Placeholder Text', 'acf')
		]);

		// Sprite
		acf_render_field_setting($field, [
			'name' => 'sprite',
			'type' => 'text',
			'label' => __('Sprite', 'epogee'),
			'placeholder' => __('Relative path to icons SVG file within the theme directory', 'epogee')
		]);
	}

	/**
	 * Renders the field input HTML.
	 *
	 * @param	array $field The ACF field.
	 * @return	void
	 */
	function render_field($field) {
		if (empty($field['sprite'])) {
			echo sprintf('<strong>%s</strong>', __('Sprite not configured.', 'epogee'));
			return;
		}

		$sprite = get_template_directory() . '/' . $field['sprite'];

		if (!file_exists($sprite)) {
			echo sprintf('<strong>%s</strong>', __('Sprite not found.', 'epogee'));
			return;
		}

		$svg = new SimpleXMLElement(file_get_contents($sprite));
		$svg->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');

		$symbols = $svg->xpath('//svg:symbol');
		$choices = [];
		foreach ($symbols as $symbol) {
			$id = ((array)$symbol->attributes()['id'])[0];
			$choices[$id] = $id;
		}

		if ($field['allow_null']) {
			$choices = ['' => $field['placeholder'] ] + $choices;
		}

		$select = [
			'id' => $field['id'],
			'class' => $field['class'],
			'name' => $field['name'],
			'data-placeholder' => $field['placeholder'],
			'data-allow_null' => $field['allow_null'],
			'data-sprite' => get_template_directory_uri() . '/' . $field['sprite'],
			'choices' => $choices,
			'value' => $field['value']
		];

		acf_select_input($select);
	}

	function input_admin_enqueue_scripts() {
		wp_enqueue_script(
			'ep-icon-field-scripts',
			plugin_dir_url(__FILE__) . '/assets/icon-field.js',
			[],
			filemtime(plugin_dir_path(__FILE__) . '/assets/icon-field.js')
		);

		wp_enqueue_style(
			'ep-icon-field-styles',
			plugin_dir_url(__FILE__) . '/assets/icon-field.css',
			[],
			filemtime(plugin_dir_path(__FILE__) . '/assets/icon-field.css')
		);
	}
}

// class_exists check
endif;
