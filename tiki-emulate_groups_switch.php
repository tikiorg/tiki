<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

if (isset($_GET['from'])) {
	$orig_url = $_GET['from'];
} elseif (isset($_SERVER['HTTP_REFERER'])) {
	$orig_url = $_SERVER['HTTP_REFERER'];
} else {
	$orig_url = $prefs['tikiIndex'];
}

if ($prefs['feature_sefurl'] == 'y' && !strstr($orig_url, '.php') && !preg_match('/article[0-9]+$/', $orig_url)) {
	$orig_url = preg_replace('#\/([^\/]+)$#', '/tiki-index.php?page=$1', $orig_url);
}

// Handle form processing
if ( isset($_REQUEST["emulategroups"]) ) {
	if ( $_REQUEST["emulategroups"] == "setgroups" ) {
		// User has selected a list of groups to emulate
		$_SESSION["groups_are_emulated"]="y";
		if ( count($_REQUEST["switchgroups"]) ) {
			$groups_emulated = array();
			$dont_forget_registered = 0;
			while ( list(,$value)=each($_REQUEST["switchgroups"]) ) {
				$groups_emulated[]=$value;
				$included = $tikilib->get_included_groups($value);
				$groups_emulated = array_merge($groups_emulated, $included);
				// If one is member of a group different from Anonymous or Registered
				// then one automatically has the rights of group "Registered"
				if ( $value != "Registered" && $value != "Anonymous" ) $dont_forget_registered = 1;
			}
			if ( $dont_forget_registered == 1 && isset($user) ) {
				$groups_emulated[]="Registered";
				$included = $tikilib->get_included_groups("Registered");
				$groups_emulated = array_merge($groups_emulated, $included);
			}
			$groups_emulated = array_unique($groups_emulated);
		} else {
			// Let's say clicking with nothing selected is the same as reset
			// Saying it's the same as Anonymous would have the disadvantage of probably
			// hiding the module, so the user would need to logout
			$_SESSION["groups_are_emulated"]="n";
			$groups_emulated = array();
			$_SESSION['groups_emulated'] = serialize($groups_emulated);
		}
		$_SESSION['groups_emulated'] = serialize($groups_emulated);
	} elseif ( $_REQUEST["emulategroups"] == "resetgroups" ) {
		// User stops groups emulation (logging out is an alternate solution for user)
		$_SESSION["groups_are_emulated"]="n";
		$groups_emulated = array();
		$_SESSION['groups_emulated'] = serialize($groups_emulated);
	}
	$tikilib->invalidate_usergroups_cache($user);
}
$smarty->assign('groups_are_emulated', $_SESSION["groups_are_emulated"]);
$smarty->assign_by_ref('groups_emulated', unserialize($_SESSION['groups_emulated']));

header("location: $orig_url");
exit;
