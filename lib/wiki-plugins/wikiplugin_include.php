<?php

// Includes a wiki page in another
// Usage:
// {INCLUDE(page=>name)}{INCLUDE}
function wikiplugin_include_help() {
	return tra("Include a page").":<br />~np~{INCLUDE(page=>)}{INCLUDE}~/np~";
}
function wikiplugin_include($data, $params) {
	global $tikilib;

	extract ($params);

	if (!isset($page)) {
		return ("<b>missing page for plugin INCLUDE</b><br/>");
	}

	$data = $tikilib->get_page_info($page);
	return $data['data'];
}

?>