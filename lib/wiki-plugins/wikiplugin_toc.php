<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_toc_info()
{
	return array(
		'name' => tra('Table of Contents (Structure)'),
		'documentation' => 'PluginTOC',
		'description' => tra('Display a table of contents of pages or sub-pages'),
		'prefs' => array( 'wikiplugin_toc', 'feature_wiki_structure' ),
		'icon' => 'pics/icons/text_list_numbers.png',
		'params' => array(
			'maxdepth' => array(
				'name' => tra('Maximum Depth'),
				'description' => tra('Maximum number of levels to display. On very large structures, this should be limited. Zero means no limit (and is the default).'),
				'required' => false,
				'filter' => 'digits',
				'default' => 0,
			),
			'structId' => array(
				'name' => tra('Structure ID'),
				'description' => tra('By default, structure for the current page will be displayed. Alternate structure may be provided.'),
				'required' => false,
				'filter' => 'digits',
				'default' => '',
			),
			'order' => array(
				'name' => tra('Order'),
				'description' => tra('Order items in ascending or descending order (deafult is ascending).'),
				'required' => false,
				'filter' => 'alpha',
				'default' => 'asc',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Ascending'), 'value' => 'asc'), 
					array('text' => tra('Descending'), 'value' => 'desc')
				)
			),
			'showdesc' => array(
				'name' => tra( 'Show Description' ),
				'description' => tra('Show the page description instead of the page name'),
				'required' => false,
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
				'required' => false,
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
				'required' => false,
				'filter' => 'alpha',
				'default' => 'plain',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Plain'), 'value' => 'plain'), 
					array('text' => tra('Fancy'), 'value' => 'fancy')
 				)
			),
			'pagename' => array(
				'name' => tra('Page Name'),
				'description' => tra('By default, the table of contents for the current page will be displayed. Alternate page may be provided.'),
				'required' => false,
				'default' => '',
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
		'numberPrefix' => '',
		'pagename' => '',
	);

	$params = array_merge( $defaults, $params );
	extract( $params, EXTR_SKIP );

	global $structlib, $page_ref_id;
	include_once ("lib/structures/structlib.php");
	if (empty($structId)) {
		if (!empty($page_ref_id)) {	//And we are currently viewing a structure
			$pageName_ref_id = null;
			if(!empty($pagename)) {
				$pageName_ref_id = $structlib->get_struct_ref_id($pagename);
			} else {
				$pageName_ref_id = $page_ref_id;
			}
			$page_info = $structlib->s_get_page_info($pageName_ref_id);
			$structure_info = $structlib->s_get_structure_info($pageName_ref_id);
			if (isset($page_info)) {
				$html = $structlib->get_toc($pageName_ref_id, $order, $showdesc, $shownum, $numberPrefix, $type, '', $maxdepth, $structure_info['pageName']);
				return "~np~$html~/np~";
			}
		}
			//Dont display the {toc} string for non structure pages
		return '';
	} else {
		$structure_info = $structlib->s_get_structure_info($structId);
		$html = $structlib->get_toc($structId, $order, $showdesc, $shownum, $numberPrefix, $type,'',$maxdepth, $structure_info['pageName']);

		return "~np~$html~/np~";
	}
}
