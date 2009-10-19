<?php
/*
 * LM plugin. Marker for left-to-right text.
 * 
 * Syntax:
 * 
 *  {LM()}
 *   some content
 *  {LM}
 * 
 */
function wikiplugin_lm_help() {
	return tra("Marker for left-to-right text:")."<br />~np~{LM()}".tra("text")."{LM}~/np~";
}

function wikiplugin_lm_info() {
	return array(
		'name' => tra('Lm'),
		'documentation' => 'PluginLm',
		'description' => tra("Marker for left-to-right text"),
		'prefs' => array('wikiplugin_lm'),
		'body' => tra('text')
	);
}

function wikiplugin_lm($data, $params) {
	return '&lrm;' . $data;
}
