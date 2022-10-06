<?php

add_action('init', function () {
	if (!ep_get_option('ep_mail_enabled', false)) {
		return;
	}

	add_action('phpmailer_init', function (PHPMailer $phpmailer) {
		// $phpmailer->isSMTP();

		if ($mailHost = ep_get_option('ep_mail_host', false)) {
			$phpmailer->Host = $mailHost;
		}

		if ($mailPort = ep_get_option('ep_mail_port', false)) {
			$phpmailer->Port = $mailPort;
		}

		// if (defined('EP_MAIL_MODE')) {
		// 	$phpmailer->Mailer = EP_MAIL_MODE;
		// }

		if ($mailUser = ep_get_option('ep_mail_username', false)) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $mailUser;
		}

		if ($mailPass = ep_get_option('ep_mail_password', false)) {
			$phpmailer->Password = $mailPass;
		}

		// if (defined('EP_MAIL_DEBUG')) {
		// 	$phpmailer->SMTPDebug = EP_MAIL_DEBUG;
		// }

		// if (defined('EP_MAIL_HEADERS')) {
		// 	$headers = unserialize(EP_MAIL_HEADERS);
		// 	foreach ($headers as $header)
		// 		$phpmailer->addCustomHeader($header);
		// }
	});
}, 10, 0);
