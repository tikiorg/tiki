<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_tabs_info()
{
	return array(
		'name' => tra('Tabs'),
		'documentation' => 'PluginTabs',
		'description' => tra('Arrange content in tabs'),
		'prefs' => array( 'wikiplugin_tabs' ),
		'body' => tra('Tabs content, separated by "/////"'),
		'iconname' => 'th-large',
		'introduced' => 4,
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Tabset Name'),
				'description' => tr('Unique tabset name (if you want the last state to be remembered). Example:')
					. '<code>user_profile_tabs</code>',
				'since' => '4.0',
				'filter' => 'text',
				'default' => '',
			),
			'tabs' => array(
				'required' => true,
				'name' => tra('Tab Titles'),
				'description' => tra('Pipe-separated list of tab titles. Example:') . '<code>tab 1|tab 2|tab 3</code>',
				'since' => '4.0',
				'filter' => 'text',
				'default' => '',
			),
			'toggle' => array(
				'required' => false,
				'name' => tra('Toggle Tabs'),
				'description' => tra('Allow toggling between tabs and no-tabs view'),
				'since' => '8.0',
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array (
					array('text' => '', 'value' => ''),
					array('value' => 'y' , 'text' => tra('Yes')),
					array('value' => 'n', 'text' => tra('No')),
				),
			),
			'inside_pretty' => array(
				'required' => false,
				'name' => tra('Inside Pretty Tracker'),
				'description' => tra('Parse pretty tracker variables within tabs'),
				'since' => '8.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array (
					array('text' => '', 'value' => ''),
					array('value' => 'n', 'text' => tra('No')),
					array('value' => 'y' , 'text' => tra('Yes')),
				),
			),		
		),
	);
}

function wikiplugin_tabs($data, $params)
{
	$tikilib = TikiLib::lib('tiki');
	if (!empty($params['name'])) {
		$tabsetname = $params['name'];
	} else {
		$tabsetname = '';
	}
	
	if (!empty($params['toggle'])) {
		$toggle = $params['toggle'];
	} else {
		$toggle = 'y';
	}

	if (!empty($params['inside_pretty']) && $params['inside_pretty'] == 'y') {
		$inside_pretty = true;
	} else {
		$inside_pretty = false;
	}
	
	$tabs = array();
	if (!empty($params['tabs'])) {
		$tabs = explode('|', $params['tabs']);
	} else {
		return "''".tra("No tab title specified. At least one must be specified in order for the tabs to appear.")."''";
	}
	if (!empty($data)) {
		$data = $tikilib->parse_data($data, array('suppress_icons' => true, 'inside_pretty' => $inside_pretty));
		$tabData = explode('/////', $data);
	}
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('tabsetname', $tabsetname);
	$smarty->assign_by_ref('tabs', $tabs);
	$smarty->assign('toggle', $toggle);
	$smarty->assign_by_ref('tabcontent', $tabData);

	$content = $smarty->fetch('wiki-plugins/wikiplugin_tabs.tpl');

	return $content;
}
