<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.

require_once ('tiki-setup.php');
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

if ($access->ticketMatch()) {
	$tikilib->set_preference('display_timezone', $tikilib->get_preference('server_timezone'));
	if (!empty($_REQUEST['testMail'])) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$mail->setSubject(tra('Tiki Email Test'));
		$mail->setText(tra('Tiki Test email from:') . ' ' . $_SERVER['SERVER_NAME']);
		if (!$mail->send(array($_REQUEST['testMail']))) {
			$msg = tra('Unable to send mail');
			if ($tiki_p_admin == 'y') {
				$mailerrors = print_r($mail->errors, true);
				$msg .= '<br>' . $mailerrors;
			}
			Feedback::warning($msg, 'session');
		} else {
			add_feedback('testMail', tra('Test mail sent to') . ' ' . $_REQUEST['testMail'], 3);
		}
	}
}

$engine_type = getCurrentEngine();
$smarty->assign('db_engine_type', $engine_type);
$smarty->assign('now', $tikilib->now);

