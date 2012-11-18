<?php

require_once('tiki-setup.php');

function tf($wikiSyntax, &$startTime, &$endTime) {
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

	if($success == false) {
		echo "\n";
		echo '"' . $wikiSyntax . '"';
		echo "\n---------------------" . mb_detect_encoding($wikiSyntax) . "-------------------------\n";
		echo $html;
		echo "\n----------------------------------------------\n";
		// $wiki = $parserHtmlToWiki->parse($html);
		echo '"' . $wiki . '"';
		echo "\n----------------------" . mb_detect_encoding($wiki) . "------------------------\n";
	}
	echo ($success  ? "SUCCESS" : "FAILURE");

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
	echo " (Elapsed msec: ".$totalTime.")\n";
}
echo "\n------------- Statistics ---------------------------------\n";

// Success / Failure
echo "\nSUCCESS: ".$cntSUCCESS."\n";
echo "FAILURE: ".$cntFAILURE."\n";

// Time spent
$avgTimePerPage = $totalElapsedTime / $pageCount;
echo "\nTotal elapsed msec: ".$totalElapsedTime;
echo "Avg msec/page: ".$totalElapsedTime."\n";


// @return current micro time in milli-seconds
function getMicroTime()
{
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	return $mtime * 1000;	// Convert to milliseconds
}