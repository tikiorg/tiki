<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/userprefs/userprefslib.php');
$access->check_feature('feature_friends');
$access->check_permission('tiki_p_list_users');

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = $prefs['user_list_order'];
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$listusers = $tikilib->list_users($offset, $maxRecords, $sort_mode, $find, true);
$smarty->assign_by_ref('cant_pages', $listusers["cant"]);
//get the distance
$listdistance = array();
$listuserscountry = array();

for ($i = 0, $count_listusers = count($listusers['data']); $i < $count_listusers; $i++) {
	if ($prefs['feature_community_list_distance'] == "y") {
		$userlogin = $listusers["data"][$i]["login"];
		$distance = $userprefslib->get_userdistance($userlogin, $user);
		if (is_null($distance)) {
			$listdistance[] = NULL;
		} else {
			$listdistance[] = round($distance, 0);
		}
	}
	if ($prefs['feature_community_list_country'] == "y") {
		$userprefs = $listusers["data"][$i]["preferences"];
		$country = "None";
		
		for ($j = 0, $count_userprefs = count($userprefs); $j < $count_userprefs; $j++) {
			if ($userprefs[$j]["prefName"] == "country") $country = $userprefs[$j]["value"];
			if ($userprefs[$j]["prefName"] == "realName") $listusers["data"][$i]["realName"] = $userprefs[$j]["value"];
		}
	}
	$listuserscountry[] = $country;
}
$smarty->assign_by_ref('listusers', $listusers["data"]);
$smarty->assign_by_ref('cant_users', $listusers["cant"]);
$smarty->assign_by_ref('listdistance', $listdistance);
$smarty->assign_by_ref('listuserscountry', $listuserscountry);
$section = 'users';
include_once ('tiki-section_options.php');
// Display the template
$smarty->assign('mid', 'tiki-list_users.tpl');
$smarty->display("tiki.tpl");
