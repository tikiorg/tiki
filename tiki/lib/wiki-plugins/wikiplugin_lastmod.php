<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_lastmod.php,v 1.3 2007-02-04 20:09:46 mose Exp $

// Wiki plugin to display last modification information.
// rlpowell 31 Dec 2006

function wikiplugin_lastmod_help() {
        return tra("The last_mod plugin replaces itself with last modification time of the named wiki page, or the current page if no name given").":<br :>~np~{LASTMOD(page=>pagename)/}~/np~";
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

	$lastmod = date( "r", $tikilib->page_exists_modtime($page) );

	return $lastmod;

}

?>
