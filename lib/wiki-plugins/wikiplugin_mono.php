<?php

// Displays the data inline using a monospace font
//
// Parameters: 
//   font -- a font name. If the name contains whitespace, you must enclose
//           the name in single quotes. Note, the fallback standard browser
//           font "monospace" is specified in case the viewing browser cannot
//           find the requested font.
//
// Examples:
//   {MONO()}text{MONO}
//      displayed using the browser-specified "monospace" font
//
//   {MONO(font=>'Courier New')}text{MONO}
//      displayed using Courier New font, if available; otherwise uses the
//      browser-specified "monospace" font
//
function wikiplugin_mono_help() {
	return tra("Displays the data using a monospace font").":<br />~np~{MONO(font=>}".tra("text")."{MONO}~/np~";
}

function wikiplugin_mono($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);

	$code = /* htmlentities( htmlspecialchars(*/ trim($data) /* ) )*/;
	$code = preg_replace("/\n/", "<br />", $code);

	if (!isset($font)) {
		$font = "monospace";
	} else {
		$font .= ", monospace";
	}

	$style = "style=\"font-family: " . $font . ";\"";
	$data = "<span " . $style . ">" . $code . "</span>";

	return $data;
}

?>
