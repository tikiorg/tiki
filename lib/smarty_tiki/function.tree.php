<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * JavaScript Tree
 * 
 * This Smarty function is mostly intended to be used in .tpl files
 * syntax: {tree}
 * 
 */
function smarty_function_tree($params, $smarty) {
	require_once ('lib/tree/BrowseTreeMaker.php');
	$link = $params['data']['link'];
	$root_id = $params['data']['id'];
	$nodes = $params['data']['data'];

	$smarty->loadPlugin('smarty_function_icon');
	$icon = '&nbsp;' . smarty_function_icon(array('_id' => 'folder'), $smarty) . '&nbsp;';
	
	$tree_nodes = array();
	$smarty->loadPlugin('smarty_block_self_link');
	foreach ($nodes as $node) {
		$link_params = array('_script' => $link, 'galleryId' => $node['id'], '_class' => 'fgalname');
		if (!empty($_REQUEST['filegals_manager'])) {
			$link_params['filegals_manager'] = $_REQUEST['filegals_manager'];
		}
		$tree_nodes[] = array(
			'id' => $node['id'],
			'parent' => $node['parentId'],
			'data' => smarty_block_self_link($link_params, $icon . htmlspecialchars($node['name']), $smarty), 
		);
	}
	$tm = new BrowseTreeMaker('Galleries');
	$res = $tm->make_tree( $root_id, $tree_nodes);
	return $res;
}
