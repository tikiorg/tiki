<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/plazes/wiki-plugins/wikiplugin_plazes.php,v 1.2 2005-09-08 17:10:27 damosoft Exp $

// Wiki plugin to display your location
// use with plazes : http://www.plazes.com

// Damian

function wikiplugin_plazes_help() {
        return tra("Displays your Plazes info").":<br />~np~{PLAZES(key=your_plazes_key,map=>1 or 0)/}~/np~";
}

function wikiplugin_plazes($data, $params) {
	
	extract ($params, EXTR_SKIP);

	if (!$key) {
		return "No Key";
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
