<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'newsletters';
require_once ('tiki-setup.php');
$access->check_feature('feature_newsletters');

global $nllib;
include_once ('lib/newsletters/nllib.php');
$auto_query_args = array(
	'nlId',
	'offset',
	'sort_mode',
	'find'
);
if (!isset($_REQUEST["nlId"])) {
	$_REQUEST["nlId"] = 0;
}
$smarty->assign('nlId', $_REQUEST["nlId"]);
$perms = Perms::get(array('type'=>'newsletter', 'object'=>$_REQUEST['nlId']));

if ($perms->admin_newsletters != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$defaultArticleClipRange = 3600 * 24; // one day
if ($_REQUEST["nlId"]) {
	$info = $nllib->get_newsletter($_REQUEST["nlId"]);
	if (empty($info)) {
		$smarty->assign('msg', tra('Newsletter does not exist'));
		$smarty->display('error.tpl');
		die;
	}
	$update = "";
	$info["articleClipTypes"] = unserialize($info["articleClipTypes"]);
	$info["articleClipRangeDays"] = $info["articleClipRange"] / 3600 / 24;
} else {
	$info = array(
		'nlId' => 0,
		'name' => '',
		'description' => '',
		'allowUserSub' => 'y',
		'allowAnySub' => 'n',
		'unsubMsg' => 'y',
		'validateAddr' => 'y',
		'allowTxt' => 'y',
		'allowArticleClip' => 'y',
		'autoArticleClip' => 'n',
		'articleClipRange' => $defaultArticleClipRange,
		'articleClipRangeDays' => $defaultArticleClipRange / 3600 / 24,
		'articleClipTypes' => array()
	);
	$update = "y";
}
$smarty->assign('info', $info);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$nllib->remove_newsletter($_REQUEST["remove"]);
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-nl');
	if (isset($_REQUEST["allowUserSub"]) && $_REQUEST["allowUserSub"] == 'on') {
		$_REQUEST["allowUserSub"] = 'y';
	} else {
		$_REQUEST["allowUserSub"] = 'n';
	}
	if (isset($_REQUEST["allowAnySub"]) && $_REQUEST["allowAnySub"] == 'on') {
		$_REQUEST["allowAnySub"] = 'y';
	} else {
		$_REQUEST["allowAnySub"] = 'n';
	}
	if (isset($_REQUEST["unsubMsg"]) && $_REQUEST["unsubMsg"] == 'on') {
		$_REQUEST["unsubMsg"] = 'y';
	} else {
		$_REQUEST["unsubMsg"] = 'n';
	}
	if (isset($_REQUEST["validateAddr"]) && $_REQUEST["validateAddr"] == 'on') {
		$_REQUEST["validateAddr"] = 'y';
	} else {
		$_REQUEST["validateAddr"] = 'n';
	}
	if (isset($_REQUEST["allowTxt"]) && $_REQUEST["allowTxt"] == 'on') {
		$_REQUEST["allowTxt"] = 'y';
	} else {
		$_REQUEST["allowTxt"] = 'n';
	}
	if (isset($_REQUEST["allowArticleClip"]) && $_REQUEST["allowArticleClip"] == 'on') {
		$_REQUEST["allowArticleClip"] = 'y';
	} else {
		$_REQUEST["allowArticleClip"] = 'n';
	}
	if (isset($_REQUEST["autoArticleClip"]) && $_REQUEST["autoArticleClip"] == 'on') {
		$_REQUEST["autoArticleClip"] = 'y';
	} else {
		$_REQUEST["autoArticleClip"] = 'n';
	}
	if (isset($_REQUEST["articleClipRangeDays"]) && $_REQUEST["articleClipRangeDays"]) {
		$articleClipRange = 3600 * 24 * $_REQUEST["articleClipRangeDays"];
	} else {
		$articleClipRange = $defaultArticleClipRange; // default to 1 day
	}
	if (!empty($_REQUEST["articleClipTypes"])) {
		$articleClipTypes = serialize($_REQUEST["articleClipTypes"]);
	} else {
		$articleClipTypes = '';
	}
	if (!isset($_REQUEST['frequency'])) $_REQUEST['frequency'] = 0;
	$sid = $nllib->replace_newsletter($_REQUEST["nlId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["allowUserSub"], $_REQUEST["allowAnySub"], $_REQUEST["unsubMsg"], $_REQUEST["validateAddr"],$_REQUEST["allowTxt"],$_REQUEST["frequency"],$_REQUEST["author"], $_REQUEST["allowArticleClip"], $_REQUEST["autoArticleClip"], $articleClipRange, $articleClipTypes );
	
	$info = array(
		'nlId' => 0,
		'name' => '',
		'description' => '',
		'allowUserSub' => 'y',
		'allowAnySub' => 'n',
		'unsubMsg' => 'y',
		'validateAddr' => 'y',
		'allowTxt' => 'y'
	);
	$smarty->assign('nlId', 0);
	$smarty->assign('info', $info);
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
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
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $nllib->list_newsletters($offset, $maxRecords, $sort_mode, $find, $update, array(
	"tiki_p_admin_newsletters"
));

// get Article types for clippings feature
$articleTypes = array();
if ($prefs["feature_articles"] == 'y') {
	include_once ('lib/articles/artlib.php');
	$allTypes = $artlib->list_types();
	foreach ($allTypes as $t) {
		$articleTypes[] = $t["type"];
	}
}
$smarty->assign('articleTypes', $articleTypes);

$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
include_once ('tiki-section_options.php');
ask_ticket('admin-nl');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_newsletters.tpl');
$smarty->display("tiki.tpl");
