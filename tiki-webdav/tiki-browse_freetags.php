<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'freetags';
require_once ('tiki-setup.php');
include_once ('lib/freetag/freetaglib.php');
$smarty->assign('headtitle', tra('Tags'));
$access->check_feature('feature_freetags');
$access->check_permission('tiki_p_view_freetags');

if (isset($_REQUEST['del'])) {
	if ($tiki_p_admin == 'y' || $tiki_p_unassign_freetags == 'y') {
		$freetaglib->delete_object_tag($_REQUEST['itemit'], $_REQUEST['typeit'], $_REQUEST['tag']);
	} else {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
	}
}
if ($freetaglib->count_tags() == 0) {
	$smarty->assign('msg', tra("Nothing tagged yet") . '.');
	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST['tag']) && $prefs['freetags_preload_random_search'] == 'y') {
	$tag = $freetaglib->get_tag_suggestion('', 1);
	if (!empty($tag[0])) {
		$_REQUEST['tag'] = $tag[0];
		if (strstr($tag[0], ' ')) {
			$_REQUEST['tag'] = '"'.$_REQUEST['tag'].'"';
		}
	}
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
$smarty->assign_by_ref('find', $find);
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (!isset($_REQUEST["type"])) {
	$type = '';
} else {
	$type = $_REQUEST["type"];
}
$smarty->assign('type', $type);
if (isset($_REQUEST["user_only"]) && $_REQUEST["user_only"] == 'on') {
	$view_user = $user;
	$smarty->assign('user_only', 'on');
} else {
	$view_user = '';
	$smarty->assign('user_only', 'off');
}
if (isset($_REQUEST['broaden']) && $_REQUEST['broaden'] == 'last') {
	$broaden = 'last';
} elseif ((isset($_REQUEST['broaden']) && $_REQUEST['broaden'] == 'n') || (isset($_REQUEST['stopbroaden']) && $_REQUEST['stopbroaden'] == 'on')) {
	$broaden = 'n';
} else {
	$broaden = 'y';
}
$smarty->assign('broaden', $broaden);
$tagArray = $freetaglib->_parse_tag((isset($_REQUEST['tag'])) ? $_REQUEST['tag'] : '');
$tagString = '';
foreach($tagArray as $t_ar) {
	if (strstr($t_ar, ' ')) {
		$tagString.= '"' . $t_ar . '" ';
	} else {
		$tagString.= $t_ar . ' ';
	}
}
$smarty->assign('tagString', trim($tagString));
$smarty->assign('tag', (isset($tagArray[0])) ? $tagArray[0] : '');
if (empty($_REQUEST['maxPopular'])) {
	$maxPopular = $prefs['freetags_browse_amount_tags_in_cloud'];
} else {
	$maxPopular = $_REQUEST['maxPopular'];
	$smarty->assign_by_ref('maxPopular', $maxPopular);
}
if (empty($_REQUEST['tsort_mode'])) {
	$tsort_mode = 'tag_asc';
} else {
	$tsort_mode = $_REQUEST['sort_mode'];
	$smarty->assign_by_ref('tsort_mode', $tsort_mode);
}
$most_popular_tags = $freetaglib->get_most_popular_tags('', 0, $maxPopular, $tsort_mode);
if (!empty($prefs['freetags_cloud_colors'])) {
	$colors = explode(',', $prefs['freetags_cloud_colors']);
	$prev = '';
	foreach($most_popular_tags as $id => $tag) {
		if (count($colors) == 1) {
			$i = 0;
		} elseif (count($colors) == 2) {
			$i = $prev ? 0 : 1;
		} else {
			while (($i = rand(0, count($colors) - 1)) == $prev) {
			}
		}
		$most_popular_tags[$id]['color'] = $colors[$i];
		$prev = $i;
	}
}
$smarty->assign('most_popular_tags', $most_popular_tags);
if ($broaden == 'last') {
	$broaden = 'n';
	$tagArray = array(
		$tagArray[count($tagArray) - 1]
	);
}
$objects = $freetaglib->get_objects_with_tag_combo($tagArray, $type, $view_user, $offset, $maxRecords, $sort_mode, $find, $broaden);

$smarty->assign_by_ref('objects', $objects["data"]);
$smarty->assign_by_ref('cantobjects', $objects["cant"]);
$cant = $objects['cant'];
$smarty->assign('cant', $objects['cant']);

include_once ('tiki-section_options.php');
ask_ticket('browse-freetags');

$smarty->assign('objects_with_freetags',
			array (
				'wiki page',
				'blog post',
				'article',
				'directory',
				'faq',
				'file gallery',
				'image gallery',
				'image',
				'poll',
				'quiz',
				'survey',
				'tracker',
				'tracker %d'
			)
	);
$smarty->assign('mid', 'tiki-browse_freetags.tpl');
$smarty->display("tiki.tpl");
