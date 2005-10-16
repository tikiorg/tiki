<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_quote.php,v 1.7 2005-10-16 14:35:10 mose Exp $
 *
 * TikiWiki QUOTE plugin.
 * 
 * Syntax:
 * 
 *  {QUOTE([replyto=>name])}
 *   Content inside box
 *  {QUOTE}
 * 
 */
function wikiplugin_quote_help() {
	return tra("Quote text by surrounding the text with a box, like the [QUOTE] BBCode").":<br />~np~{QUOTE(replyto=>name)}".tra("text")."{QUOTE}~/np~";
}

function wikiplugin_quote($data, $params) {
	/* set default values for some args */
	$replyto = '';
	
	// Remove first <ENTER> if exists...
//	if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
	// trim space/returns from beginning and end
	$data = trim($data);
    
	extract ($params, EXTR_SKIP);
	if (!empty($replyto)) {
		$caption = $replyto . tra(' wrote:');
	} else {
		$caption = tra('Quote:');
	}
    
	$begin  = "<div class='quoteheader'>";
    $begin .= "$caption</div><div class='quotebody'>";
	$end = "</div>";
		// Prepend any newline char with br
		$data = preg_replace("/\\n/", "<br />", $data);
    // Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
//    if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	return $begin . $data . $end;
}

?>
