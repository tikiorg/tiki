<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_intertiki.php,v 1.5 2006-11-15 14:02:05 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.

// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (isset($_REQUEST["alter_tiki_prefs_table"])) {
	$alter_result = alterprefs();
}


if (!isset($_REQUEST['interlist'])) $_REQUEST['interlist']= array();
if (!isset($_REQUEST['known_hosts'])) $_REQUEST['known_hosts']= array();

if (isset($_REQUEST['del'])) {
	check_ticket('admin-inc-intertiki');
	$interlist = unserialize($tikilib->get_preference("interlist",''));
	$_REQUEST["intertikiclient"] = true;
	foreach ($interlist as $k=>$i) {
		if ($k != $_REQUEST['del']) {
			$_REQUEST['interlist'][$k] = $i;
		}
	}
}

if (isset($_REQUEST['delk'])) {
	check_ticket('admin-inc-intertiki');
	$known_hosts = unserialize($tikilib->get_preference("known_hosts",''));
	$_REQUEST["intertikiserver"] = true;
	foreach ($known_hosts as $k=>$i) {
		if ($k != $_REQUEST['delk']) {
			$_REQUEST['known_hosts'][$k] = $i;
		}
	}
}

if (isset($_REQUEST["intertikiclient"])) {
	check_ticket('admin-inc-intertiki');
	if (isset($_REQUEST['new']) and is_array($_REQUEST['new']) and $_REQUEST['new']['name']) {
		$new["{$_REQUEST['new']['name']}"] = $_REQUEST['new'];
		$_REQUEST['interlist'] += $new;
	}
	$_REQUEST['interlist'] = serialize($_REQUEST['interlist']);
	simple_set_value('interlist');
	simple_set_value('tiki_key');
	simple_set_value('feature_intertiki_mymaster');

	simple_set_toggle('feature_intertiki_import_preferences');
	simple_set_toggle('feature_intertiki_import_groups');
	simple_set_value('feature_intertiki_imported_groups');
}

if (isset($_REQUEST["intertikiserver"])) {
	check_ticket('admin-inc-intertiki');
	simple_set_toggle('feature_intertiki_server');
	simple_set_value('intertiki_logfile');
	simple_set_value('intertiki_errfile');
	if (isset($_REQUEST['newhost']) and is_array($_REQUEST['newhost']) and $_REQUEST['newhost']['key']) {
		$newhost["{$_REQUEST['newhost']['key']}"] = $_REQUEST['newhost'];
		$_REQUEST['known_hosts'] += $newhost;
	}
	$_REQUEST['known_hosts'] = serialize($_REQUEST['known_hosts']);
	simple_set_value('known_hosts');
}

$interlist   = unserialize($tikilib->get_preference("interlist",''));
$known_hosts = unserialize($tikilib->get_preference("known_hosts",''));

$smarty->assign("interlist", $interlist);
$smarty->assign("known_hosts", $known_hosts);
$smarty->assign("intertiki_server", $tikilib->get_preference("intertiki_server", "n"));

ask_ticket('admin-inc-intertiki');
?>
