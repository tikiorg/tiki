<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to display last modification information.
// rlpowell 31 Dec 2006

function wikiplugin_lastmod_help() {
        return tra("The last_mod plugin replaces itself with last modification time of the named wiki page, or the current page if no name given").":<br />~np~{LASTMOD(page=>pagename)/}~/np~";
}

function wikiplugin_lastmod_info() {
	return array(
		'name' => tra('Last Modification'),
		'documentation' => 'PluginLastMod',
		'description' => tra('Show the last modification date for a page'),
		'prefs' => array('feature_wiki', 'wikiplugin_lastmod'),
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
		if( strstr( $_REQUEST["SCRIPT_NAME"], "tiki-index.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], "tiki-editpage.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], 'tiki-pagehistory.php') ) {
			$page = $_REQUEST["page"];
		}

	}

	$lastmod = $tikilib->date_format( "%a, %e %b %Y %H:%M:%S %Z", $tikilib->page_exists_modtime($page) );

	return $lastmod;

}
