<?php
// $Header: /cvsroot/tikiwiki/tiki/import/mwxml2tikixml.php,v 1.1 2008-01-05 20:22:32 evanprodromou Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// USAGE: phpmwxml2tikixml.php INPUT OUTPUT

require_once('Text/Wiki.php');
require_once('Text/Wiki/Mediawiki.php');
require_once('Text/Wiki/Tiki.php');

function convertMediaWikiFile($input, $output) {
	$mw = simplexml_load_file($input);
	
	if ($mw === false) {
		// FIXME: Signal error
		print("ERROR: can't load input file '$input'; quitting.\n");
		exit(1);
	}

	$tw = new Text_Wiki_Mediawiki();
	
	foreach ($mw->page as $page) {
		foreach ($page->revision as $revision) {
			$t = htmlspecialchars($revision->text);
			$revision->text = $tw->transform($t, 'Tiki');
		}
	}

	$mw->asXML($output);
	
	if ($result === false) {
		// FIXME: Signal error
		print("ERROR: can't write output file '$output'; quitting.\n");
		exit(1);
	}
}

function printUsage() {
	print "USAGE: phpmwxml2tikixml.php INPUT OUTPUT\n";
}

if (count($argv) != 3) {
	printUsage();
} else {
	convertMediaWikiFile($argv[1], $argv[2]);
}

?>