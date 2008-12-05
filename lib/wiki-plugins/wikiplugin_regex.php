<?php
// Performs a regular expression on the $data between the {REGEX()} $data {REGEX} tags 
// Usage the WikiPageName holds the regex find replace commands 
// for example /^i/i:: $1 is the first letter
// where $1 is the value of the first expression
// example two: /(it's|its)(your|you're)/:: Check $1 $2 is correct
// other syntax example : s/^i/$1/i
// {REGEX(search=>WikiPageName)}
// line 2
// line 3{REGEX}

function wikiplugin_regex_help() {
return tra("Takes regex expressions and parses the content between the REGEX tags and replaces the text.").":
<br />~np~{REGEX(pageName=>(WikiPageWithRegexCommands)}".tra("data")."{REGEX}~/np~ - ''".tra("one data per line")."''";
}

function wikiplugin_regex_info() {
	return array(
		'name' => tra('Regular Expression'),
		'description' => tra('Takes regex expressions and parses the content between REGEX tags and replaces the text.'),
		'prefs' => array( 'wikiplugin_regexp' ),
		'body' => tra('one data per line'),
		'params' => array(
			'pageName' => array(
				'required' => true,
				'name' => tra('Page Name'),
				'description' => tra('Page name containing the regular expression.'),
			),
		),
	);
}

function wikiplugin_regex($data, $params) {
	global $tikilib;
	if ( ! isset($params['pageName'] ) return $data;

	$search = array();
	$replace = array();
	$info = $tikilib->get_page_info($params['pageName']);
	$content = $info['data']; 
	$lines = explode("\n", $content); // separate lines into array no emtpy lines at beginning mid or end
	foreach ( $lines as $line ) {
		if ( ( $strlen = strlen($line) ) > 2 && $line[0] == 's' && $line[1] == '/' ) {
			// Support 's/search/replace/modifiers' syntax
			$p = 0;
			$parts = array();
			for ( $i = 2 ; $i < $strlen ; $i++ ) {
				if ( $line[$i] == '/' && ( $line[$i-1] != '/' || ( $line[$i-2] == '\\' && $line[$i-3] ) ) {
					$p++;
					continue;
				}
				$parts[$p] .= $line[$i];
				$last = $line[$i];
			}
		} else {
			// Support '/search/modifiers::replace' syntax
			list($search[], $replace[]) = explode('::', $line); // use two colons to separate your find and replace
		}
	}

	return trim(preg_replace($search, $replace, $data));
}
?>
