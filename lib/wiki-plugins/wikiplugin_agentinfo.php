<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_agentinfo.php,v 1.5 2005-05-18 11:01:59 mose Exp $

// Wiki plugin to display a browser client information
// damian aka damosoft May 2004

function wikiplugin_agentinfo_help() {
        return tra("Displays browser client info").":<br :>~np~{AGENTINFO(info=>IP or SVRSW or BROWSER)/}~/np~";
}

function wikiplugin_agentinfo($data, $params) {
	
	extract ($params,EXTR_SKIP);

	$asetup = '';

	if (!isset($info)) {
		$info = 'IP';
	}

	if ($info == 'IP') {
		$asetup = $_SERVER["REMOTE_ADDR"];
	}

	if ($info == 'SVRSW') {
		$asetup = $_SERVER["SERVER_SOFTWARE"];
	}
	
	if ($info == 'BROWSER') {
		$asetup = $_SERVER["HTTP_USER_AGENT"];
	}

	return $asetup;
}

?>
