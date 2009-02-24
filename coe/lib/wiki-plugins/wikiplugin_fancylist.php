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
	$lines = split("\n", $data);

	foreach ($lines as $line) {
		$parts = explode(")", $line);

		if (isset($parts[0]) && isset($parts[1])) {
			$result .= '<li><p>' . $parts[1] . '</p></li>';
		}
	}

	$result .= '</ol>';
	return $result;
}

?>