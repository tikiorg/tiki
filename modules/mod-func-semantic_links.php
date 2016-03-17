<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_semantic_links_info()
{
	return array(
		'name' => tra('Semantic Links'),
		'description' => tra('List the relationships known for the Wiki page displayed. For each relation type contained in the page, it lists all the pages it links to or gets linked from.'),
		'prefs' => array('feature_semantic'),
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_semantic_links($mod_reference, $module_params)
{
	global $page;
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('show_semantic_links_module', false);

	if (isset($page) && !empty($page)) {
		$semanticlib = TikiLib::lib('semantic');
	
		$msl_page = $page;
		$relations = $semanticlib->getRelationList($msl_page);

		if (count($relations)) {
			$smarty->assign('msl_page', $msl_page);
			$smarty->assign('show_semantic_links_module', true);
			$smarty->assign('msl_relations', $relations);
			$smarty->clear_assign('tpl_module_title');
		}
	}
}
