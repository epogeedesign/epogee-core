<?php

/**
 * Tab adding options related to Phpmailer and wp_mail functions.
 *
 * @since 1.0.0
 */
class Ep_Options_Mail_Tab extends Ep_Admin_Settings_Tab
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
			'mail_general_section',
			__('General', 'ep'),
			__('Manage general Mail options.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_mail_enabled',
					__('Mail Enabled', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputCheckboxField(
							$option, $states, $value,
							__('Enable Mail filters.', 'ep')
						);
					}
				);
			}
		);

		$this->AddSettingsSection(
			'mail_override_section',
			__('Overrides', 'ep'),
			__('Manage overrides to Mail options.', 'ep'),
			function ($section) {
				$this->AddSettingsField(
					$section,
					'ep_mail_host',
					__('Host', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value,
							__('Host name used when sending mail.', 'ep')
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_mail_port',
					__('Port', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value,
							__('Port number used when sending mail.', 'ep')
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_mail_username',
					__('Username', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value,
							__('Username used when sending mail.', 'ep')
						);
					}
				);

				$this->AddSettingsField(
					$section,
					'ep_mail_password',
					__('Password', 'ep'),
					function ($option, $states, $value) {
						return $this->GenerateInputTextField(
							$option, $states, $value,
							__('Password used when sending mail.', 'ep')
						);
					}
				);
			}
		);
	}
}
