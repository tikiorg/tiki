<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: lib/setup/tracker_force_fill.php 57351 2016-02-23 09:03:22Z pat-proulx $
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) != FALSE) {
	header('location: index.php');
	exit;
}
global $user;
if (empty($user)) {
	return;
}
$tracker_id = $prefs['tracker_force_tracker_id'];
$tracker_definition = Tracker_Definition::get($tracker_id);
//user field info
$user_field_id = $tracker_definition->getUserField();
$user_field = $tracker_definition->getField($user_field_id);
$user_field_permname = $user_field['permName'];
//mandatory field info
$mandatory_field_permname = $prefs['tracker_force_mandatory_field'];
$mandatory_field_info = $tracker_definition->getFieldFromPermName($mandatory_field_permname);
$mandatory_field_id = $mandatory_field_info['fieldId'];

$fields = array_map('trim', explode(',', $prefs['tracker_force_tracker_fields']));
$trackerlib = TikiLib::lib('trk');
$item = $trackerlib->get_item($tracker_id,$user_field_id,$user);

if ($item) {
	//if the mandatory field is empty or if it's a checkbox and is set to 'n', force tracker input
	if (empty($item[$mandatory_field_id]) || ($mandatory_field_info['type'] =='c' && $item[$mandatory_field_id] == "n")) {
		$action = "update";
	} else {
		return; //do nothing
	}
} else {
	$action = "new";
}

$smarty->assign_by_ref("force_fill_action",$action);
$smarty->assign("force_fill_tracker",$tracker_id);
$smarty->assign("force_fill_user_field",$user_field_id);
$smarty->assign("force_fill_user_field_permname",$user_field_permname);
$smarty->assign("force_fill_mandatory_field",$mandatory_field_id);
$smarty->assign("force_fill_item",$item);
$smarty->assign("force_fill_fields",json_encode($fields, JSON_FORCE_OBJECT));
