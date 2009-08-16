<?php

// Centers the plugin content in the wiki page
// Usage
// {CENTER()}
//  data
// {CENTER}
function wikiplugin_center_help() {
	return tra("Centers the plugin content in the wiki page").":<br />~np~{CENTER()}".tra("text")."{CENTER}~/np~";
}

function wikiplugin_center_info() {
	return array(
		'name' => tra('Center'),
		'documentation' => 'PluginCenter',
		'description' => tra("Centers the plugin content in the wiki page"),
		'prefs' => array('wikiplugin_center'),
		'body' => tra('text'),
		'icon' => 'pics/icons/text_align_center.png',
		'params' => array(
		),
	);
}

function wikiplugin_center($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);
	$data = '<div align="center">' . trim($data). '</div>';
	return $data;
}
