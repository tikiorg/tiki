<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (! isset($_REQUEST['interlist'])) {
	$_REQUEST['interlist'] = [];
}
if (! isset($_REQUEST['known_hosts'])) {
	$_REQUEST['known_hosts'] = [];
}

$smarty->assign('serverFields', ['name', 'host', 'port', 'path', 'groups']);

if ($access->ticketMatch()) {
	if (isset($_REQUEST['del'])) {
		//TODO add service for confirm popup
//		$access->check_authenticity(tra('Are you sure you want to remove this server?'));
		foreach ($prefs['interlist'] as $k => $i) {
			if ($k == $_REQUEST['del']) {
				unset($_REQUEST['interlist'][$k]);
			}
		}
		simple_set_value('interlist');
		//to refresh interlist dropdown - not sure if there's a better way to do this
		$access->redirect($_SERVER['REQUEST_URI'], '', 200);
	}
	if (isset($_REQUEST['delk'])) {
		//TODO add service for confirm popup
//		$access->check_authenticity(tra('Are you sure you want to remove this host?'));
		foreach ($prefs['known_hosts'] as $k => $i) {
			if ($k == $_REQUEST['delk']) {
				unset($_REQUEST['known_hosts'][$k]);
			}
		}
		simple_set_value('known_hosts');
	}
	if (isset($_REQUEST['new']) and is_array($_REQUEST['new']) and $_REQUEST['new']['name']) {
		$new["{$_REQUEST['new']['name']}"] = $_REQUEST['new'];
		$_REQUEST['interlist'] += $new;
		simple_set_value('interlist');
	}

	if (isset($_REQUEST['newhost']) and is_array($_REQUEST['newhost']) and $_REQUEST['newhost']['key']) {
		$newhost["{$_REQUEST['newhost']['key']}"] = $_REQUEST['newhost'];
		$_REQUEST['known_hosts'] += $newhost;
		simple_set_value('known_hosts');
	}
	if (! empty($_REQUEST['known_hosts'])) {
		foreach ($_REQUEST['known_hosts'] as $k => $v) {
			if (isset($_REQUEST['known_hosts'][$k]['allowusersregister'])) {
				$_REQUEST['known_hosts'][$k]['allowusersregister'] = 'y';
			}
			if (empty($_REQUEST['known_hosts'][$k]['name'])
				&& empty($_REQUEST['known_hosts'][$k]['key'])
				&& empty($_REQUEST['known_hosts'][$k]['ip'])
				&& empty($_REQUEST['known_hosts'][$k]['contact'])) {
				unset($_REQUEST['known_hosts'][$k]);
			}
		}
		simple_set_value('known_hosts');
	}
}
