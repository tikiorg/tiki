<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// @author luciash
// \brief Wiki plugin to display a tabset
// Known Issues: current Tiki implementation of tabs limits to only one tabset per page will work properly
// 

// this script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

function wikiplugin_tabs_help() {
        return tra("Displays a set of tabs").":<br />~np~{TABS(name='foo' tabs='bar|another bar|last bar')}Foo 1/////Foo 2/////Foo 3~/np~";
}

function wikiplugin_tabs_info() {
	return array(
		'name' => tra('Tabs'),
		'documentation' => 'PluginTabs',
		'description' => tra('Arrange content in tabs'),
		'prefs' => array( 'wikiplugin_tabs' ),
		'body' => tra('Tabs content separated by /////'),
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Tabset Name'),
				'description' => tra('Unique tabset name (if you want it to remember its last state). Ex: user_profile_tabs'),
				'default' => '',
			),
			'tabs' => array(
				'required' => true,
				'name' => tra('Tab Titles'),
				'description' => tra('Pipe separated list of tab titles. Ex: tab 1|tab 2|tab 3'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_tabs($data, $params) {
	global $tikilib, $smarty;
	if (!empty($params['name'])) {
		$tabsetname = $params['name'];
	} else {
		$tabsetname = '';
	}
	
	$tabs = array();
	if (!empty($params['tabs'])) {
		$tabs = explode('|', $params['tabs']);
	} else {
		return "''".tra("No tab title specified. At least one has to be set to make the tabs appear.")."''";
	}
	if (!empty($data)) {
		$tabData = explode('/////', $data);
		foreach ($tabData as &$d) {
			if (strpos( $d, '</p>') === 0) {
				$d = substr( 4, $d);
			}
			$d = '~np~' . $tikilib->parse_data($d) . '~/np~';
		}
	}
	
	$smarty->assign( 'tabsetname', $tabsetname );
	$smarty->assign_by_ref( 'tabs', $tabs );
	$smarty->assign_by_ref( 'tabcontent', $tabData );

	$content = $smarty->fetch( 'wiki-plugins/wikiplugin_tabs.tpl' );

	return $content;
}
