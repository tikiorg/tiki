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

function smarty_block_filter($params, $content, &$smarty, $repeat) {
	global $prefs, $tikilib;
	
	if (! isset($params['action'])) {
		$params['action'] = '';
	}

	$types = array();

	if ($prefs['feature_wiki'] == 'y') {
		$types[] = 'wiki page';
	}

	if ($prefs['feature_blogs'] == 'y') {
		$types[] = 'blog post';
	}
	
	if ($prefs['feature_articles'] == 'y') {
		$types[] = 'article';
	}

	if ($prefs['feature_forums'] == 'y') {
		$types[] = 'forum post';
	}

	if ($prefs['feature_trackers'] == 'y') {
		$types[] = 'trackeritem';
	}

	if ($prefs['feature_sheet'] == 'y') {
		$types[] = 'sheet';
	}

	$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();

	// General
	$smarty->assign('filter_action', $params['action']);

	$smarty->assign('filter_content', isset($filter['content']) ? $filter['content'] : '');
	$smarty->assign('filter_type', isset($filter['type']) ? $filter['type'] : '');
	$smarty->assign('filter_types', array_combine($types, array_map('tra', $types)));

	// Categories
	if ($prefs['feature_categories'] == 'y') {
		$smarty->assign('filter_deep', isset($filter['deep']));
		$smarty->assign('filter_categories', isset($filter['categories']) ? $filter['categories'] : '');
		$smarty->assign('filter_categmap', json_encode(TikiDb::get()->fetchMap('SELECT categId, name FROM tiki_categories')));

		// Generate the category tree {{{
		global $categlib; require_once 'lib/categories/categlib.php';
		require_once 'lib/tree/categ_browse_tree.php';
		$ctall = $categlib->get_all_categories_respect_perms(null, 'view_category');

		$tree_nodes = array();
		foreach($ctall as $c) {
			$name = htmlentities($c['name'], ENT_QUOTES, 'UTF-8');

			$body = <<<BODY
<label>
	<input type="checkbox" value="{$c['categId']}"/>
	{$name}
</label>
BODY;

			$tree_nodes[] = array(
				'id' => $c['categId'],
				'parent' => $c['parentId'],
				'data' => $body,
			);
		}

		$tm = new CatBrowseTreeMaker('categ');
		$res = $tm->make_tree(0, $tree_nodes);
		$smarty->assign('filter_category_picker', $res);
		// }}}
	}

	if ($prefs['feature_freetags'] == 'y') {
		global $freetaglib; require_once 'lib/freetag/freetaglib.php';
		
		$smarty->assign('filter_tags', isset($filter['tags']) ? $filter['tags'] : '');
		$smarty->assign('filter_tagmap', json_encode(TikiDb::get()->fetchMap('SELECT tagId, tag FROM tiki_freetags')));
		$smarty->assign('filter_tags_picker', (string) $freetaglib->get_cloud() );
	}

	// Language
	if ($prefs['feature_multilingual'] == 'y') {
		$languages = $tikilib->list_languages();
		$smarty->assign('filter_languages', $languages);
		$smarty->assign('filter_language_unspecified', isset($filter['language_unspecified']));
		$smarty->assign('filter_language', isset($filter['language']) ? $filter['language'] : '');
	}

	return $smarty->fetch('filter.tpl');
}
