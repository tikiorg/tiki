<?php
/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_box.php,v 1.18.2.1 2007-11-28 23:29:23 sylvieg Exp $
 *
 * Tiki-Wiki BOX plugin.
 * 
 * Syntax:
 * 
 *  {BOX([title=>Title],[bg=>color|#999fff],[width=>num[%]],[align=>left|right|center])}
 *   Content inside box
 *  {BOX}
 * 
 */
function wikiplugin_box_help() {
	return tra("Insert theme styled box on wiki page").":<br />~np~{BOX(title=>Title, bg=>color, width=>num[%], align=>left|right|center, float=>|left|right),class=class, id=id}".tra("text")."{BOX}~/np~";
}

function wikiplugin_box($data, $params) {
	global $tikilib;
	
	// Remove first <ENTER> if exists...
	// if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
    
	extract ($params,EXTR_SKIP);
	$bg   = (isset($bg))    ? " background:$bg;" : "";
	$id = (isset($id)) ? " id=\"$id\" ":'';
	$class = (isset($class))? ' '.$class: ' ';
	if (isset($float)) {// box without table 
		$w = (isset($width)) ? " width:$width"  : "";
		$f = ($float == "left" || $float == "right")? " float:$float;" : "";
		$c = (isset($clear))    ? " clear:both;" : "";
		$begin = "<div class='cbox$class' $id style='$bg;$f;$w;$c'>";
	} else { // box in a table
		$w = (isset($width)) ? " width=\"$width\""  : "";
		$al = (isset($align) && ($align == 'right' || $align == "center")) ? " align=\"$align\"" : "";
		$c = (isset($clear))    ? " style='clear:both;'" : "";
		$begin  = "<table$al$w$c><tr><td><div class='cbox$class'$id".(strlen($bg) > 0 ? " style='$bg'" : "").">";
	}
    
	if (isset($title)) {
		$begin .= "<div class='cbox-title'>$title</div>";
	}
	$begin.= "<div class='cbox-data'".(strlen($bg) > 0 ? " style=\"$bg\"" : "").">";
	$end = "</div></div>";
	if (!isset($float)) {
		$end .= "</td></tr></table>";
	}
	// Prepend any newline char with br
	//$data = preg_replace("/\\n/", "<br />", $data);
	// Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
	//if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	//$data = $tikilib->parse_data($data);
	return $begin . $data . $end;
}

?>
