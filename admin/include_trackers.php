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

$trklib = TikiLib::lib('trk');

$find = '';
$offset = 0;
$sort_mode = 'created_desc';


if ($access->ticketMatch()) {
	if (isset($_REQUEST['trkset'])) {
		$tikilib->set_preference('t_use_db', $_REQUEST['t_use_db']);
		if (substr($_REQUEST['t_use_dir'], -1) != '\\' && substr($_REQUEST['t_use_dir'], -1) != '/' && $_REQUEST['t_use_dir'] != '') {
			$_REQUEST['t_use_dir'] .= '/';
		}
		$tikilib->set_preference('t_use_dir', $_REQUEST['t_use_dir']);
	}

	if (isset($_REQUEST['action']) and isset($_REQUEST['attId'])) {
		$item = $trklib->get_item_attachment($_REQUEST['attId']);
		if ($_REQUEST['action'] == 'move2db') {
			$trklib->file_to_db($prefs['t_use_dir'] . $item['path'], $_REQUEST['attId']);
		} elseif ($_REQUEST['action'] == 'move2file') {
			$trklib->db_to_file($prefs['t_use_dir'] . md5($item['filename']), $_REQUEST['attId']);
		}
	}

	if (isset($_REQUEST['all2db'])) {
		$attachements = $trklib->list_all_attachements();
		for ($i = 0; $i < $attachements['cant']; $i++) {
			if ($attachements['data'][$i]['path']) {
				$trklib->file_to_db($prefs['t_use_dir'] . $attachements['data'][$i]['path'], $attachements['data'][$i]['attId']);
			}
		}
	} elseif (isset($_REQUEST['all2file'])) {
		$attachements = $trklib->list_all_attachements();
		for ($i = 0; $i < $attachements['cant']; $i++) {
			if (! $attachements['data'][$i]['path']) {
				$trklib->db_to_file($prefs['t_use_dir'] . md5($attachements['data'][$i]['filename']), $attachements['data'][$i]['attId']);
			}
		}
	}

	if (! empty($_REQUEST['find'])) {
		$find = $_REQUEST['find'];
	}
	if (! empty($_REQUEST['offset'])) {
		$offset = $_REQUEST['offset'];
	}
	if (! empty($_REQUEST['sort_mode'])) {
		$sort_mode = $_REQUEST['sort_mode'];
	}
}

$smarty->assign_by_ref('find', $find);
$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$attachements = $trklib->list_all_attachements($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $attachements['cant']);
$headerlib->add_cssfile('themes/base_files/feature_css/admin.css');
$smarty->assign_by_ref('attachements', $attachements['data']);

$factory = new Tracker_Field_Factory(false);
$fieldPreferences = [];

foreach ($factory->getFieldTypes() as $type) {
	$fieldPreferences[] = array_shift($type['prefs']);
}

$smarty->assign('fieldPreferences', $fieldPreferences);
