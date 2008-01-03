<?php
// $Header: /cvsroot/tikiwiki/_mods/goodies/batch_close_trackers/batch_close_trackers.php,v 1.1 2008-01-03 16:24:35 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Batch to close all the items that fieldId value is smaller than the current date
// Parameter 1: fieldId: the filedId containing the date
// Parameter 2: path: path to tiki ex:/var/www/html/tiki
// crontab line(for each day): 0 0 * * * /usr/local/bin/php ~cron/batch_close_trackers.php 282 /var/www/html/tiki
// command line: php ~cron/batch_close_trackers.php 282 /var/www/html/tiki
$fieldId = $_SERVER['argv'][1];//$_REQUEST['fieldId'];
$path = $_SERVER['argv'][2];//$_REQUEST['path'];
$error = false;
if (!empty($path)) {
	chdir($path);
}
if (empty($fieldId)) {
	echo "Missing parameter fieldId";
	$error = true;
}
require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
$field = $trklib->get_tracker_field($fieldId);
if (empty($field)) {
	echo "Incorrect parameter fieldId";
	$error = true;
}
if ($error) {
	die;
}
$date = $tikilib->now;
$items = $trklib->list_items($field['trackerId'], 0, -1, 'created_desc', array($fieldId=>$field), '', '', 'o');
$cpt = 0;
foreach($items['data'] as $item) {
	if ($item['field_values'][0]['value'] <= $date) {
		$query = "update `tiki_tracker_items` set `status`=?, `lastModif`=?  where `itemId`=?";
		$tikilib->query($query, array('c', (int)$date, (int)$item['itemId']));
		++$cpt;
	}
}
if ($cpt) {
	echo "$cpt closed\n";
}
?>