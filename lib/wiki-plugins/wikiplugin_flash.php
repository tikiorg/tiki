<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_flash.php,v 1.2 2004-05-01 01:06:31 damosoft Exp $

// Wiki plugin to display a SWF file
// damian aka damosoft 30 March 2004

function wikiplugin_flash_help() {
        return tra("Displays a SWF on the wiki page").":<br :>~np~{FLASH(movie=url_to_flash,width=>xx,height=>xx,quality=>high)}{FLASH}~/np~";
}

function wikiplugin_flash($data, $params) {
	
	extract ($params);

	$asetup = "<OBJECT CLASSID=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" WIDTH=\"$width\" HEIGHT=\"$height\">";
	$asetup .= "<PARAM NAME=\"movie\" VALUE=\"$movie\">";
	$asetup .= "<PARAM NAME=\"quality\" VALUE=\$quality\">";
	$asetup .= "<embed src=\"$movie\" quality=\"$quality\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\"></embed></object>";

	return $asetup;
}

?>
