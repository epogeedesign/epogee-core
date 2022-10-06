<?php

/**
 * Tab adding options related to Scripts.
 *
 * @since 1.0.0
 */
class Ep_Options_Scripts_Tab extends Ep_Admin_Settings_Tab
{
	/**
	 * Add the various settings sections for the General tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function Init() {
		$this->AddSettingsSection(
			'scripts_gtm_section',
			__('Google Tag Manager', 'ep'),
			__('Manage general Google Tag Manager options.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_gtm_enabled',
					__('GTM Enabled', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_gtm_container_id',
					__('Container ID', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value
						);
					}
				);
			}
		);

		$this->AddSettingsSection(
			'scripts_maps_section',
			__('Google Maps', 'ep'),
			__('Manage general Google Maps options.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_maps_enabled',
					__('Maps Enabled', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_maps_browser_key',
					__('Browser Key', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_maps_server_key',
					__('Server Key', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value
						);
					}
				);
			}
		);
	}
}
