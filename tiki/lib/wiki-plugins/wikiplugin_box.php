<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_box.php,v 1.6 2003-10-19 09:57:59 redflo Exp $
 *
 * Tiki-Wiki BOX plugin.
 * 
 * Syntax:
 * 
 *  {BOX([title=>Title],[bg=color],[width=>num[%]])}
 *   Text inside box
 *  {BOX}
 * 
 */
function wikiplugin_box_help() {
	return tra("Insert theme styled box on wiki page").":<br />~np~{BOX(title=>,bg=>,width=>)}".tra("text")."{BOX}~/np~";
}

function wikiplugin_box($data, $params) {
	/* set default values for some args */
	$title = "Message box";

	extract ($params);
	$w = (isset($width)) ? " width=$width" : "";
	$back = (isset($bg)) ? " style='background:$bg'" : "";
	$begin = "<table$w><tr><td><div class=cbox$back><div class=cbox-title>$title</div><div class=cbox-data$back>";
	$end = "</div></div></td></tr></table>";
	$data=preg_replace("/([\r\n])/","<br />",$data);

	return $begin . $data . $end;
}

?>
