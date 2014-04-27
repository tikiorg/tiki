<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @param $installer
 */
function upgrade_20120429_fix_collation_tiki($installer)
{
	global $dbs_tiki;
	$installer->query("ALTER DATABASE `" . $dbs_tiki . "` CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'");
	$results= $installer->query('SHOW TABLES') ;
	while ( $table = $results->fetch_row() ) {
		$installer->query("ALTER TABLE ".$table[0]." convert to character set DEFAULT COLLATE DEFAULT");
	}
}
