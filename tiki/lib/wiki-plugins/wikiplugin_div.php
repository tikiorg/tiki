<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_div.php,v 1.3 2005-01-22 22:55:56 mose Exp $
 *
 * DIV plugin. Creates a division block for the content. Forces the content 
 * to be aligned (left by deafault).
 * 
 * Syntax:
 * 
 *  {DIV([align=>left|right|center][, bg=color][, width=>num[%]])}
 *   some content
 *  {DIV}
 * 
 */
function wikiplugin_div_help() {
	return tra("Insert a division block on wiki page").":<br />~np~{DIV([align=>left|right|center][, bg=color][, width=>num[%]])}".tra("text")."{DIV}~/np~";
}

function wikiplugin_div($data, $params) {

	extract ($params,EXTR_SKIP);
	$w    = (isset($width)) ? " width: $width;"  : "";
	$bg   = (isset($bg))    ? " background: $bg;" : "";
	$al   = (isset($align) && ($align == 'right' || $align == "center")) ? " text-align: $align;" : " text-align: left;";

	$begin  = "<div style=\"$bg$al$w\">";
	$end = "</div>";
	return $begin . $data . $end;
}
?>
