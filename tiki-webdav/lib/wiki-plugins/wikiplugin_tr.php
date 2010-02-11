<?php
// $Id$

function wikiplugin_tr_help() {
	$help = tra("Translate a string");
	$help .= "~np~{TR()}string{TR}~/np~";
	return $help;
}

function wikiplugin_tr_info() {
	return array(
		'name' => tra('Translate'),
		'documentation' => 'PluginTR',
		'description' => tra('Translate a string using Tikiwiki translation table.'),
		'prefs' => array( 'wikiplugin_tr' ),
		'body' => tra('string'),
		'params' => array(
		),
	);
}

function wikiplugin_tr($data) {
	return tra($data);
}
