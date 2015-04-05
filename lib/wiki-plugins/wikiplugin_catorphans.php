<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_catorphans_info()
{
	return array(
		'name' => tra('Category Orphans'),
		'documentation' => 'PluginCatOrphans',
		'description' => tra('List objects that are not categorized'),
		'prefs' => array( 'feature_categories', 'wikiplugin_catorphans' ),
		'icon' => 'img/icons/sitemap_color.png',
		'params' => array(
			'objects' => array(
				'required' => false,
				'name' => tra('Object'),
				'description' => tra('Currently, only works with wiki pages, file galleries and trackers (set to wiki (Wiki Pages) by default)'),
				'default' => 'wiki',
				'options' => array(
					array('text' => tra('Wiki Pages'), 'value' => 'wiki'),
					array('text' => tra('File Galleries'), 'value' => 'file gallery'),
					array('text' => tra('Trackers'), 'value' => 'tracker'),
				) 
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => tra('Maximum number of items. Use -1 for unlimited. Default is the site admin setting for maximum records.'),
				'default' => '$prefs[\'maxRecords\']'
			),
			'offset' => array(
				'required' => false,
				'name' => tra('Result Offset'),
				'description' => tra('Result number at which the listing should start (default is no offset)'),
				'default' => 0
			),
		),
	);
}

function wikiplugin_catorphans($data, $params)
{
	global $prefs;
	$access = TikiLib::lib('access');
	$access->check_feature('feature_categories');
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$categlib = TikiLib::lib('categ');

	$default = array('offset'=>0, 'max'=>$prefs['maxRecords'], 'objects'=>'wiki');
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);
	// check required objects parameter
	if ($params['objects'] !== 'wiki' && $params['objects'] !== 'file gallery' && $params['objects'] !== 'tracker') {
		return ("<span class='error'>Wrong objects parameter - only wiki, file gallery and tracker allowed at present</span>");
	}

	if (!empty($_REQUEST['offset'])) {
		$offset = $_REQUEST['offset'];
	}

	// currently only supports display of wiki pages, file galleries and trackers
	$smarty->assign('objecttype', $objects);
	if ($objects == 'wiki') {
		$listobjects = $tikilib->list_pages($offset, $max, 'pageName_asc', '', '', true, true, false, false, array('noCateg' => true));
	} elseif ($objects == 'file gallery') {
		$listobjects = $categlib->get_catorphan_object_type($offset, $max, 'file gallery','file_galleries','galleryId');
	} elseif ($objects == 'tracker') {
		$listobjects = $categlib->get_catorphan_object_type($offset, $max, 'tracker','trackers','trackerId');
	}
	$smarty->assign_by_ref('orphans', $listobjects['data']);
	$smarty->assign('pagination', array('cant'=>$listobjects['cant'], 'step'=>$max, 'offset'=>$offset));
	$out = '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_catorphans.tpl') . '~/np~';	
	$smarty->assign('pagination', null);
	return $out;

}
