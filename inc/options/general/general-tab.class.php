<?php

/**
 * Tab adding options related to general filters.
 *
 * @since 1.0.0
 */
class Ep_Options_General_Tab extends Ep_Admin_Settings_Tab
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
			'general_general_section',
			__('General', 'ep'),
			__('Manage options.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_404_page_id',
					__('404 Page', 'ep'),
					function ($option, $states, $value) {
						echo '<div class="ep-field ep-field-select">';
						wp_dropdown_pages([
							'name' => $option,
							'show_option_none' => __('&mdash; Select Page &mdash;', 'ep'),
							'option_none_value' => 0,
							'selected' => $value
						]);
						echo $this->GenerateFieldDescription(__('Override the default 404.php template with a custom page.', 'ep'));
						echo '</div>';
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_user_last_login_enabled',
					__('Last Login Enabled', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value,
							__('Enable tracking dates for user logins and add column to Users admin.', 'ep')
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_editor_gutenberg_disabled',
					__('Disable Gutenberg', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value,
							__('Use the classic editor for posts.', 'ep')
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_editor_move_excerpt_metabox',
					__('Move Excerpt Box', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value,
							__('Move the excerpt meta box to the top of the editor screen.', 'ep')
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_comments_disabled',
					__('Disable Comments', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value,
							__('Remove links, metaboxes, and functionality which supports comments.', 'ep')
						);
					}
				);
			}
		);
	}
}
