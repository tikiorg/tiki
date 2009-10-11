<?php

// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// To contain data services for ajax calls (autocomplete calls sa far)

require_once ('tiki-setup.php');
//require_once ('lib/ajax/ajaxlib.php');

if ($prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_autocomplete'] != 'y') {
	header("location: index.php");
	exit;
}

if (!$user) {	// only registered users so far - pending proper perms control
	header("location: index.php");
	exit;
}

if ($access->is_serializable_request() && isset($_REQUEST['listonly'])) {
	if ($_REQUEST['listonly'] == 'groups') {
		$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
		
		// TODO proper perms checking - this looks right but returns nothing for reg, and everything for admin
		// $listgroups['data'] = Perms::filter( array( 'type' => 'group' ), 'object', $listgroups['data'], array( 'object' => 'groupName' ), 'view_group' );
		
		$grs = array();
		$p = strrpos($_REQUEST['q'], '|');
		if ($p !== false) {
			$_REQUEST['q'] = substr($_REQUEST['q'], $p + 1);
		}
		foreach($listgroups['data'] as $gr) {
			if (isset($_REQUEST['q']) && stripos($gr['groupName'], $_REQUEST['q']) !== false) {
				$grs[] = $gr['groupName'];
			}
		}
		$access->output_serialized($grs);
	} else if ($_REQUEST['listonly'] == 'users') {
		$listusers = $userlib->get_users_names();
		
		// TODO also - proper perms checking
		// tricker for users? Check the group they're in, then tiki_p_group_view_members
		
		$usrs = array();
		$p = strrpos($_REQUEST['q'], '|');	// delimiter should be sent in the request
		if ($p !== false) {
			$_REQUEST['q'] = substr($_REQUEST['q'], $p + 1);
		}
		foreach($listusers as $usr) {
			if (isset($_REQUEST['q']) && stripos($usr, $_REQUEST['q']) !== false) {
				$usrs[] = $usr;
			}
		}
		$access->output_serialized($usrs);
	}

}

