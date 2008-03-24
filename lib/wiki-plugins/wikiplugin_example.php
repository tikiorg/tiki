<?php
/* Tiki-Wiki plugin example 
 *
 * This is an example plugin to let you know how to create
 * a plugin. Plugins are called using the syntax
 * {NAME(params)}content{NAME}
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
function wikiplugin_example_help() {
	return tra("Example").":<br />~np~{EXAMPLE(face=> size=>)}".tra("text")."{EXAMPLE}~/np~";
}
function wikiplugin_example($data, $params) {
	extract ($params,EXTR_SKIP);

	if (!isset($face)) {
		return ("<b>missing face parameter for plugin</b><br />");
	}

	if (!isset($size)) {
		return ("<b>missing size parameter for plugin</b><br />");
	}

	$ret = "<span style='font-face: $face; font-size: $size'>$data</span>";
	return $ret;
}

?>
