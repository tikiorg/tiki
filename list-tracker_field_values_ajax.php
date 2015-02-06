<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$trklib = TikiLib::lib('trk');
$err = false;

if ($prefs['feature_trackers'] !== 'y' || $prefs['feature_jquery'] !== 'y' || $prefs['feature_jquery_autocomplete'] !== 'y' ||
				empty($_REQUEST['fieldId'])) {
	$err = true;
} else if (empty($_REQUEST['trackerId'])) {
	$field_info = $trklib->get_tracker_field($_REQUEST['fieldId']);
	$_REQUEST['trackerId'] = $field_info['trackerId'];
}
if (empty($_REQUEST['trackerId'])) {
	$err = true;
} else {
	$tracker_info = $trklib->get_tracker($_REQUEST['trackerId']);
	if (empty($tracker_info)) {
		$err = true;
	} else {
		$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info, true);
		if ($tiki_p_view_trackers != 'y') {
			$err = true;
		}
	}
}
if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}

$values = array();
if (!$err) {	// errors
	$vals = $trklib->list_tracker_field_values($_REQUEST['trackerId'], $_REQUEST['fieldId'], 'opc', 'y', $_REQUEST['lang']);
	if (!empty($_REQUEST['q'])) {
		foreach ( $vals as &$val ) {
			if (strpos($val, $_REQUEST['q']) !== false) {
				$values[] = $val;
			}
		}
	}
}

$access->output_serialized($values);
