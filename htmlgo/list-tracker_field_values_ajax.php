<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
global $trklib; include_once('lib/trackers/trackerlib.php');
if ($prefs['feature_trackers'] != 'y' || $prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_autocomplete'] != 'y') {
	echo '{}';
	exit;
}
if (empty($_REQUEST['trackerId'])) {
	$field_info = $trklib->get_tracker_field($_REQUEST['fieldId']);
	$_REQUEST['trackerId'] = $field_info['trackerId'];
}
if (empty($_REQUEST['trackerId'])) {
	echo '{}';
	exit;
}
$tracker_info = $trklib->get_tracker($_REQUEST['trackerId']);
if (empty($tracker_info)) {
	echo '{}';
	exit;
}
$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info, true);
if ($tiki_p_view_trackers != 'y') {
	echo '{}';
	exit;
}
if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}

$values = $trklib->list_tracker_field_values($_REQUEST['trackerId'], $_REQUEST['fieldId'], 'opc', 'y', $_REQUEST['lang']);
header( 'Content-Type: application/json' );
echo json_encode( $values );

