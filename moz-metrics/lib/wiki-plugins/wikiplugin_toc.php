<?php

function wikiplugin_toc_info()
{
	return array(
		'name' => tra('Table of Contents (Structure)'),
		'documentation' => 'PluginTOC',		
		'description' => tra('Displays the table of contents for the current structure\'s subtree as part of the page content.'),
		'prefs' => array( 'wikiplugin_toc', 'feature_wiki_structure' ),
		'params' => array(
			'maxdepth' => array(
				'name' => tra('Maximum Depth'),
				'description' => tra('Maximum number of levels to display. On very large structures, this should be limited.'),
				'required' => false,
			),
			'structId' => array(
				'name' => tra('Structure ID'),
				'description' => tra('By default, structure for the current page will be displayed. Alternate structure may be provided.'),
				'required' => false,
			),
			'order' => array(
				'name' => tra('Order'),
				'description' => tra('asc|desc'),
				'required' => false,
			),
			'showdesc' => array(
				'name' => tra( 'Show Description' ),
				'description' => tra('0|1, show the page description instead of the page name'),
				'required' => false,
			),
			'shownum' => array(
				'name' => tra('Show Numbering'),
				'description' => tra('0|1, display the section numbers or not'),
				'required' => false,
			),
			'type' => array(
				'name' => tra('Type'),
				'description' => tra('plain|fancy'),
				'required' => false,
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
	);

	$params = array_merge( $defaults, $params );
	extract( $params, EXTR_SKIP );

	global $structlib, $page_ref_id;
	include_once ("lib/structures/structlib.php");
	if (empty($structId)) {
		if (!empty($page_ref_id)) {	//And we are currently viewing a structure
			$page_info = $structlib->s_get_page_info($page_ref_id);
			$structure_info = $structlib->s_get_structure_info($page_ref_id);
			if (isset($page_info)) {
				$html = $structlib->get_toc($page_ref_id, $order, $showdesc, $shownum, $numberPrefix, $type, '', $maxdepth, $structure_info['pageName']);
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
