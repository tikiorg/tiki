<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_quote.php,v 1.3 2004-07-01 16:09:43 teedog Exp $
 *
 * TikiWiki QUOTE plugin.
 * 
 * Syntax:
 * 
 *  {QUOTE([title=>Title],[bg=>color|#999fff],[width=>num[%]],[align=>left|right|center])}
 *   Content inside box
 *  {QUOTE}
 * 
 */
function wikiplugin_quote_help() {
	return tra("Quote text by surrounding the text with a box, like the [QUOTE] BBCode").":<br />~np~{QUOTE(title=>Title, bg=>color, width=>num[%], align=>left|right|center)}".tra("text")."{QUOTE}~/np~";
}

function wikiplugin_quote($data, $params) {
	/* set default values for some args */
	$title = tra("Quote:");
	$width = '100%';
	
	// Remove first <ENTER> if exists...
	if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
    
	extract ($params);
	$w    = (isset($width)) ? " width=\"$width\""  : "";
	$bg   = (isset($bg))    ? " background:$bg;" : "";
    $al   = (isset($align) && ($align == 'right' || $align == 'center')) ? " align=\"$align\"" : "";
    
	$begin  = "<table$al$w><tr><td$w><div class='quote'".(strlen($bg) > 0 ? " style='$bg'" : "").">";
    $begin .= "$title";
	$end = "</div></td></tr></table>";
		// Prepend any newline char with br
		$data = preg_replace("/\\n/", "<br />", $data);
    // Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
    if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	return $begin . $data . $end;
}

?>
