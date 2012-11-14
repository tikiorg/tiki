<?php

require_once('tiki-setup.php');

function tf($wikiSyntax) {
	//echo str_replace("\n", '"\n"', str_replace("\r", '"\r"', $wikiSyntax));
	//die;
	$parser = new JisonParser_Wiki_Handler();
	$WysiwygParser = new JisonParser_WikiCKEditor_Handler();
	$parserHtmlToWiki = new JisonParser_Html_Handler();
	$html = $WysiwygParser->parse($wikiSyntax);

	echo '"' . $wikiSyntax . '"';
	echo "\n---------------------" . mb_detect_encoding($wikiSyntax) . "-------------------------\n";
	echo $html;
	echo "\n----------------------------------------------\n";
	$wiki = $parserHtmlToWiki->parse($html);
	echo '"' . $wiki . '"';
	echo "\n----------------------" . mb_detect_encoding($wiki) . "------------------------\n";
	echo ($wikiSyntax == $wiki ? "SUCCESS" : "FAILURE");

	unset($parser);
	unset($WysiwygParser);
	unset($parserHtmlToWiki);
	unset($html);
}

global $tikilib;

$pages = $tikilib->fetchAll("SELECT pageName, data from tiki_pages");
foreach($pages as &$page) {
	tf($page['data']);
	unset($page);
}