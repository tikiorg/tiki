<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/redirect/wiki-plugins/wikiplugin_redirect.php,v 1.1 2004-10-18 11:24:20 damosoft Exp $

// Wiki plugin to redirect to another page.
// damian aka damosoft 30 March 2004

function wikiplugin_redirect_help() {
        return tra("Redirects you to another wiki page").":<br />~np~{REDIRECT(page=pagename)/}~/np~";
}

function wikiplugin_redirect($data, $params) {
	
	extract ($params);

	if (!isset($page)) {

		$areturn = "REDIRECT plugin: No page specified!";
	
	} else {

		header("Location: tiki-index.php?page=$page");
	
	}

	return $areturn;
}

?>
