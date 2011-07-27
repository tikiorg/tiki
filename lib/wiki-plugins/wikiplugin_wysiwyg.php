<?php

function wikiplugin_wysiwyg_info() {
	return array(
		'name' => 'WYSIWYG',
		//'documentation' => 'PluginWYSIWYG',
		'description' => tra('Experimental: Purify the HTML content.'),
		'format' => 'wiki',
		'prefs' => array('wikiplugin_wysiwyg'),
		'params' => array(),
		'filter' => 'purifier',
		'body' => tra('Content'),
	);
} // wikiplugin_wysiwyg_info()


function wikiplugin_wysiwyg($data, $params) {
	return $data;
} // wikiplugin_wysiwyg()

