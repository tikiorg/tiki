<?php
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

function wikiplugin_regex($data, $params) {
global $tikilib;

extract ($params,EXTR_SKIP);
$pageName = (isset($pageName)) ? $pageName : "pageName";//gets a page
$info = $tikilib->get_page_info($pageName);
$content=$info["data"]; 
$lines = explode("\n", $content); // separate lines into array no emtpy lines at beginning mid or end
foreach($lines as $line){
list($search[],$replace[])=explode("::",$line);// use two colons to separate your find and replace
}

$data=preg_replace($search,$replace,$data);
	$data = trim($data);
	return $data;
}
?>
