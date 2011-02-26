<?php

// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(array(
	'staticKeyFilters' => array(
		'categId' => 'digits',
		'action' => 'alpha',
	),
	'staticKeyFiltersForArrays' => array(
		'objects' => 'text',
	),
	'catchAllUnset' => null,
));

require_once 'tiki-setup.php';
require_once 'lib/categories/categlib.php';
require_once 'lib/tree/categ_browse_tree.php';

$smarty->assign('headtitle', tra('Categories'));
$access->check_feature('feature_categories');

if (isset($_POST['action'])) {
	$perms = Perms::get('category', $_REQUEST['categId']);

	if ($_POST['action'] == 'add' && $perms->add_objects) {
		foreach ($_POST['objects'] as $identifier) {
			list($type, $id) = explode(':', $identifier, 2);
			$objectPerms = Perms::get($type, $id);

			if ($objectPerms->modify_object_categories) {
				$categlib->categorize_any($type, $id, $_REQUEST['categId']);
			}
		}
	} elseif ($_POST['action'] == 'remove' && $perms->remove_objects) {
		foreach ($_POST['objects'] as $identifier) {
			list($type, $id) = explode(':', $identifier, 2);
			$objectPerms = Perms::get($type, $id);

			if ($objectPerms->modify_object_categories && $oId = $categlib->is_categorized($type, $id)) {
				$categlib->uncategorize($oId, $_REQUEST['categId']);
			}
		}
	}

	$objects = $categlib->list_category_objects($_REQUEST['categId'], 0, 1, 'name_asc');
	echo json_encode(array(
		'count' => $objects['cant'],
	));
	exit;
}

// Generate the category tree {{{
$ctall = $categlib->get_all_categories_respect_perms(null, 'view_category');

$tree_nodes = array();
foreach($ctall as $c) {
	$url = htmlentities('tiki-edit_categories.php?' . http_build_query(array(
		'categId' => $c['categId'],
	)), ENT_QUOTES, 'UTF-8');
	$name = htmlentities($c['name'], ENT_QUOTES, 'UTF-8');
	$perms = Perms::get('category', $c['categId']);

	$add = $perms->add_object ? '<span class="control categ-add"></span>' : '';
	$remove = $perms->remove_object ? '<span class="control categ-remove"></span>' : '';

	$body = <<<BODY
$add
$remove
<span class="object-count">{$c['objects']}</span>
<a class="catname" href="{$url}">{$name}</a>
BODY;

	$tree_nodes[] = array(
		'id' => $c['categId'],
		'parent' => $c['parentId'],
		'data' => $body,
	);
}

$orphans = $categlib->list_orphan_objects(0, 0, 'name_asc');
$tree_nodes[] = array(
	'id' => 'orphan',
	'parent' => '0',
	'data' => '<span class="object-count">' . $orphans['cant'] . '</span><a class="catname" href="tiki-edit_categories.php?categId=0"><em>' . tr('Orphans') . '</em></a>',
);

$tm = new CatBrowseTreeMaker('categ');
$res = $tm->make_tree(0, $tree_nodes);
$smarty->assign('tree', $res);
// }}}

// Generate the object list {{{
if (isset($_REQUEST['categId'])) {
	$categId = $_REQUEST['categId'];

	if ($categId > 0) {
		$objects = $categlib->list_category_objects($categId, 0, -1, 'name_asc');
	} else {
		$objects = $categlib->list_orphan_objects(0, -1, 'name_asc');
	}

	$smarty->assign('objects', $objects);
}
// }}}

$smarty->assign('mid', 'tiki-edit_categories.tpl');
$smarty->display('tiki.tpl');
