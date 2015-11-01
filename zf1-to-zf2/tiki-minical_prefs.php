<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/minical/minicallib.php');
$access->check_feature('feature_minical');
$access->check_user($user);
//if ($tiki_p_minical != 'y') {
//  $smarty->assign('msg',tra("You do not have permission to use this feature"));
//  $smarty->display("error.tpl");
//  die;
//}
if (isset($_REQUEST['save'])) {
	check_ticket('minical-prefs');
	$tikilib->set_user_preference($user, 'minical_interval', $_REQUEST['minical_interval']);
	$tikilib->set_user_preference($user, 'minical_reminders', $_REQUEST['minical_reminders']);
	$tikilib->set_user_preference($user, 'minical_upcoming', $_REQUEST['minical_upcoming']);
	$tikilib->set_user_preference($user, 'minical_start_hour', $_REQUEST['minical_start_hour']);
	$tikilib->set_user_preference($user, 'minical_end_hour', $_REQUEST['minical_end_hour']);
	//  $tikilib->set_user_preference($user,'minical_public',$_REQUEST['minical_public']);
	
}
$minical_interval = $tikilib->get_user_preference($user, 'minical_interval', 60 * 60);
$minical_start_hour = $tikilib->get_user_preference($user, 'minical_start_hour', 9);
$minical_end_hour = $tikilib->get_user_preference($user, 'minical_end_hour', 20);
$minical_public = $tikilib->get_user_preference($user, 'minical_public', 'n');
$minical_upcoming = $tikilib->get_user_preference($user, 'minical_upcoming', 7);
if (isset($_REQUEST['minical_interval'])) {
	$minical_interval = $_REQUEST['minical_interval'];
}
if (isset($_REQUEST['minical_start_hour'])) {
	$minical_start_hour = $_REQUEST['minical_start_hour'];
}
if (isset($_REQUEST['minical_end_hour'])) {
	$minical_end_hour = $_REQUEST['minical_end_hour'];
}
if (isset($_REQUEST['minical_public'])) {
	$minical_interval = $_REQUEST['minical_public'];
}
if (isset($_REQUEST['minical_upcoming'])) {
	$minical_upcoming = $_REQUEST['minical_upcoming'];
}
if (isset($_REQUEST['minical_reminders'])) {
	$prefs['minical_reminders'] = $_REQUEST['minical_reminders'];
	$smarty->assign('minical_reminders', $prefs['minical_reminders']);
}
$smarty->assign('minical_interval', $minical_interval);
$smarty->assign('minical_public', $minical_public);
$smarty->assign('minical_start_hour', $minical_start_hour);
$smarty->assign('minical_end_hour', $minical_end_hour);
$smarty->assign('minical_upcoming', $minical_upcoming);
$hours = range(0, 23);
$smarty->assign('hours', $hours);
$upcoming = range(1, 20);
$smarty->assign('upcoming', $upcoming);
if (isset($_REQUEST['removetopic'])) {
	check_ticket('minical-prefs');
	$minicallib->minical_remove_topic($user, $_REQUEST['removetopic']);
}
if (isset($_REQUEST['import'])) {
	check_ticket('minical-prefs');
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
		$heading = fgetcsv($fp, 1000, ",");
		while ($data = fgetcsv($fp, 1000, ",")) {
			$subject = $data[array_search('Subject', $heading) ];
			$description = $data[array_search('Description', $heading) ];
			$start = strtotime($data[array_search('Start Date', $heading) ]);
			$start = strtotime($data[array_search('Start Time', $heading) ], $start);
			$end = strtotime($data[array_search('End Date', $heading) ]);
			$end = strtotime($data[array_search('End Time', $heading) ], $start);
			$minicallib->minical_replace_event($user, 0, $subject, $description, $start, $end - $start, 0);
		}
	}
}
// Process upload here
if (isset($_REQUEST['addtopic'])) {
	check_ticket('minical-prefs');
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
		$data = '';
		while (!feof($fp)) {
			$data.= fread($fp, 8192 * 16);
		}
		fclose($fp);
		$size = $_FILES['userfile1']['size'];
		$name = $_FILES['userfile1']['name'];
		$type = $_FILES['userfile1']['type'];
	} else {
		$size = 0;
		$name = '';
		$type = '';
		$data = '';
	}
	$minicallib->minical_upload_topic($user, $_REQUEST['name'], $name, $type, $size, $data, $_REQUEST['path']);
}
$topics = $minicallib->minical_list_topics($user, 0, -1, 'name_asc', '');
$smarty->assign('topics', $topics['data']);
$smarty->assign('cols', 4);
include_once ('tiki-mytiki_shared.php');
ask_ticket('minical-prefs');
$smarty->assign('mid', 'tiki-minical_prefs.tpl');
$smarty->display("tiki.tpl");
