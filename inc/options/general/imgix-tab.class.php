<?php

/**
 * Tab adding options related to Imgix and related media functions.
 *
 * @since 1.0.0
 */
class Ep_Options_Imgix_Tab extends Ep_Admin_Settings_Tab
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
			'imgix_general_section',
			__('General', 'ep'),
			__('Manage general Imgix options.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_imgix_enabled',
					__('Imgix Enabled', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value,
							__('Enable the following Imgix related filters.', 'ep')
								. '<br />&mdash; ' . __('Disable WP thumbnail sizes and automatic thumbnail generation.', 'ep')
								. '<br />&mdash; ' . __('Disable WP max image size of 2560 and scaled upload generation.', 'ep')
								. '<br />&mdash; ' . __('Remove filters in Offload Media that conflict with Imgix URLs.', 'ep')
								. '<br />&mdash; ' . __('Enable filter which adds Imgix parameters to internal WP image size requests.', 'ep')
								. '<br />&mdash; ' . __('Add filter for Yoast to prevent stripping of Imgix Parameters and set default size.', 'ep')
								. '<br />&mdash; ' . __('Add MCE plugin which adds CSS vars for the inserted media size.', 'ep')
								. '<br />&mdash; ' . __('Enable a custom Media URL (configured below).', 'ep')
								. '<br />&mdash; ' . __('Enable post thumbnail columns (configured below).', 'ep')
						);
					}
				);
			}
		);

		$this->AddSettingsSection(
			'imgix_url_section',
			__('Custom URL', 'ep'),
			__('Set a custom Media URL, useful for when not using Offload Media.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_imgix_custom_url',
					__('Custom URL', 'ep'),
					function ($option, $states, $value) {
						$upload_dir = wp_get_upload_dir();

						return $this->GenerateInputTextField(
							$option, $states, $value,
							__('URL used to replace the default Media URL base.', 'ep')
								. '<br />&mdash; ' . sprintf(__('Defaults to %s.', 'ep'), $upload_dir['baseurl'])
						);
					}
				);
			}
		);

		$this->AddSettingsSection(
			'imgix_thumbnails_section',
			__('Thumbnails Column', 'ep'),
			__('Manage the addition of the post type Featured Image column.', 'ep'),
			function ($section) {
				$post_types = get_post_types([
					'public' => true
				], 'objects');
		
				foreach ($post_types as $post_type => $post_type_config) {
					if ($post_type == 'attachment') continue;

					$this->AddSettingsField(
						$section,
						'ep_imgix_' . $post_type . '_thumbnails',
						sprintf(__('%s', 'ep'), $post_type_config->label),
						function ($option, $states, $value) use ($post_type_config) {
							return $this->GenerateInputCheckboxField(
								$option, $states, $value,
								sprintf(__('Add thumbnails column to %s admin.', 'ep'), $post_type_config->label)
							);
						}
					);
				}
			}
		);
	}
}
