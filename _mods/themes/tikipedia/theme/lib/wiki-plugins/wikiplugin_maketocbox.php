<?php
/* Tiki-Wiki plugin example
 *
 * This is an example plugin to let you know how to create
 * a plugin. Plugins are called using the syntax
 * {Tikipediacontents(params)}content{NAME}
 * Name must be in uppercase!
 * params is in the form: name=>value,name2=>value2 (don't use quotes!)
 * If the plugin doesn't use params use {NAME()}content{NAME}
 *
 * The function will receive the plugin content in $data and the params
 * in the asociative array $params (using extract to pull the arguments
 * as in the example is a good practice)
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_maketocbox_help() {
	return tra("Insert a Mediawiki/Wikipedia-type contents link box on a wiki page, using maketoc.").":<br />~np~{MAKETOCBOX(float=>[left|right])}"."{MAKETOCBOX}~/np~";
}

function wikiplugin_maketocbox($data, $params) {
	extract ($params,EXTR_SKIP);

	$fl   = (isset($float) && ($float == 'left' || $float == "right")) ? " float: $float;"  : " float: none;";


$ret = "<div class=\"maketocbox\" style=\"$fl\"><div><span id=\"maketocbox-title\">Contents</span>&nbsp;<a href=\"javascript:icntoggle('maketocbox-contents');\" />[[-/+]</a></div><div id=\"maketocbox-contents\">{maketoc}</div></div>";

return $ret;

}

?>
