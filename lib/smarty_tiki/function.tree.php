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
 * That smarty function is mostly intended to be used in .tpl files
 * syntax: {tree}
 * 
 */
function smarty_function_tree($params, &$smarty) {
	global $prefs;

	if ( $prefs['javascript_enabled'] == 'n' ) {
		// If JavaScript is disabled, force the php version of the tree
		$params['type'] = 'phptree';
	} else {
		// no phplayers - use category-style ones (for now)
		require_once ('lib/tree/categ_browse_tree.php');
		$link = $params['data']['link'];
		$name = $params['data']['name'];
		$link_id = 'id';
		$link_var = 'galleryId';
		require_once $smarty->_get_plugin_filepath('function', 'icon');
		$icon = '&nbsp;' . smarty_function_icon(array('_id' => 'folder'), $smarty) . '&nbsp;';
		
		$tree_nodes = array(
			array(
				'id' => $params['data']['id'],
				'parent' => 0,
				'data' => '<a class="fgalname" href="' . $link . '">' . $icon . htmlspecialchars($name) .'</a>', 
			)
		);
		$root_id = $params['data']['id'];
		require_once $smarty->_get_plugin_filepath('block', 'self_link');
		foreach($params['data']['data'] as $d) {
			$link_params = array('_script' => $link, $link_var => $d[$link_id], '_class' => 'fgalname');
			if (!empty($_REQUEST['filegals_manager'])) {
				$link_params['filegals_manager'] = $_REQUEST['filegals_manager'];
			}
			$tree_nodes[] = array(
				'id' => $d['id'],
				'parent' => $d['parentId'],
				'data' => smarty_block_self_link($link_params, $icon . htmlspecialchars($d['name']), $smarty), 
			);
		}
		$tm = new CatBrowseTreeMaker('categ');
		$res = $tm->make_tree( $root_id, $tree_nodes);
		return $res;
	}

}
