<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (isset($_REQUEST['loginprefs'])) {
	check_ticket('admin-inc-login');

	if (isset($_REQUEST['registration_choices'])) {
		$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
		$in = array();
		$out = array();
		foreach($listgroups['data'] as $gr) {
			if ($gr['groupName'] == 'Anonymous') continue;
			if ($gr['registrationChoice'] == 'y' && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) // deselect
				$out[] = $gr['groupName'];
			elseif ($gr['registrationChoice'] != 'y' && in_array($gr['groupName'], $_REQUEST['registration_choices'])) //select
				$in[] = $gr['groupName'];
		}
		if (count($in)) $userlib->set_registrationChoice($in, 'y');
		if (count($out)) $userlib->set_registrationChoice($out, NULL);
	}
}
if (!empty($_REQUEST['refresh_email_group'])) {
	$nb = $userlib->refresh_set_email_group();
	$smarty->assign('feedback', tra(sprintf(tra("%d user-group assignments"), $nb)));
}

$smarty->assign('gd_lib_found', function_exists('gd_info') ? 'y' : 'n');

$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);
ask_ticket('admin-inc-login');
