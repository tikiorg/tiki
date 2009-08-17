<?php

// Sorts the surrounded lines
// Usage
// {SORT(sort=>[asc|desc|reverse|shuffle])}line 1	# defaults to "asc"
// line 2
// line 3
// line 4{SORT}
function wikiplugin_sort_help() {
	return tra("Sorts the plugin content in the wiki page").":<br />~np~{SORT(sort=>asc|desc|reverse|shuffle)}".tra("data")."{SORT}~/np~ - ''".tra("one data per line")."''";
}

function wikiplugin_sort_info() {
	return array(
		'name' => tra('Sort'),
		'documentation' => 'PluginSort',
		'description' => tra('Sorts the plugin content in the wiki page'),
		'prefs' => array( 'wikiplugin_sort' ),
		'body' => tra('Data to sort, one entry per line.'),
		'filter' => 'text',
		'params' => array(
			'sort' => array(
				'required' => false,
				'name' => tra('Order'),
				'description' => tra('asc|desc|shuffle'),
				'filter' => 'alpha',
			),
		),
	);
}

function wikiplugin_sort($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);

	$sort = (isset($sort)) ? $sort : "asc";

	$lines = preg_split("/\n+/", $data, -1, PREG_SPLIT_NO_EMPTY); // separate lines into array
	// $lines = array_filter( $lines, "chop" ); // remove \n  
	srand ((float)microtime() * 1000000); // needed for shuffle;

	if ($sort == "asc") {
		sort ($lines);
	} else if ($sort == "desc") {
		rsort ($lines);
	} else if ($sort == "reverse") {
		$lines = array_reverse($lines);
	} else if ($sort == "shuffle") {
		shuffle ($lines);
	}

	reset ($lines);

	if (is_array($lines)) {
		$data = implode("\n", $lines);
	}

	$data = trim($data);
	return $data;
}
