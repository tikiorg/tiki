<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_div.php,v 1.1 2004-02-29 17:09:35 luciash Exp $
 *
 * DIV plugin. Creates a division block for the content.
 * 
 * Syntax:
 * 
 *  {DIV([bg=color],[width=>num[%]],[align=>left|right|center])}
 *   Content inside
 *  {DIV}
 * 
 */
function wikiplugin_div_help() {
	return tra("Insert a division block on wiki page").":<br />~np~{DIV(bg=>,width=>,align=>)}".tra("text")."{DIV}~/np~";
}

function wikiplugin_div($data, $params) {
	/* set default values for some args */
	$align = "left";

	extract ($params);
	$w    = (isset($width)) ? " width: $width;"  : "";
	$bg   = (isset($bg))    ? " background: $bg;" : "";
	$al   = (isset($align) && ($align == 'right' || $align == "center")) ? " text-align: $align;" : "";

	$begin  = "<div".(strlen($bg) > 0 ? " style='$bg$al$w'" : "").">";
	$end = "</div>";
	return $begin . $data . $end;
}
?>
