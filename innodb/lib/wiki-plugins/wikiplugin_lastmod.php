<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_lastmod_info() {
	return array(
		'name' => tra('Last Modification'),
		'documentation' => 'PluginLastMod',
		'description' => tra('Show the last modification date for a page'),
		'prefs' => array('feature_wiki', 'wikiplugin_lastmod'),
		'icon' => 'pics/icons/date_edit.png',
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Page name to display information of. Default value is current page.'),
			),
		),
	);
}

function wikiplugin_lastmod($data, $params) {

	global $tikilib;
	
	extract ($params,EXTR_SKIP);

	if (!isset($page)) {
		# See if we're being called from a wiki page; stolen from wikiplugin_attach
		if (isset($_REQUEST['SCRIPT_NAME']) && isset($_REQUEST['page'])) {
			if( strstr( $_REQUEST["SCRIPT_NAME"], "tiki-index.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], "tiki-editpage.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], 'tiki-pagehistory.php') ) {
				$page = $_REQUEST["page"];
			}
		} else {
			return;
		}

	}

	$lastmod = $tikilib->date_format( "%a, %e %b %Y %H:%M:%S %Z", $tikilib->page_exists_modtime($page) );

	return $lastmod;

}
