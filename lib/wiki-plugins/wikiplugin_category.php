<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_category_info() {
	return array(
		'name' => tra('Category'),
		'documentation' => 'PluginCategory',
		'description' => tra('List categories and objects assigned to them'),
		'prefs' => array( 'feature_categories', 'wikiplugin_category' ),
		'icon' => 'pics/icons/sitemap_color.png',
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Category IDs'),
				'description' => tra('List of category IDs separated by + signs. ex: 1+2+3. Default will use category of the current page.'),
				'filter' => 'digits',
				'separator' => '+',
				'default' => '',
			),
			'types' => array(
				'required' => false,
				'name' => tra('Types'),
				'description' => tra('List of object types to include in the list separated by plus signs. ex: article+blog+faq+fgal<br />+forum+igal+newsletter<br />+event+poll+quiz+survey<br />+tracker+wiki+img'),
				'filter' => 'alpha',
				'default' => '*',
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => tra('Sort ascending or descending based on various attributes (sorted ascending by name by default)'),
				'filter' => 'text',
				'default' => '',
				'options' => array (
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Created Ascending'), 'value' => 'created_asc'),
					array('text' => tra('Created Descending'), 'value' => 'created_desc'),
					array('text' => tra('Hits Ascending'), 'value' => 'hits_asc'),
					array('text' => tra('Hits Descending'), 'value' => 'hits_desc'),
					array('text' => tra('Item ID Ascending'), 'value' => 'itemId_asc'),
					array('text' => tra('Item ID Descending'), 'value' => 'itemId_desc'),
					array('text' => tra('Name Ascending'), 'value' => 'name_asc'),
					array('text' => tra('Name Descending'), 'value' => 'name_desc'),
					array('text' => tra('Type Ascending'), 'value' => 'type_asc'),
					array('text' => tra('Type Descending'), 'value' => 'type_desc'),
					array('text' => tra('Random'), 'value' => 'random'),
				),
			),
			'split' => array(
				'required' => false,
				'name' => tra('Split'),
				'description' => tra('Whether multiple categories will be listed on different lines (default is to split them)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'and' => array(
				'required' => false,
				'name' => tra('And'),
				'description' => tra('If set to y (Yes), only objects in all of the categories will be shown (default is to show objects in any of the categories)'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'sub' => array(
				'required' => false,
				'name' => tra('With Sub-categories'),
				'description' => tra('Also list objects in sub-categories of the categories given (default is to list sub-category objects)'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'showdescription' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => tra('Show descriptions (not shown by default)'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'showname' => array(
				'required' => false,
				'name' => tra('Show Name'),
				'description' => tra('Show object names (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'showtype' => array(
				'required' => false,
				'name' => tra('Show type'),
				'description' => tra('Show type (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'one' => array(
				'required' => false,
				'name' => tra('One Per Line'),
				'description' => tra('Show one object per line (multiple per line shown by default)'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),		
			'showlinks' => array(
				'required' => false,
				'name' => tra('Show Child Links'),
				'description' => tra('Show children category links (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),		
			'categoryshowlink' => array(
				'required' => false,
				'name' => tra('Show Top Link'),
				'description' => tra('Show top category link (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),		
			'maxRecords' => array(
				'required' => false,
				'name' => tra('Max Records'),
				'description' => tra('Maximum number of objects to list (default is 50)'),
				'default' => '50',
				'filter' => 'digits',
			),		
			'showTitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show title text above category object lists (shown by default)'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'lang' => array(
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('List only objects in this language.').' '.tra('Only apply if type=wiki.'),
				'filter' => 'lang',
				'default' => '',
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
	if (!empty($lang)) {
		$filter['language'] = $lang;
	} elseif (isset($params['lang'])) {
		$filter['language'] = $prefs['language'];
	} else {
		$filter = null;
	}
	if (isset($and) and substr(strtolower($and),0,1) == 'y') {
		$and = true;
	} else {
		$and = false;
	}
	if (isset($sort)) {
		$list = explode(',', $sort);
		foreach ($list as $l) {
			if (!in_array($l, array('name_asc', 'name_desc', 'hits_asc', 'hits_desc', 'type_asc', 'type_desc', 'created_asc', 'created_desc', 'itemId_asc', 'itemId_desc', 'random'))) {
				return tra('Incorrect parameter:').' sort';
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
		if (isset($_REQUEST['page'])) {
			$objId = urldecode($_REQUEST['page']);
			$id = $categlib->get_object_categories('wiki page', $objId);
		} else {
			$id = array();
		}
	}
	$smarty->assign('params', $params);

	return "~np~". $categlib->get_categoryobjects($id,$types,$sort,$split,$sub,$and, $maxRecords, $filter)."~/np~";
}
