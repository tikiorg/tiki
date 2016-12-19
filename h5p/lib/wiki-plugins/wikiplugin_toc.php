<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_toc_info()
{
	return array(
		'name' => tra('Table of Contents (Structure)'),
		'documentation' => 'PluginTOC',
		'description' => tra('Display a table of contents of pages in a structure'),
		'prefs' => array( 'wikiplugin_toc', 'feature_wiki_structure' ),
		'iconname' => 'list-numbered',
		'introduced' => 3,
		'lateParse' => true,
		'params' => array(
			'structId' => array(
				'name' => tra('Structure ID'),
				'description' => tra('By default, structure for the current page will be displayed. Alternate
					structure may be provided.'),
				'since' => '3.0',
				'required' => false,
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'structure',
			),
			'pagename' => array(
				'name' => tra('Page Name'),
				'description' => tra('By default, the table of contents for the current page will be displayed.
					Alternate page may be provided.'),
				'since' => '5.0',
				'required' => false,
				'filter' => 'pagename',
				'default' => '',
				'profile_reference' => 'wiki_page',
			),
			'order' => array(
				'name' => tra('Order'),
				'description' => tra('Order items in ascending or descending order (default is ascending).'),
				'since' => '3.0',
				'required' => false,
				'filter' => 'alpha',
				'default' => 'asc',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Ascending'), 'value' => 'asc'),
					array('text' => tra('Descending'), 'value' => 'desc')
				)
			),
			'sortalpha' => array(
				'name' => tra('First Level Items Sort Order'),
				'description' => tr('Display first level by structure order: %0 (the default) or by sorting the items in alphabetical order: %1', '<code>struct</code>','<code>alpha</code>'),
				'since' => '15.3.',
				'required' => false,
				'filter' => 'alpha',
				'default' => 'struct',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Structure Order'), 'value' => 'struct'),
					array('text' => tra('Alphabetic Order'), 'value' => 'alpha')
				)
			),
			'showdesc' => array(
				'name' => tra('Show Description'),
				'description' => tra('Show the page description instead of the page name'),
				'since' => '3.0',
				'required' => false,
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'shownum' => array(
				'name' => tra('Show Numbering'),
				'description' => tra('Display the section numbers or not'),
				'since' => '3.0',
				'required' => false,
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'type' => array(
				'name' => tra('Type'),
				'description' => tra('Style to apply'),
				'since' => '3.0',
				'required' => false,
				'filter' => 'alpha',
				'default' => 'plain',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Plain'), 'value' => 'plain'),
					array('text' => tra('Fancy'), 'value' => 'fancy'),
					array('text' => tra('Admin'), 'value' => 'admin'),
 				)
			),
			'maxdepth' => array(
				'name' => tra('Maximum Depth'),
				'description' => tr('Maximum number of levels to display. On very large structures, this should be
					limited. %0 means no limit (and is the default).', '<code>0</code>'),
				'since' => '3.0',
				'required' => false,
				'filter' => 'digits',
				'default' => 0,
			),
			'mindepth' => array(
				'name' => tra('Minimum Depth'),
				'description' => tr('Hide number of levels below this number to display. %0 means no limit (and is the default).', '<code>0</code>'),
				'since' => '15.3.',
				'required' => false,
				'filter' => 'digits',
				'default' => 0,
			),
		),
	);
}

function wikiplugin_toc( $data, $params )
{
	$defaults = array(
		'order' => 'asc',
		'showdesc' => false,
		'shownum' => false,
		'type' => 'plain',
		'structId' => '',
		'maxdepth' => 0,
		'mindepth' => 0,
		'sortalpha' => 'struct',
		'numberPrefix' => '',
		'pagename' => '',
	);

	$params = array_merge($defaults, $params);
	extract($params, EXTR_SKIP);

	global $page_ref_id;
	$structlib = TikiLib::lib('struct');

	global $prefs;
	if ($prefs['feature_jquery_ui'] === 'y' && $type === 'admin') {
		TikiLib::lib('header')
				->add_jsfile('lib/structures/tiki-edit_structure.js')
				->add_jsfile('vendor/jquery/plugins/nestedsortable/jquery.ui.nestedSortable.js');

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_button');
		$button = smarty_function_button(
			array(
				'_text'		=> tra('Save'),
				'_style'	=> 'display:none;',
				'_class'	=> 'save_structure',
				'_ajax'		=> 'n',
				'_auto_args'=> 'save_structure,page_ref_id',
			),
			$smarty
		);
	} else {
		$button = '';
	}

	if (empty($structId)) {
		$pageName_ref_id = null;
		if (!empty($pagename)) {
			$pageName_ref_id = $structlib->get_struct_ref_id($pagename);
		} else if (!empty($page_ref_id)) {
			$pageName_ref_id = $page_ref_id;
		}
		if (!empty($pageName_ref_id)) {	// we have a structure
			$page_info = $structlib->s_get_page_info($pageName_ref_id);
			$structure_info = $structlib->s_get_structure_info($pageName_ref_id);
			if (isset($page_info)) {
				$html = $structlib->get_toc($pageName_ref_id, $order, $showdesc, $shownum, $numberPrefix, $type, '', $maxdepth, $mindepth, $sortalpha, $structure_info['pageName']);
				return "~np~$button $html $button~/np~";
			}
		}
		return '';
	} else {
		$structure_info = $structlib->s_get_structure_info($structId);
		$html = $structlib->get_toc($structId, $order, $showdesc, $shownum, $numberPrefix, $type, '', $maxdepth, $mindepth, $sortalpha, $structure_info['pageName']);

		return "~np~$button $html $button~/np~";
	}
}
