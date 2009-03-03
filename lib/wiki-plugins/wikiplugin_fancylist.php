<?php

function wikiplugin_fancylist_help() {
	return tra("Creates a fancy looking list").": ~np~{FANCYLIST()}".tra("num").")".tra("item text")."{FANCYLIST}~/np~ - ''".tra("one item per line")."''";
}

function wikiplugin_fancylist_info() {
	return array(
		'name' => tra('Fancy List'),
		'description' => tra("Creates a fancy looking list"),
		'prefs' => array('wikiplugin_fancylist'),
		'body' => tra('One item per line starting with anything followed by ")".'),
		'params' => array(
		),
	);
}

function wikiplugin_fancylist($data, $params) {
	global $tikilib;

	global $replacement;
	if (isset($param))
		extract ($params,EXTR_SKIP);
	$result = '<ol class="fancylist">';
	// split data by lines (trimed whitespace from start and end)
	$lines = split("\n", trim($data));

	foreach ($lines as $line) {
		// replace all before and including the ")"
		$part = preg_replace("/[\w]+\)(.*)/", "$1", $line);
		$result .= '<li><p>' . $part . '</p></li>';
	}

	$result .= '</ol>';
	return $result;
}

?>