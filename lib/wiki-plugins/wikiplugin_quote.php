<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_quote.php,v 1.4 2004-07-01 16:43:17 teedog Exp $
 *
 * TikiWiki QUOTE plugin.
 * 
 * Syntax:
 * 
 *  {QUOTE([title=>Title])}
 *   Content inside box
 *  {QUOTE}
 * 
 */
function wikiplugin_quote_help() {
	return tra("Quote text by surrounding the text with a box, like the [QUOTE] BBCode").":<br />~np~{QUOTE(title=>Title)}".tra("text")."{QUOTE}~/np~";
}

function wikiplugin_quote($data, $params) {
	/* set default values for some args */
	$title = tra("Quote:");
	
	// Remove first <ENTER> if exists...
//	if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
	// trim space/returns from beginning and end
	$data = trim($data);
    
	extract ($params);
    
	$begin  = "<div class='quoteheader'>";
    $begin .= "$title</div><div class='quotebody'>";
	$end = "</div>";
		// Prepend any newline char with br
		$data = preg_replace("/\\n/", "<br />", $data);
    // Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
//    if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	return $begin . $data . $end;
}

?>
