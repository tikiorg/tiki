<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Tiki-Wiki CATEGORY plugin.
 * 
 * Syntax:
 * 
 * {CATEGORY(
 *	id=>1+2+3,	 # defaults to current
 *	types=>article+blog+directory+faq+fgal+forum+igal+newsletter+event+poll+quiz+survey+tracker+wiki # list of types of objects, default * (all),
 *	sort=>[type|created|name|hits]_[asc|desc]	# default name_asc,
 *	sub=>y|n		# display items of subcategories # default is 'true';
 *	split=>y|n		# when displaying multiple categories, whether they should be split or not; defaults to yes
 *	one=>y|n		# when y displays one categoy per line
 * )}
 * {CATEGORY}
 */
function wikiplugin_category_help() {
	return tra("Insert list of items with the current/given category in the wiki page").":<br />~np~{CATEGORY(id=1+2+3, types=article+blog+faq+fgal+forum+igal+newsletter+event+poll+quiz+survey+tracker+wiki+img, sort=[type|created|name|hits|shuffle]_[asc|desc], sub=y|n, split=|n, and=y|n)}{CATEGORY}~/np~";
}

function wikiplugin_category_info() {
	return array(
		'name' => tra('Category'),
		'documentation' => 'PluginCategory',
		'description' => tra("Insert list of items with the current/given category in the wiki page"),
		'prefs' => array( 'feature_categories', 'wikiplugin_category' ),
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Category IDs'),
				'description' => tra('List of category IDs separated by + signs. ex: 1+2+3. Default will use category of the current page.'),
				'filter' => 'digits',
				'separator' => '+'
			),
			'types' => array(
				'required' => false,
				'name' => tra('Types'),
				'description' => tra('List of object types to include in the list separated by plus signs. ex: article+blog+faq+fgal<br />+forum+igal+newsletter<br />+event+poll+quiz+survey<br />+tracker+wiki+img'),
				'filter' => 'alpha'
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => tra('fieldName_asc|fieldName_desc, valid fields: type, created, name, hits, shuffle'),
				'filter' => 'text'
			),
			'split' => array(
				'required' => false,
				'name' => tra('Split'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'and' => array(
				'required' => false,
				'name' => tra('And'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'sub' => array(
				'required' => false,
				'name' => tra('With sub-categories'),
				'description' => 'y|n',
				'default' => 'n',
				'filter' => 'alpha'
			),
			'showdescription' => array(
				'required' => false,
				'name' => tra('Show description'),
				'description' => 'y|n',
				'default' => 'n',
			),
			'showname' => array(
				'required' => false,
				'name' => tra('Show object name'),
				'description' => 'y|n',
				'default' => 'y',
				'filter' => 'alpha',
			),
			'showtype' => array(
				'required' => false,
				'name' => tra('Show type'),
				'description' => 'y|n',
				'default' => 'y',
				'filter' => 'alpha',
			),
			'one' => array(
				'required' => false,
				'name' => tra('Show one object per line'),
				'description' => 'y|n',
				'default' => 'n',
				'filter' => 'alpha',
			),		
			'showlinks' => array(
				'required' => false,
				'name' => tra('Show children category links'),
				'description' => 'y|n',
				'default' => 'y',
				'filter' => 'alpha',
			),		
			'categoryshowlink' => array(
				'required' => false,
				'name' => tra('Show top category link'),
				'description' => 'y|n',
				'default' => 'y',
				'filter' => 'alpha',
			),		
			'maxRecords' => array(
				'required' => false,
				'name' => tra('Max Records'),
				'description' => tra('Maximum number of objects to list. Default 50'),
				'default' => '50',
				'filter' => 'digits',
			),		
			'showTitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show title text above category object lists'),
				'default' => 'y',
				'filter' => 'alpha',
			),		
		),
	);
}

function wikiplugin_category($data, $params) {
	global $smarty, $prefs, $categlib;

	if (!is_object($categlib)) {
		require_once ("lib/categories/categlib.php");
	}

	if ($prefs['feature_categories'] != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}

	$default = array('one' => 'n', 'showlinks' => 'y', 'categoryshowlink'=>'y', 'maxRecords' => 50, 'showTitle' => 'y');
	$params = array_merge($default, $params);
	extract ($params,EXTR_SKIP);

	// TODO: use categ name instead of id (alternative)
	if (isset($split) and substr(strtolower($split),0,1) == 'n') {
		$split = false;
	} else {
		$split = true;
	}
	if (isset($sub) and substr(strtolower($sub),0,1) == 'n') {
		$sub = false;
	} else {
		$sub = true;
	}
	if (isset($and) and substr(strtolower($and),0,1) == 'y') {
		$and = true;
	} else {
		$and = false;
	}
	if (isset($sort)) {
		$list = explode(',', $sort);
		foreach ($list as $l) {
			if (!in_array($l, array('name_asc', 'name_desc', 'hits_asc', 'hits_desc', 'type_asc', 'type_desc', 'created_asc', 'created_desc', 'itemId_asc', 'itemId_desc'))) {
				return tra('Incorrect param:').' sort';
			}
		}
	} else {
		$sort = '';
	}

	$types = (isset($types)) ? strtolower($types) : "*";
	
	$id = (!empty($id)) ? $id : 'current'; // use current category if none is given
	if (isset($one) && $one == 'y')
		$smarty->assign('one', $one);

	if ($id == 'current') {
		$objId = urldecode($_REQUEST['page']);
		$id = $categlib->get_object_categories('wiki page', $objId);
	}
	$smarty->assign('params', $params);

	return "~np~". $categlib->get_categoryobjects($id,$types,$sort,$split,$sub,$and, $maxRecords)."~/np~";
}
