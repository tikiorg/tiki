<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/plazes/wiki-plugins/wikiplugin_plazes.php,v 1.1 2005-01-06 22:45:08 damosoft Exp $

// Wiki plugin to display your location
// use with plazes : http://www.plazes.com

// Damian

function wikiplugin_plazes_help() {
        return tra("Displays your Plazes info").":<br />~np~{PLAZES(key=your_plazes_key,map=>1 or 0)/}~/np~";
}

function wikiplugin_plazes($data, $params) {
	
	extract ($params);

	if (!$key) {
		return "Plugin called without a key.";
	}

	if ($map != 0 && $map != 1) {
		$map = 1;
	}

	$asetup = "<script type=\"text/javascript\">";
	$asetup .= "  plazeskey = \"$key\";";
	$asetup .= "  plazesmap = $map;";
	$asetup .= "  plazeswidth = 220;";
	$asetup .= "  plazesheight = ". ($map==1 ? "184" : "70") . ";";
	$asetup .= "</script>";
	$asetup .= "<script type=\"text/javascript\" src=\"http://beta.plazes.com/plugin/plazesplugin_2.js\"></script>";

	return $asetup;
}

?>
