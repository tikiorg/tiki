<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_objecthits.php,v 1.3 2005-10-03 17:21:46 sylvieg Exp $

// Wiki plugin to display the number of hits per object
// Franck Martin 2005

function wikiplugin_objecthits_help() {
        return tra("Displays object hit info by object and days").":<br :>~np~{OBJECTHITS(object=>,type=>,days=>)/}~/np~";
}

function wikiplugin_objecthits($data, $params) {
	global $tikilib;

	global $statslib;
	if (!is_object($statslib)) {
		global $dbTiki;
		include "lib/stats/statslib.php";
	}
 
	extract ($params,EXTR_SKIP);

	if (!isset($object)) {
	  global $page;
		$object = $page;
		$type= "wiki";
	}

	if (!isset($days)) {
		$days=0;
	}
	
	if (!isset($type)) {
		$type="wiki";
	}
	
  return $statslib->object_hits($object,$type,$days);
}

?>