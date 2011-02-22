<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_menupage_info() {
	return array(
		'name' => tra('Menu Page'),
		'description' => tra('Displays a Wiki page.'),
		'prefs' => array( 'feature_wiki' ),
		'params' => array(
			'pagemenu' => array(
				'name' => tra('Page'),
				'description' => tra('Page to display in the menu. Example value: HomePage.'),
				'required' => true
			)
		)
	);
}

function module_menupage( $mod_reference, $module_params ) {
	global $smarty;
	$pagemenu = $module_params['pagemenu'];
	
	if (!empty($pagemenu)) {
		global $wikilib; include_once('lib/wiki/wikilib.php');
		$content = $wikilib->get_parse($pagemenu, $dummy, true);
		$smarty->assign('tpl_module_title', $pagemenu);
		$smarty->assign_by_ref('contentmenu',$content);
		$smarty->assign('pagemenu', $pagemenu);
	}
}
