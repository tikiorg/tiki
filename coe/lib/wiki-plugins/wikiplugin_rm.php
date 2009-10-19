<?php
/*
 * RM plugin. Marker for right-to-left text.
 * 
 * Syntax:
 * 
 *  {RM()}
 *   some content
 *  {RM}
 * 
 */
function wikiplugin_rm_help() {
	return tra("Marker for right-to-left text:")."<br />~np~{RM()}".tra("text")."{RM}~/np~";
}

function wikiplugin_rm_info() {
	return array(
		'name' => tra('Rm'),
		'documentation' => 'PluginRm',
		'description' => tra("Marker for right-to-left text"),
		'prefs' => array('wikiplugin_rm'),
		'body' => tra('text')
	);
}

function wikiplugin_rm($data, $params) {
	return '&rlm;' . $data;
}
