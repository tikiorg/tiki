<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'categories';
require_once ('tiki-setup.php');
$categlib = TikiLib::lib('categ');
include_once ('lib/tree/BrowseTreeMaker.php');
$access->check_feature('feature_categories');

$prefsgroups = $prefs['feature_group_watches'];
global $prefsgroups, $tiki_p_admin_users, $tiki_p_admin;

$auto_query_args = array('deep', 'sort_mode', 'offset', 'find', 'type', 'parentId');

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST['parentId'])) {
	$_REQUEST['parentId'] = 0;
	$access->check_permission('tiki_p_view_category');
} else {
	$access->check_permission('tiki_p_view_category', '', 'category', $_REQUEST['parentId']);
}
$smarty->assign('parentId', $_REQUEST['parentId']);
if (isset($_REQUEST['maxRecords']) && ($_REQUEST['maxRecords'] >= 1 || $_REQUEST['maxRecords'] == -1)) {
	$maxRecords = $_REQUEST['maxRecords'];
} else {
	$maxRecords = $prefs['maxRecords'];
}

if (!isset($_REQUEST['sort_mode'])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST['sort_mode'];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
} else {
	$offset = $_REQUEST['offset'];
}
$smarty->assign_by_ref('offset', $offset);

if (!isset($_REQUEST['type'])) {
	$type = '';
} else {
	$type = $_REQUEST['type'];
}
$smarty->assign('type', $type);

if (isset($_REQUEST['find'])) {
	$find = $_REQUEST['find'];
} else {
	$find = '';
}
$smarty->assign('find', $find);

if (isset($_REQUEST['deep']) && $_REQUEST['deep'] == 'on') {
	$deep = 'on';
	$smarty->assign('deep', 'on');
} else {
	$deep = 'off';
	$smarty->assign('deep', 'off');
}
$canView = false;

// If the parent category is not zero get the category path
if ($_REQUEST['parentId']) {
	$perms = Perms::get(array( 'type' => 'category', 'object' => $_REQUEST['parentId'] ));

	$p_info = $categlib->get_category($_REQUEST['parentId']);
	if (empty($p_info)) {
		$smarty->assign('msg', tra('Incorrect parameter'));
		$smarty->display('error.tpl');
		die;
	}
	if ($prefs["feature_multilingual"] === "y")
		$p_info["name"] = tra($p_info["name"]);
	$father = $p_info['parentId'];
	$smarty->assign_by_ref('p_info', $p_info);
	$canView = $perms->view_category;
} else {
	$father = 0;
	$canView = true;
}
$smarty->assign('father', $father);

if (!$canView) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to view this page.'));
	$smarty->display('error.tpl');
	die;
}
//watches
if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_event'])) {
		if ($_REQUEST['watch_action'] == 'add_desc') {
			$name = tra('Top');
			if ($_REQUEST['watch_object'] != 0) {
				$name = $categlib->get_category_path_string_with_root($_REQUEST['watch_object']);
			}
			$categlib->watch_category_and_descendants($user, $_REQUEST['watch_object'], $name);
		} else if ($_REQUEST['watch_action'] == 'add') {
			$name = tra('Top');
			if ($_REQUEST['watch_object'] != 0) {
				$name = $categlib->get_category_path_string_with_root($_REQUEST['watch_object']);
			}
			$categlib->watch_category($user, $_REQUEST['watch_object'], $name);
		} else if ($_REQUEST['watch_action'] == 'remove_desc') {
			$categlib->unwatch_category_and_descendants($user, $_REQUEST['watch_object']);
		} else if ($_REQUEST['watch_action'] == 'remove') {
			$categlib->unwatch_category($user, $_REQUEST['watch_object']);
		}
	}
}

$ctall = $categlib->getCategories();

$descendants_curr = $categlib->get_category_descendants($_REQUEST['parentId']);
//user watches on current level
$usercatwatches_curr = $tikilib->get_user_watches($user, 'category_changed');
$eyes_curr = add_watch_icons($descendants_curr, $usercatwatches_curr, $_REQUEST['parentId'], $_REQUEST['parentId'],
	$deep, $user, null);
$smarty->assign_by_ref('eyes_curr', $eyes_curr);

foreach ($ctall as &$c) {
	$descendants = $categlib->get_category_descendants($c['categId']);
	$usercatwatches = $tikilib->get_user_watches($user, 'category_changed');
	$eyes = add_watch_icons($descendants, $usercatwatches, $_REQUEST['parentId'], $c['categId'], $deep, $user, $c['name']);
	$c['eyes'] = $eyes;
}
unset($c);
$tree_nodes = array();
foreach ($ctall as $c) {
	if ($prefs['category_browse_count_objects'] === 'y' || isset($_REQUEST['count'])) { 	// show/hide count button TODO after 12.0
	 	// display correct count of objects depending on browse in and find filters -- luci Thu 05 Sep 2013 10:15:50 PM UTC
		$objectcount = $categlib->list_category_objects(
			$c['categId'],
			0,
			-1,
			'',
			$type,
			$find,
			$deep == 'on',
			(!empty($_REQUEST['and'])) ? true : false
		);
		$countString = '<span class="object-count badge">' . $objectcount['cant'] . '</span>';
	} else {
		$countString = '';
	}

	$tree_nodes[] = array(
		'id' => $c['categId'],
		'categId' => $c['categId'],
		'parent' => $c['parentId'],
		'parentId' => $c['parentId'],
		'data' => $countString .
							$c['eyes'] . ' <a class="catname" href="tiki-browse_categories.php?parentId=' . $c['categId'] .
							'&amp;deep=' . $deep . '&amp;type='. urlencode($type) . '">' . htmlspecialchars($c['name']) .'</a> ',
	);
}
$res  = '';
$tm = new BrowseTreeMaker('categ');
foreach ($categlib->findRoots($tree_nodes) as $node) {
	$res .= $tm->make_tree($node, $tree_nodes);
}

$smarty->assign('tree', $res);

$objects = $categlib->list_category_objects(
	$_REQUEST['parentId'],
	$offset,
	$maxRecords,
	$sort_mode,
	$type,
	$find,
	$deep == 'on',
	(!empty($_REQUEST['and'])) ? true : false
);

if ($deep == 'on') {
	foreach ($objects['data'] as &$object) {
		$object['categName'] = $tikilib->other_value_in_tab_line($ctall, $object['categId'], 'categId', 'name');
	}
}


$smarty->assign_by_ref('objects', $objects['data']);
$smarty->assign_by_ref('cant_pages', $objects['cant']);
$smarty->assign_by_ref('maxRecords', $maxRecords);
include_once ('tiki-section_options.php');
ask_ticket('browse-categories');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
if (isset($_GET['plain'])) {				// used by profile repositories to list available profiles (see _htaccess for more info)
	header('Content-Type: text/plain');
	foreach ($objects['data'] as &$object) echo "{$object['categName']}\t{$object['type']}\t{$object['itemId']}\n";
	exit;
} else if (isset($_GET['links'])) {			// used to generate plain text sitemaps for submitting to search engines (see _htaccess for more info)
	header('Content-Type: text/plain');
	if ($prefs['feature_sefurl'] === 'y') {
		foreach ($objects['data'] as &$object) echo "$base_url{$object['sefurl']}\n";
	} else {
		foreach ($objects['data'] as &$object) echo "$base_url{$object['href']}\n";
	}
	exit;
} else {
	// Display the template
	$smarty->assign('mid', 'tiki-browse_categories.tpl');
	$smarty->display('tiki.tpl');
}

/**
 * @param $descendants
 * @param $usercatwatches
 * @param $requestid
 * @param $categid
 * @param $deep
 * @param $user
 * @return bool|string
 */
function add_watch_icons($descendants, $usercatwatches, $requestid, $categid, $deep, $user, $name)
{
	global $prefs;
	if (!$user || $prefs["feature_user_watches"] != 'y') {
		 return false;
	}
	global $prefsgroups, $tiki_p_admin_users, $tiki_p_admin;
	$categlib = TikiLib::lib('categ');
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_icon');
	$smarty->loadPlugin('smarty_function_popup');

	$section = 'categories';
	$nodesc = count($descendants);
	$watch_desc = 'n';
	$watch_this = 'n';
	$eyes = $eyesgroup = '';
	if ($categid == 0) {
		$tip_rem_desc = tra('Stop watching all categories');
		$tip_add_desc = tra('Watch all categories');
		$tip_group = tra('Group watches for all categories');
	} else {
		$tip_rem_desc = tra('Stop watching this category and its descendants');
		$tip_add_desc = tra('Watch this category and its descendants');
		$tip_group = tra('Group watches for this category');
	}
	$eye_rem_desc = '<a href="tiki-browse_categories.php?' . 'parentId=' . $requestid
		. '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep
		. '&amp;watch_action=remove_desc" class="catname">'
		. smarty_function_icon(['name'=> 'stop-watching', '_menu_text' => 'y', '_menu_icon' => 'y',
			'alt' =>  $tip_rem_desc], $smarty) . '</a>';

	$eye_rem = 	'<a href="tiki-browse_categories.php?' . 'parentId=' . $requestid
		. '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep
		. '&amp;watch_action=remove" class="catname">'
		. smarty_function_icon(['name'=> 'stop-watching', '_menu_text' => 'y', '_menu_icon' => 'y',
			'alt' =>  tra('Stop watching this category')], $smarty) . '</a>';

	$eye_add_desc = '<a href="tiki-browse_categories.php?' . 'parentId=' . $requestid
		. '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep
		. '&amp;watch_action=add_desc" class="catname">' . smarty_function_icon(['name'=> 'watch',
			'_menu_text' => 'y', '_menu_icon' => 'y', 'alt' =>  $tip_add_desc], $smarty) . '</a>';

	$eye_add = 	'<a href="tiki-browse_categories.php?' . 'parentId=' . $requestid
		. '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep
		. '&amp;watch_action=add">' . smarty_function_icon(['name'=> 'watch', '_menu_text' => 'y', '_menu_icon' => 'y',
			'alt' =>  tra('Watch this category')], $smarty) . '</a>';

	foreach ($descendants as $descendant) {
		if ($nodesc > 1) {
			//this category and descendants
			foreach ($usercatwatches as $usercatwatch) {
				if ($usercatwatch['object'] == $descendant || $descendant == 0) {
					$watch_desc = 'y';
					break;
				} else {
					$watch_desc = 'n';
				}
			}
			if ($watch_desc == 'n') {
				$eyes = $eye_add_desc;
				break;
			} else {
				$eyes = $eye_rem_desc;
			}
		}
	}
	//this category only
	foreach ($usercatwatches as $usercatwatch) {
		if ($usercatwatch['object'] == $categid) {
			$watch_this = 'y';
			break;
		} else {
			$watch_this = 'n';
		}
	}
	if ($categid == 0) {
		$eyes .= '';
	} elseif ($watch_this == 'n') {
		$eyes = $eye_add . $eyes;
	} else {
		$eyes = $eye_rem . $eyes;
	}
	//group watches
	if ($prefsgroups == 'y' && ( $tiki_p_admin_users == 'y' || $tiki_p_admin == 'y' )) {
		$objName = '';
		if ($categid == 0) {
			$objName = 'Top';
		} else {
			$objName = $categlib->get_category_path_string_with_root($categid);
		}
		$eyesgroup = '<a href="tiki-object_watches.php?' . 'objectId=' . $categid
			. '&amp;watch_event=category_changed&amp;objectType=Category&amp;objectName=' . urlencode($objName)
			. '&amp;objectHref=tiki-browse_categories.php?parentId=' . $categid . '&amp;deep=' . $deep . '">'
			. smarty_function_icon(['name'=> 'watch', '_menu_text' => 'y', '_menu_icon' => 'y',
				'alt' =>  $tip_group], $smarty) . '</a>';
	}
	$alleyes = $eyes . $eyesgroup;
	$escapedeyes = htmlspecialchars(strtr($alleyes, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r",
		"\n" => "\\n", "</" => "<\/" )), ENT_QUOTES, 'UTF-8', true);
	$popupparams = ['trigger' => 'click', 'fullhtml' => 1, 'center' => true, 'text' =>  $escapedeyes];
	return '<a class="tips" title="' . tra('Monitoring') . '" href="#" '. smarty_function_popup( $popupparams, $smarty)
		. 'style="padding:0; margin:0; border:0">' . smarty_function_icon(['name'=> 'wrench'], $smarty) . '</a>';


//	return $eyes . $eyesgroup;
}
