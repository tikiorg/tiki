<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_agentinfo.php,v 1.5.2.1 2007-12-07 12:55:20 pkdille Exp $

// Wiki plugin to display a browser client information
// damian aka damosoft May 2004

function wikiplugin_agentinfo_help() {
        return tra("Displays browser client info").":<br />~np~{AGENTINFO(info=>IP or SVRSW or BROWSER)/}~/np~";
}

function wikiplugin_agentinfo_info() {
	return array(
		'name' => tra( 'User-Agent Info' ),
		'documentation' => 'PluginAgentinfo',
		'description' => tra( 'Displays various information about the client.' ),
		'prefs' => array('wikiplugin_agentinfo'),
		'params' => array(),
	);
}

function wikiplugin_agentinfo($data, $params) {
	
	extract ($params,EXTR_SKIP);

	$asetup = '';

	if (!isset($info)) {
		$info = 'IP';
	}

	if ($info == 'IP') {
		$asetup = $tikilib->get_ip_address();
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
