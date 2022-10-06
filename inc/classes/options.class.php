<?php

/**
 * Service class providing a repository of defaults and abstraction for option requests used throughout Xo.
 *
 * @since 1.0.0
 */
class Ep_Options
{
	/**
	 * Collection of options which override defaults or database configurations.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $overrides = array();

	public function __construct() {
		add_action('init', array($this, 'Init'), 1, 0);
	}

	public function Init() {
		$this->SetOverrides();
	}

	/**
	 * Set overrides from EP_SETTINGS if defined.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function SetOverrides() {
		if (!defined('EP_SETTINGS'))
		    return;

		if (!$settings = json_decode(EP_SETTINGS, true))
			return;

		if (!empty($settings) && is_array($settings))
			$this->overrides = $settings;
	}

	/**
	 * Get an option value using get_option filtered by ep/options/get/{{option_name}}.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the option to get.
	 * @param mixed $value Default value if the option was not found.
	 * @return mixed Return value of the option.
	 */
	public function GetOption($name, $value = false) {
		if (isset($this->overrides[$name])) {
			$value = $this->overrides[$name];
		} else {
			$value = get_option($name, $value);
		}

		$value = apply_filters('ep/options/get/' . $name, $value);

		return $value;
	}

	/**
	 * Set an option using update_option filtered by ep/options/set/{{option_name}}.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the option to set.
	 * @param mixed $value Value to set for the given option.
	 * @return bool Whether the option was updated.
	 */
	public function SetOption($name, $value = false) {
		$value = apply_filters('ep/options/set/' . $name, $value);

		return update_option($name, $value);
	}

	/**
	 * Get the default settings for Xo.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function GetDefaultSettings() {
		$defaults = [
			// General Tab
			'ep_404_page_id' => 0,
			'ep_user_last_login_enabled' => true,

			// Editor Tab
			'ep_editor_gutenberg_disabled' => true,
			'ep_editor_move_excerpt_metabox' => true,
			'ep_comments_disabled' => true,

			// Scripts Tab
			'ep_gtm_enabled' => false,
			'ep_gtm_container_id' => '',
			'ep_maps_enabled' => false,
			'ep_maps_browser_key' => '',
			'ep_maps_server_key' => '',

			// Imgix Tab
			'ep_imgix_enabled' => true,
			'ep_imgix_custom_url' => '',
			'ep_imgix_post_thumbnails' => true,
			'ep_imgix_page_thumbnails' => false,

			// Mail Tab
			'ep_mail_enabled' => false,
			'ep_mail_host' => '',
			'ep_mail_port' => 587,
			'ep_mail_mode' => 'smtp',
			'ep_mail_username' => '',
			'ep_mail_password' => ''
		];

		return $defaults;
	}

	/**
	 * Get the defaults for Xo filtered by ep/options/defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed The defaults filtered by ep/options/defaults.
	 */
	public function GetDefaults() {
		$defaults = $this->GetDefaultSettings();

		$defaults = apply_filters('ep/options/defaults', $defaults);

		return $defaults;
	}

	/**
	 * Set the default options for Xo based on the current internal defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether any options were set.
	 */
	public function SetDefaults() {
		$defaults = $this->GetDefaults();

		$setDefaults = false;
		foreach ($defaults as $option => $value)
			if (add_option($option, $value, '', true))
				$setDefaults = true;

		return $setDefaults;
	}

	/**
	 * Reset all options for Xo based on the current internal defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether any options were set.
	 */
	public function ResetDefaults() {
		$defaults = $this->GetDefaults();

		$setOptions = false;
		foreach ($defaults as $option => $value)
			if (update_option($option, $value, true))
				$setOptions = true;

		return $setOptions;
	}

	/**
	 * Get the states of a given option filtered by ep/options/states/{{option_name}}.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option Name of the option.
	 * @return array States of the given option.
	 */
	public function GetStates($option) {
		$states = array();

		if (isset($this->overrides[$option]))
			array_push($states, 'override');

		$states = apply_filters('ep/options/states/' . $option, $states);

		return $states;
	}
}

$GLOBALS['Ep_Options'] = new Ep_Options();
