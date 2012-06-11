<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20120429_fix_collation_tiki($installer)
{
	global $dbs_tiki;
	$installer->query("ALTER DATABASE `" . $dbs_tiki . "` CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'");
	if ( $results= $installer->fetchAll('SELECT DISTINCT(TABLE_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ?', $dbs_tiki)
	) {
		foreach ( $results as $table ) 
			$installer->query("ALTER TABLE ".$table['TABLE_NAME']." convert to character set DEFAULT COLLATE DEFAULT");
	} else {
		die('MySQL INFORMATION_SCHEMA not available. Your MySQL version is too old to perform this operation. (upgrade_20120429_fix_collation_tiki)');
	}
}
