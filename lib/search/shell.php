<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if ( isset($_SERVER['REQUEST_METHOD']) ) die;

if ( ! isset( $_SERVER['argc'] ) || $_SERVER['argc'] === 1 )
	die( 'Usage: php lib/search/shell.php command [option]
	Where command [option] can be:
		rebuild [log]
		process [integer (default 10)]
		optimize
	N.B. Needs to be run as the "apache" user, e.g. > "sudo -u www-data php lib/search/shell.php process 20"
' );

if ( ! file_exists('db/local.php') )
	die( "Tiki is not installed yet.\n" );


require_once('tiki-setup.php');

echo "Running search shell utility for: $local_php\n";

global $unifiedsearchlib;
require_once 'lib/search/searchlib-unified.php';

if ( $_SERVER['argc'] >= 2 && $_SERVER['argv'][1] === 'process' ) {

	$queueCount = $unifiedsearchlib->getQueueCount();

	if ($queueCount > 0) {

		$toProcess = (isset($_SERVER['argv'][2]) && is_numeric($_SERVER['argv'][2])) ? $_SERVER['argv'][2] : 10;
		$toProcess = min($queueCount, $toProcess);

		try {
			echo 'Processing queue...';
			ob_flush();
			$unifiedsearchlib->processUpdateQueue($toProcess);
			echo "done\n";

		} catch (Zend_Search_Lucene_Exception $e) {

			$errlib = TikiLib::lib('errorreport');
			$errlib->report(tr('Search index could not be updated: %0', $e->getMessage()));
		}

		$msgs = TikiLib::lib('errorreport')->get_errors();
		if (count($msgs)) {
			echo "Problem processing $toProcess items.\n";
			echo implode("\n", str_replace('<br />', "\n", $msgs));
		} else {
			echo "Processed $toProcess items, {$unifiedsearchlib->getQueueCount()} remaining.\n";
		}
	}

} else if ( $_SERVER['argc'] >= 2 && $_SERVER['argv'][1] === 'rebuild' ) {

	$loggit = (isset($_SERVER['argv'][2]) && $_SERVER['argv'][2] === 'log');

	echo 'Rebuilding Index...';
	ob_flush();
	$unifiedsearchlib->rebuild($loggit);
	echo "done\n";

} else if ( $_SERVER['argc'] === 2 && $_SERVER['argv'][1] === 'optimize' ) {

	echo 'Optimizing Index...';
	ob_flush();
	$stat = $unifiedsearchlib->getIndex()->optimize();
	echo "done\n";

}
