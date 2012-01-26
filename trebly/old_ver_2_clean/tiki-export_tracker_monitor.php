<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
global $monitor_filename, $stat_array;

if ($prefs['feature_trackers'] != 'y') {
	die(json_encode(array('msg' => tra('This feature is disabled').': feature_trackers')));
}
if ($prefs['feature_ajax'] != 'y') {
	die(json_encode(array('msg' => tra('This feature is disabled').': feature_ajax')));
}
if (!isset($_REQUEST['trackerId'])) {
	die(json_encode(array('msg' => tra('No tracker indicated'))));
}

function saveStatus($in_vals = array()) {
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
		die(json_encode(array('msg' => 'No monitor file found')));
	}
	
	$stat_array = unserialize(file_get_contents($monitor_filename));
	
	//$stat_array = file($monitor_filename);
	$json_data = array();
	foreach ($stat_array as $k => $v) {
		if ($k == 'user' && $v != $user) {
			$json_data['msg'] = tra("Another user is currently exporting that tracker, please try again later.");
			break;
		} else {
			$json_data[$k] = $v;
		}
	}
	
	die(json_encode($json_data));
}
