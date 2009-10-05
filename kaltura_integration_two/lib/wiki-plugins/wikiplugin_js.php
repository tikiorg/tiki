<?php
/* $Id$
 * file = external javascript file
 * data is the javascript code
 * if you need the data to be interpreted before the file use the JS plugin 2 times
 */
function wikiplugin_js_help() {
	return tra("Insert a javascript file or/and some javascript code.")."<br />~np~{JS(file=file.js)}".tra("javascript code")."{JS}~/np~";
}

function wikiplugin_js_info() {
		return array(
			'name' => tra('Javascript'),
			'documentation' => 'PluginJS',
			'description' => tra('Insert a javascript file or/and some javascript code.'),
			'prefs' => array( 'wikiplugin_js' ),
			'body' => tra('javascript code'),
			'validate' => 'all',
			'filter' => 'rawhtml_unsafe',
			'params' => array(
				'file' => array(
					'required' => false,
					'name' => tra('File'),
					'description' => tra('Javascript filename.'),
					'filter' => 'url',
				),
			),
		);
	}
function wikiplugin_js($data, $params) {
	extract($params, EXTR_SKIP);
	if(isset($file)) {
		$ret =  "~np~<script type=\"text/javascript\" src=\"$file\"></script> ~/np~";
	} else {
		$ret = '';
	}
	if ($data) {
		$ret .= "~np~<script type=\"text/javascript\">".$data."</script>~/np~"; 
	}
	return $ret;
}
