<?php
// wikiplugin_html v1.0
//
// Include literal HTML in a Wiki page
// Jeremy Lee  2009-02-16


function wikiplugin_html_help() {
	return tra("Include literal HTML").":<br />~np~{HTML(wiki=0|1)}".tra("code")."{HTML}~/np~";
}

function wikiplugin_html_info() {
	return array(
		'name' => tra('HTML'),
		'documentation' => 'PluginHTML',
		'description' => tra('Include literal HTML in a Wiki page'),
		'prefs' => array('wikiplugin_html'),
		'body' => tra('HTML code'),
		'validate' => 'all',
		'filter' => 'rawhtml_unsafe',
		'params' => array(
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki syntax'),
				'description' => tra('0|1, parse wiki syntax within the html code.'),
			),
		),
	);
}

function wikiplugin_html($data, $params) {
	// extract parameters
	extract ($params,EXTR_SKIP);
	// parse the report definition
	$parse_fix = ($_REQUEST['preview']) && ($_SESSION['s_prefs']['tiki_release']=='2.2');
	if($parse_fix) {
		$html =& $data;
	} else {
		$html  =& html_entity_decode($data);
	}
	if(!$wiki) $ret .= '~np~';
	$ret .= $html;
	if(!$wiki) $ret .= '~/np~';
	// return the result
	return $ret;
}

?>
