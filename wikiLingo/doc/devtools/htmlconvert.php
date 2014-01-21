<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once('tiki-setup.php');

// Make sure script is run from a shell
if (PHP_SAPI !== 'cli') {
	die("Please run from a shell");
}


// Show a description of this tool
echo "\nhtmlconvert: test tool for Jison parser\n";
echo "---------------------------------------\n";

// Interpret command line

// Display help if no arguments are passed
if (count($argv) <= 1) {
	$argv[] = '-?';
}

// Help
if (in_array('-?', $argv)) {
	echo "Command line arguments\n";
	echo "  -?	Show this help text\n";
	echo "  -f	Generate a full report\n";
	echo "  -x	Exclude the page contents from the report\n";
	echo "\n";
	die;
}

// Hide content reporting
$xcludeContent = false;
if (in_array('-x', $argv)) {
	echo "Hiding reported content.\n";
	$xcludeContent = true;
}

// Full content reporting
if (in_array('-f', $argv)) {
	echo "Full report.\n";
	$xcludeContent = false;
}


// The test function
///////////////////////////
function tf($page, &$startTime, &$endTime)
{
	global $xcludeContent;

	//The new parser strips all \r and lets \n do all the line break work
	$syntax = $page['data'];
	$syntax = str_replace("\r", '', $syntax);

	// Determine the page syntax type
	$is_html = isset($page['is_html']) ? $page['is_html'] == 1 : false;

	$parser = new JisonParser_Wiki_Handler();
	$WysiwygParser = new JisonParser_WikiCKEditor_Handler();
	$parserHtmlToWiki = new JisonParser_Html_Handler();

	// Parse
	$startTime = getMicroTime();
	if (!$is_html) {
		$wikiSyntax = $syntax;
		$html = $WysiwygParser->parse($wikiSyntax);
		$wiki = $parserHtmlToWiki->parse($html);
	} else {
		$htmlSyntax = $syntax;
		$wikiSyntax = $parserHtmlToWiki->parse($htmlSyntax);
		$html = $WysiwygParser->parse($wikiSyntax);
		$wiki = $parserHtmlToWiki->parse($html);
	}
	$endTime = getMicroTime();

	$success =  $wikiSyntax == $wiki;

	if ($success == false && !$xcludeContent) {
		echo "\n";
		echo '"' . $wikiSyntax . '"';
		echo "\n---------------------" . mb_detect_encoding($wikiSyntax) . "-------------------------\n";
		echo $html;
		echo "\n----------------------------------------------\n";
		echo '"' . $wiki . '"';
		echo "\n----------------------" . mb_detect_encoding($wiki) . "------------------------\n";
	}
	echo ($success  ? "\tSUCCESS" : "\tFAILURE");

	unset($parser);
	unset($WysiwygParser);
	unset($parserHtmlToWiki);
	unset($html);

	return $success;
}

// Run the process
/////////////////////
global $tikilib;

echo "Loading ALL wiki pages from the database...\n";
$pages = $tikilib->fetchAll("SELECT pageName, data, is_html from tiki_pages");
$pageCount = count($pages);

echo "Analyzing wiki pages...\n";
$cntSUCCESS = 0;
$cntFAILURE = 0;
$totalElapsedTime = 0;
$idx = 0;
foreach ($pages as &$page) {

	// Processing preparation
	$idx++;
	echo "Processing (".$idx."/".$pageCount.") - ".$page['pageName']." - ";

	// Process the page
	if (tf($page, $startTime, $endTime)) {
		++$cntSUCCESS;
	} else {
		++$cntFAILURE;
	}
	unset($page);

	// page_Parser statistics
	$totalTime = $endTime - $startTime;
	$totalElapsedTime += $totalTime;
	echo " - ".(int)$totalTime." ms\n";

}
echo "\n------------- Statistics ---------------------------------\n";

// Success / Failure
echo "\nSUCCESS: ".$cntSUCCESS."\n";
echo "FAILURE: ".$cntFAILURE."\n";

// Time spent
$avgTimePerPage = "N/A";
if ($idx > 0) {
	$avgTimePerPage = $totalElapsedTime / $idx;
}
echo "\nTotal elapsed msec: ".$totalElapsedTime."\n";
echo "Avg msec/page: ".$avgTimePerPage."\n";


// Support functions
///////////////////////

// @return current micro time in milli-seconds
function getMicroTime()
{
	$mtime = microtime();
	$mtime = explode(' ', $mtime);
	$mtime = $mtime[1] + $mtime[0];
	return $mtime * 1000;	// Convert to milliseconds
}
