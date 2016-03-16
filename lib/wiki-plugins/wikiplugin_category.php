<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_category_info()
{
	return array(
		'name' => tra('Category'),
		'documentation' => 'PluginCategory',
		'description' => tra('List categories and objects assigned to them'),
		'prefs' => array( 'feature_categories', 'wikiplugin_category' ),
		'iconname' => 'structure',
		'introduced' => 1,
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Category IDs'),
				'description' => tr('List of category IDs separated by + signs. ex: %0. Default will use category
					of the current page.', '<code>1+2+3</code>'),
				'since' => '1',
				'filter' => 'digits',
				'separator' => '+',
				'default' => '',
				'profile_reference' => 'category',
			),
			'types' => array(
				'required' => false,
				'name' => tra('Types'),
				'description' => tra('List of object types to include in the list separated by plus signs. Example: ')
					. '<code>article+blog+blog post+fgal</code>',
				'since' => '1',
				'accepted' => 'article, blog, blog post, fgal, forum, igal, newsletter, event, poll, quiz, survey,
					tracker, wiki, img',
				'filter' => 'text',
				'default' => '*',
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tra('Sort ascending or descending based on various attributes (sorted ascending by
					name by default)'),
				'since' => '1',
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
				'since' => '1',
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
				'description' => tr('If set to %0 (Yes), only objects in all of the categories will be shown (default
					is to show objects in any of the categories)', '<code>y</code>'),
				'since' => '1',
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
				'name' => tra('Sub-categories'),
				'description' => tra('Also list objects in sub-categories of the categories given (default is to list
					sub-category objects)'),
				'since' => '4.1',
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
				'name' => tra('Description'),
				'description' => tra('Show descriptions (not shown by default)'),
				'since' => '4.1',
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
				'name' => tra('Name'),
				'description' => tra('Show object names (shown by default)'),
				'since' => '4.1',
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
				'name' => tra('Type'),
				'description' => tra('Show type (shown by default)'),
				'since' => '4.1',
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
				'since' => '5.0',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),		
			'showlinks' => array(
				'required' => false,
				'name' => tra('Child Links'),
				'description' => tra('Show children category links (shown by default)'),
				'since' => '5.0',
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
				'name' => tra('Top Link'),
				'description' => tra('Show top category link (shown by default)'),
				'since' => '5.0',
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
				'name' => tra('Maximum Records'),
				'description' => tr('Maximum number of objects to list (default is %0)', '<code>50</code>'),
				'since' => '6.1',
				'default' => '50',
				'filter' => 'digits',
			),
			'showTitle' => array(
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Show title text above category object lists (shown by default)'),
				'since' => '6.1',
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
				'description' => tra('List only objects in this language.') . ' ' . tr('Only apply if %0.',
					'<code>type="wiki"</code>'),
				'since' => '8.0',
				'filter' => 'lang',
				'default' => '',
			),
		),
	);
}

function wikiplugin_category($data, $params)
{
	global $prefs;

	if ($prefs['feature_categories'] != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}
	
	$categlib = TikiLib::lib('categ');

	$default = array('maxRecords' => 50);
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	// TODO: use categ name instead of id (alternative)
	if (isset($split) and substr(strtolower($split), 0, 1) == 'n') {
		$split = false;
	} else {
		$split = true;
	}
	if (isset($sub) and substr(strtolower($sub), 0, 1) == 'n') {
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
	if (isset($and) and substr(strtolower($and), 0, 1) == 'y') {
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

	if ($id == 'current') {
		if (isset($_REQUEST['page'])) {
			$objId = urldecode($_REQUEST['page']);
			$id = $categlib->get_object_categories('wiki page', $objId);
		} else {
			$id = array();
		}
	}

	// We pass maxRecords because get_categoryobjects ignores it when $and is set so we need to do an additional check in the template
	$displayParameters = array_intersect_key($params, array_flip(array('showTitle', 'categoryshowlink', 'showtype', 'one', 'showlinks', 'showname', 'showdescription', 'maxRecords')));
	return "~np~". $categlib->get_categoryobjects($id, $types, $sort, $split, $sub, $and, $maxRecords, $filter, $displayParameters)."~/np~";
}
