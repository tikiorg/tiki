<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(array(
	'staticKeyFiltersForArrays' => array(
		'filter' => 'text',
		'sort_mode' => 'text',
	),
	'catchAllUnset' => null,
));

require_once 'tiki-setup.php';
$categlib = TikiLib::lib('categ');
require_once 'lib/tree/BrowseTreeMaker.php';

$access->check_feature('feature_categories');

// Generate the category tree {{{
$ctall = $categlib->getCategories();

$tree_nodes = array();
foreach ($ctall as $c) {
	$url = htmlentities(
		'tiki-edit_categories.php?' . http_build_query(
			array(
				'filter~categories' => $c['categId'],
			)
		),
		ENT_QUOTES,
		'UTF-8'
	);
	$name = htmlentities($c['name'], ENT_QUOTES, 'UTF-8');
	$perms = Perms::get('category', $c['categId']);

	$add = $perms->add_object ? '<span class="control categ-add"></span>' : '';
	$remove = $perms->remove_object ? '<span class="control categ-remove"></span>' : '';

	$body = <<<BODY
$add
$remove
<span class="object-count">{$c['objects']}</span>
<a class="catname" href="{$url}" data-categ="{$c['categId']}">{$name}</a>
BODY;

	$tree_nodes[] = array(
		'id' => $c['categId'],
		'parent' => $c['parentId'],
		'data' => $body,
	);
}

$tree_nodes[] = array(
	'id' => 'orphan',
	'parent' => '0',
	'data' => '<span class="object-count">' . $orphans['cant'] . '</span><a class="catname" href="tiki-edit_categories.php?filter~categories=orphan"><em>' . tr('Orphans') . '</em></a>',
);

$tm = new BrowseTreeMaker('categ');
$res = $tm->make_tree(0, $tree_nodes);
$smarty->assign('tree', $res);
// }}}

$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();
$smarty->assign('filter', $filter);

if (count($filter)) {
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');
	$query = $unifiedsearchlib->buildQuery($filter);
	if (isset($_REQUEST['sort_mode']) && $order = Search_Query_Order::parse($_REQUEST['sort_mode'])) {
		$query->setOrder($order);
	}
	$result = $query->search($unifiedsearchlib->getIndex());
	$smarty->assign('result', $result);
}
// }}}

$smarty->assign('mid', 'tiki-edit_categories.tpl');
$smarty->display('tiki.tpl');
