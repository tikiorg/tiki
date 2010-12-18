<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Performs a regular expression on the $data between the {REGEX()} $data {REGEX} tags 
// Usage the WikiPageName holds the regex find replace commands 
// for example /^i/i:: $1 is the first letter
// where $1 is the value of the first expression
// example two: /(it's|its)(your|you're):: Check $1 $2 is correct
// {REGEX(search=>WikiPageName)}
// line 2
// line 3{REGEX}

function wikiplugin_regex_help() {
return tra("Takes regex expressions and parses the content between the REGEX tags and replaces the text.").":
<br />~np~{REGEX(search=>(WikiPageWithRegexCommands)}".tra("data")."{REGEX}~/np~ - ''".tra("one data per line")."''";
}

function wikiplugin_regex_info() {
	return array(
		'name' => tra('Regular Expression'),
		'documentation' => 'PluginRegex',
		'validate' => 'all',
		'description' => tra('Perform a regular expression search and replace'),
		'prefs' => array( 'wikiplugin_regex' ),
		'body' => tra('Each line of content is evaluated separately'),
		'params' => array(
			'pageName' => array(
				'required' => true,
				'name' => tra('Page name'),
				'description' => tra('Name of page containing search and replace expressions separated by two colons. Example of syntax on that page: /search pattern/::replacement text'),
				'default' => 'pageName',
			),
		),
	);
}

function wikiplugin_regex($data, $params) {
global $tikilib;

extract ($params,EXTR_SKIP);
$pageName = (isset($pageName)) ? $pageName : 'pageName';//gets a page
$info = $tikilib->get_page_info($pageName);
$content=$info['data']; 
$lines = explode("\n", $content); // separate lines into array no emtpy lines at beginning mid or end
$i = 0;
foreach($lines as $line){
list($pattern[$i],$replace[$i])=explode("::",$line);// use two colons to separate your find and replace
$i++;
}

$data=preg_replace($pattern,$replace,$data);
	$data = trim($data);
	return $data;
}
