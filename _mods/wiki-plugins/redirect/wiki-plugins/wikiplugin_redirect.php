<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/redirect/wiki-plugins/wikiplugin_redirect.php,v 1.3 2005-02-09 07:31:23 mhausi Exp $

// Wiki plugin to redirect to another page.
// damian aka damosoft 30 March 2004

function wikiplugin_redirect_help() {
        return tra("Redirects you to another wiki page").":<br />~np~{REDIRECT(page=pagename)/}~/np~";
}

function wikiplugin_redirect($data, $params) {

	extract ($params,EXTR_SKIP);
	$areturn = '';

	if (!isset($page)) {

		$areturn = "REDIRECT plugin: No page specified!";
	
	} else {
		if ((isset($_REQUEST['redirectpage'])) && ($page == $_REQUEST['redirectpage'])){
			$areturn = "REDIRECT plugin: redirect loop dedected!";
		}else{
			header("Location: tiki-index.php?page=$page&redirectpage=".$_REQUEST['page']);
		}
	}

	return $areturn;
}

?>
