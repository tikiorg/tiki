<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins-dist/wikiplugin_box.php,v 1.3 2003-10-29 23:33:04 zaufi Exp $
 *
 * Tiki-Wiki BOX plugin.
 * 
 * Syntax:
 * 
 *  {BOX([title=>Title],[bg=color],[width=>num[%]],[align=>left|right|center])}
 *   Text inside box
 *  {BOX}
 * 
 */
function wikiplugin_box_help() {
	return tra("Insert theme styled box on wiki page").":<br />~np~{BOX(title=>,bg=>,width=>,align=>)}".tra("text")."{BOX}~/np~";
}

function wikiplugin_box($data, $params) {
	/* set default values for some args */
	$title = "Message box";
    
	extract ($params);
	$w    = (isset($width)) ? " width=\"$width\""  : "";
	$bg   = (isset($bg))    ? " background:$bg;" : "";
    $al   = (isset($align) && ($align == 'right' || $align == "center")) ? " align=\"$align\"" : "";
    
	$begin  = "<table$al$w><tr><td$w><div class=cbox".(strlen($bg) > 0 ? " style='$bg'" : "").">";
    $begin .= "<div class=cbox-title>$title</div><div class=cbox-data".(strlen($bg) > 0 ? " style=\"$bg\"" : "").">";
	$end = "</div></div></td></tr></table>";
    // Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
    if (substr($data, 1) != "\n") $data = "\n".$data;
	return $begin . $data . $end;
}

?>
