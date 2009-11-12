<?php
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
		'description' => tra('Provides tabs built using the smarty tabset block.'),
		'prefs' => array( 'wikiplugin_tabs' ),
		'body' => tra('Tabs content separated by /////'),
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Tabset name'),
				'description' => tra('Unique tabset name (if you want it to remember its last state). Ex: user_profile_tabs'),
			),
			'tabs' => array(
				'required' => true,
				'name' => tra('Tab titles'),
				'description' => tra('Pipe separated list of tab titles. Ex: tab 1|tab 2|tab 3'),
			),
		),
	);
}

function wikiplugin_tabs($data, $params) {
	global $tikilib, $smarty;
	if (!empty($params['name'])) {
		$tabsetname = $params['name'];
	}
	
	$tabs = array();
	if (!empty($params['tabs'])) {
		$tabs = explode('|', $params['tabs']);
	} else {
		return "''".tra("No tab title specified. At least one has to be set to make the tabs appear.")."''";
	}
	if (!empty($data)) {
		$tabData = explode('/////', $data);
	}
	
	$smarty->assign( 'tabsetname', $tabsetname );
	$smarty->assign_by_ref( 'tabs', $tabs );
	$smarty->assign_by_ref( 'tabcontent', $tabData );

	$content = $smarty->fetch( 'wiki-plugins/wikiplugin_tabs.tpl' );

	return $content;
}
