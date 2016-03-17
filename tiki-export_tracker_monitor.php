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
global $monitor_filename, $stat_array;
$access = TikiLib::lib('access');

if ($prefs['feature_trackers'] != 'y') {
	$access->output_serialized(array('msg' => tra('This feature is disabled').': feature_trackers'));
	die();
}
if ($prefs['feature_ajax'] != 'y') {
	$access->output_serialized(array('msg' => tra('This feature is disabled').': feature_ajax'));
	die();
}
if (!isset($_REQUEST['trackerId'])) {
	$access->output_serialized(array('msg' => tra('No tracker indicated')));
	die();
}

/**
 * @param array $in_vals
 */
function saveStatus($in_vals = array())
{
	global $stat_array, $monitor_filename;
	$stat_array = array_merge($stat_array, $in_vals);
	$fp_mon = fopen($monitor_filename, 'w');
	fwrite($fp_mon, serialize($stat_array));
	fclose($fp_mon);
}

$monitor_filename = $prefs['tmpDir'].'/tracker_'.$_REQUEST['trackerId'].'_monitor.json';

if (isset($_REQUEST['trackerId']) && isset($_REQUEST['xuser'])) {
	if (is_file($monitor_filename)) {
		$stat_array = unserialize(file_get_contents($monitor_filename));
	} else {
		$access->output_serialized(array('msg' => 'No monitor file found'));
		die();
	}
	
	$stat_array = unserialize(file_get_contents($monitor_filename));
	
	//$stat_array = file($monitor_filename);
	$json_data = array();
	foreach ($stat_array as $k => $v) {
		if ($k == 'user' && $v != $user) {
			$json_data['msg'] = tra("Another user is currently exporting that tracker. Please try again later.").' '.tra('Or delete the file: '.$monitor_filename);
			break;
		} else {
			$json_data[$k] = $v;
		}
	}
	
	$access->output_serialized($json_data);
}
