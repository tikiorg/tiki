<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_flash.php,v 1.8.2.1 2007-11-29 00:25:57 xavidp Exp $

// Wiki plugin to display a SWF file
// damian aka damosoft 30 March 2004

function wikiplugin_flash_help() {
        return tra("Displays a SWF on the wiki page").":<br />~np~{FLASH(movie=\"url_to_flash\",width=>xx,height=>xx,quality=>high)}{FLASH}~/np~";
}

function wikiplugin_flash($data, $params) {
	
	extract ($params,EXTR_SKIP);

	if (empty($movie)) {
		return tra('Missing parameter movie to the plugin flash');
	}
	
	if (!isset($width)) {
	$width = "425";
	}	

	if (!isset($height)) {
	$height = "350";
	}	

	if (!isset($quality)) {
	$quality = "high";
	}	

	$asetup = "<OBJECT CLASSID=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" WIDTH=\"$width\" HEIGHT=\"$height\">";
	$asetup .= "<PARAM NAME=\"movie\" VALUE=\"$movie\">";
	$asetup .= "<PARAM NAME=\"quality\" VALUE=\"$quality\">";
	$asetup .= "<PARAM NAME=\"wmode\" VALUE=\"transparent\">";
	$asetup .= "<embed src=\"$movie\" quality=\"$quality\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\" wmode=\"transparent\"></embed></object>";

	return $asetup;
}

?>
