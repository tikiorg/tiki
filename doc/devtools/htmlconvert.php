<?php

require_once('tiki-setup.php');

function tf($wikiSyntax) {
	//The new parser strips all \r and lets \n do all the line break work
	$wikiSyntax = str_replace("\r", '', $wikiSyntax);

	$parser = new JisonParser_Wiki_Handler();
	$WysiwygParser = new JisonParser_WikiCKEditor_Handler();
	$parserHtmlToWiki = new JisonParser_Html_Handler();
	$html = $WysiwygParser->parse($wikiSyntax);

	$wiki = $parserHtmlToWiki->parse($html);
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
	echo ($success  ? "SUCCESS" : "FAILURE")."\n";

	unset($parser);
	unset($WysiwygParser);
	unset($parserHtmlToWiki);
	unset($html);
	
	return $success;
}

global $tikilib;

$cntSUCCESS = 0;
$cntFAILURE = 0;
$pages = $tikilib->fetchAll("SELECT pageName, data from tiki_pages");
foreach($pages as &$page) {
	echo "Processing ".$page['pageName']." ";
	if (tf($page['data'])) {
		++$cntSUCCESS;
	} else {
		++$cntFAILURE;
	}
	unset($page);
}
echo "\n------------- Statistics ---------------------------------\n";
echo "\nSUCCESS: ".$cntSUCCESS."\n";
echo "\nFAILURE: ".$cntFAILURE."\n";
