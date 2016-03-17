<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

if (isset($_REQUEST['connectprefs'])) {
	check_ticket('admin-inc-connect');
}

ask_ticket('admin-inc-connect');
global $prefs, $base_url;
$userlib = TikiLib::lib('user');
$headerlib = TikiLib::lib('header');
$smarty = TikiLib::lib('smarty');

$headerlib->add_jsfile('lib/jquery_tiki/tiki-connect.js');

if (empty($prefs['connect_site_title'])) {
	$defaults = json_encode(
		array(
			'connect_site_title' => $prefs['browsertitle'],
			'connect_site_email' => $userlib->get_admin_email(),
			'connect_site_url' => $base_url,
			'connect_site_keywords' => $prefs['metatag_keywords'],
			'connect_site_location' => $prefs['gmap_defaultx'] . ',' . $prefs['gmap_defaulty'] . ',' . $prefs['gmap_defaultz'],
		)
	);

	$headerlib->add_jq_onready(
<<<JQ
		$("#connect_defaults_btn a").click(function(){
			var connect_defaults = $defaults;
			for (var el in connect_defaults) {
				$("input[name=" + el + "]").val(connect_defaults[el]);
			}
			return false;
		});
JQ
	);
}

if ($prefs['connect_server_mode'] === 'y') {
	$connectlib = TikiLib::lib('connect_server');

	$search_str = '';

	if (isset($_REQUEST['cserver'])) {
		if ($_REQUEST['cserver'] === 'rebuild') {
			$connectlib->rebuildIndex();
		} else if (!empty($_REQUEST['cserver_search'])) {
			$search_str = $_REQUEST['cserver_search'];
		}
	}
	$smarty->assign('cserver_search_text', $search_str);
	$receivedDataStats = $connectlib->getReceivedDataStats();
	$smarty->assignByRef('connect_stats', $receivedDataStats);
	$matchingConnections = $connectlib->getMatchingConnections(empty($search_str) ? '*' : $search_str);
	$smarty->assignByRef('connect_recent', $matchingConnections);
} else {
	$smarty->assign('connect_stats', null);
	$smarty->assign('connect_recent', null);
}

$smarty->assign('jitsi_url', Services_Suite_Controller::getJitsiUrl());
