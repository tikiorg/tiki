<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.

require_once ('tiki-setup.php');
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));
if (isset($_REQUEST['new_prefs'])) {
	$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$in = array();
	$out = array();
	foreach($listgroups['data'] as $gr) {
		if ($gr['groupName'] == 'Anonymous') continue;
		if ($gr['registrationChoice'] == 'y' && isset($_REQUEST['registration_choices']) && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) // deselect
		$out[] = $gr['groupName'];
		elseif ($gr['registrationChoice'] != 'y' && isset($_REQUEST['registration_choices']) && in_array($gr['groupName'], $_REQUEST['registration_choices'])) //select
		$in[] = $gr['groupName'];
	}
	check_ticket('admin-inc-general');
	$pref_toggles = array(
		'feature_wiki_1like_redirection',
	);
	foreach($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	$pref_byref_values = array(
		'server_timezone',
	);
	foreach($pref_byref_values as $britem) {
		byref_set_value($britem);
	}
	$tikilib->set_preference('display_timezone', $tikilib->get_preference('server_timezone'));
	// Special handling for tied fields: tikiIndex, urlIndex and useUrlIndex
	
}
$smarty->assign('now', $tikilib->now);

if (!empty($_REQUEST['testMail'])) {
	include_once('lib/webmail/tikimaillib.php');
	$mail = new TikiMail();
	$mail->setSubject(tra('Tiki Email Test'));
	$mail->setText(tra('Tiki Test email from:').' '.$_SERVER['SERVER_NAME']);
	if (!$mail->send(array($_REQUEST['testMail']))) {
		$msg = tra('Unable to send mail') ;
		if ($tiki_p_admin == 'y') {
			$mailerrors = print_r($mail->errors, true);
			$msg .= $mailerrors;
		}
		$smarty->assign('error_msg', $msg);
	} else {
		 add_feedback( 'testMail', tra('Test mail sent to').' '.$_REQUEST['testMail'], 3 );
	}
}
ask_ticket('admin-inc-general');
