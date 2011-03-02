<?php 
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function validator_distinct($input, $parameter = '', $message = '')
{
	global $trklib;
	include_once 'lib/trackers/trackerlib.php';
	
	parse_str($parameter, $arr);
	
	if (count($arr) < 2 || !isset($arr['trackerId']) || !isset($arr['fieldId'])) {
		return tra("Edit field: (Parameter needs to be 'trackerId=XX&fileId=YY' or be empty to use the current field).");
	}
	if (!isset($arr['itemId']) || $arr['itemId'] < 1) {
		$arr['itemId'] = 0;
	}
	
	$info = $trklib->get_tracker_field($arr['fieldId']);
	
	if (!$info || $info['validation'] != 'distinct' || (!empty($info['validationParam']) && $info['validationParam'] != $parameter)) {
		return tra("Edit field: (Incorrect validation parameter).");
	}
	
	if ($trklib->check_field_value_exists($input, $arr['fieldId'], $arr['itemId'])) {
		return tra(empty($message) ? "Value already exists" : $message);
	}
	
	return true;
}
