<?php

// Centers the plugin content in the wiki page
// Usage
// {CENTER()}
//  data
// {CENTER}
function wikiplugin_center_help() {
	return tra("Centers the plugin content in the wiki page").":<br />~np~{CENTER()}".tra("text")."{CENTER}~/np~";
}

function wikiplugin_center($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);
	$data = '<div align="center">' . trim($data). '</div>';
	return $data;
}

?>
