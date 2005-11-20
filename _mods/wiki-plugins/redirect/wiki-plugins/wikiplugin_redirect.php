<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/redirect/wiki-plugins/wikiplugin_redirect.php,v 1.7 2005-11-20 09:54:17 ang23 Exp $

// Wiki plugin to redirect to another page.
// damian aka damosoft 30 March 2004

function wikiplugin_redirect_help() {
        return tra("Redirects you to another wiki page").":<br />~np~{REDIRECT(page=pagename [,url=http://foobar])/}~/np~";
}

function wikiplugin_redirect($data, $params) {

	extract ($params,EXTR_SKIP);
	$areturn = '';

	if (!isset($page)) {$areturn = "REDIRECT plugin: No page specified!";}
	if (!isset($url)) {$areturn += "REDIRECT plugin: No url specified!";}
	if ((isset($_REQUEST['redirectpage']))) {
		$areturn = "REDIRECT plugin: redirect loop detected!";
	} else {
		if (isset($page)) {
			header("Location: tiki-index.php?page=$page&redirectpage=".$_REQUEST['page']);
			exit;
		}
		if (isset($url)) {
			header("Location: $url");
			exit;
		}
	}
		
	return $areturn;
}

?>
