<?php
require_once('tiki-setup.php');

// Make sure script is run from a shell
if (PHP_SAPI !== 'cli') {
	die("Please run for a shell");
}

// Display command line options
echo "Command line arguments\n";
echo "  -x	eXclude the page contents from the dump\n";
echo "\n";


// Interpret command line

// Help
if (in_array('-?', $argv)) {
	die;
}

// Hide content reporting
$xcludeContent = false;
if (in_array('-x', $argv)) {
	echo "Hiding reported content.\n";
	$xcludeContent = true;
}

// The test function
function tf($wikiSyntax, &$startTime, &$endTime) {
	global $xcludeContent;
	//The new parser strips all \r and lets \n do all the line break work
	$wikiSyntax = str_replace("\r", '', $wikiSyntax);

	$parser = new JisonParser_Wiki_Handler();
	$WysiwygParser = new JisonParser_WikiCKEditor_Handler();
	$parserHtmlToWiki = new JisonParser_Html_Handler();

	// Parse
	$startTime = getMicroTime();
	$html = $WysiwygParser->parse($wikiSyntax);
	$wiki = $parserHtmlToWiki->parse($html);
	$endTime = getMicroTime();

	$success =  $wikiSyntax == $wiki;
	
	if($success == false && !$xcludeContent) {
		echo "\n";
		echo "xcludeContent = ".$xcludeContent."\n";
		echo '"' . $wikiSyntax . '"';
		echo "\n---------------------" . mb_detect_encoding($wikiSyntax) . "-------------------------\n";
		echo $html;
		echo "\n----------------------------------------------\n";
		// $wiki = $parserHtmlToWiki->parse($html);
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

global $tikilib;

$pages = $tikilib->fetchAll("SELECT pageName, data from tiki_pages");
$pageCount = count($pages);

$cntSUCCESS = 0;
$cntFAILURE = 0;
$totalElapsedTime = 0;
$idx = 0;
foreach($pages as &$page) {
	
	// Processing preparation
	$idx++;
	echo "Processing (".$idx."/".$pageCount.") - ".$page['pageName']." - ";
	
	// Process the page
	if (tf($page['data'], $startTime, $endTime)) {
		++$cntSUCCESS;
	} else {
		++$cntFAILURE;
	}
	unset($page);

	// page_Parser statistics	
	$totalTime = $endTime - $startTime;
	$totalElapsedTime += $totalTime;
	echo " msec: ".$totalTime."\n";

}
echo "\n------------- Statistics ---------------------------------\n";

// Success / Failure
echo "\nSUCCESS: ".$cntSUCCESS."\n";
echo "FAILURE: ".$cntFAILURE."\n";

// Time spent
$avgTimePerPage = "N/A";
if($idx > 0) {
	$avgTimePerPage = $totalElapsedTime / $idx;
}
echo "\nTotal elapsed msec: ".$totalElapsedTime."\n";
echo "Avg msec/page: ".$avgTimePerPage."\n";


// @return current micro time in milli-seconds
function getMicroTime()
{
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	return $mtime * 1000;	// Convert to milliseconds
}